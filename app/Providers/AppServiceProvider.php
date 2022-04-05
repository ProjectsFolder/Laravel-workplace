<?php

namespace App\Providers;

use App\Domain\Interfaces\Output\VatGetterInterface;
use App\Domain\Interfaces\Output\VatSaverInterface;
use App\Domain\VatSaver;
use App\External\Interfaces\RabbitClientInterface;
use App\External\Mq\RabbitClient;
use App\External\Soap\ViesClient;
use App\Model\Repository\VatRepository;
use App\Rules\Vat;
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
        $this->app->singleton(VatGetterInterface::class, function () {
            return new ViesClient(env('VIES_WSDL_URL'));
        });
        $this->app->singleton(VatSaverInterface::class, function () {
            return resolve(VatRepository::class);
        });
        $this->app->singleton(\App\Domain\Interfaces\Input\VatSaverInterface::class, function () {
            return new VatSaver(resolve(VatGetterInterface::class), resolve(VatSaverInterface::class));
        });
        $this->app->singleton(RabbitClientInterface::class, function () {
            return new RabbitClient(env('RABBIT_URL'));
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
        $responseFactory->macro('success', function ($data = null, $headers = []) {
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

            return new JsonResponse($result, 200, $headers);
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
