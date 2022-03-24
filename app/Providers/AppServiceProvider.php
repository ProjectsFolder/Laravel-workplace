<?php

namespace App\Providers;

use App\Model\Interfaces\VatSaverInterface;
use App\Model\Repository\VatRepository;
use App\Rules\Vat;
use App\Service\Interfaces\VatGetterInterface;
use App\Service\VIES;
use App\Utils\Paginator;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Factory;
use JsonSerializable;

class AppServiceProvider extends ServiceProvider
{
    protected $rules = [
        Vat::class,
    ];

    public $bindings = [
        LengthAwarePaginator::class => Paginator::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $vies = new VIES(env('VIES_WSDL_URL'));
        $this->app->singleton(VatGetterInterface::class, function () use ($vies) {
            return $vies;
        });

        $vatRepository = new VatRepository();
        $this->app->singleton(VatSaverInterface::class, function () use ($vatRepository) {
            return $vatRepository;
        });
        $this->app->singleton(VatRepository::class, function () use ($vatRepository) {
            return $vatRepository;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @param ResponseFactory $responseFactory
     * @param Factory $validatorFactory
     *
     * @return void
     */
    public function boot(ResponseFactory $responseFactory, Factory $validatorFactory)
    {
        $this->registerResponseMacro($responseFactory);
        $this->registerValidationRules($validatorFactory);
    }

    private function registerResponseMacro(ResponseFactory $responseFactory)
    {
        $responseFactory->macro('success', function ($data = null) {
            $result = ['success' => true];
            if ($data instanceof JsonSerializable) {
                $data = $data->jsonSerialize();
            }
            $meta = [];
            if (isset($data['meta'])) {
                $meta = $data['meta'];
                unset($data['meta']);
            }
            $result['data'] = is_array($data) && 1 == count($data) ? array_shift($data) : $data;
            if (!empty($meta)) {
                $result['meta'] = $meta;
            }

            return new JsonResponse($result);
        });
    }

    private function registerValidationRules(Factory $validatorFactory)
    {
        foreach ($this->rules as $class) {
            /** @var Rule $rule */
            $rule = new $class();
            if (method_exists($rule, '__toString')) {
                $alias = (string) $rule->__toString();
                if ($alias) {
                    $validatorFactory->extend($alias, $class.'@passes', $rule->message());
                }
            }
        }
    }
}
