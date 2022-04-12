<?php

namespace App\Providers;

use App\Domain\Interfaces\Output\VatGetterInterface;
use App\Domain\Interfaces\Output\VatSaverInterface;
use App\Domain\VatSaver;
use App\External\Interfaces\RabbitClientInterface;
use App\External\Mq\RabbitClient;
use App\External\Soap\ViesClient;
use App\Infrastructure\Security\RoleTreeParser;
use App\Infrastructure\Serializer\ApiEncoder;
use App\Infrastructure\Serializer\ApiNormalizer;
use App\Model\Repository\VatRepository;
use App\Rules\Vat;
use App\Utils\Mapper\Mappers\VatDataToVatEntity;
use App\Utils\Mapper\TypeMapper;
use App\Utils\Paginator;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Factory;
use JsonSerializable;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class AppServiceProvider extends ServiceProvider
{
    protected $rules = [
        Vat::class,
    ];

    protected $mappers = [
        VatDataToVatEntity::class,
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
        $this->app->singleton(SerializerInterface::class, function () {
            return new Serializer([new ApiNormalizer()], [new ApiEncoder()]);
        });
        $this->app->singleton(RoleTreeParser::class, function () {
            return new RoleTreeParser(config('auth.roles', []));
        });
        $this->app->tag($this->mappers, ['type-mapper']);
        $this->app->singleton(TypeMapper::class, function () {
            return new TypeMapper($this->app->tagged('type-mapper'));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @param ResponseFactory $responseFactory
     * @param Factory $validatorFactory
     * @param SerializerInterface $serializer
     *
     * @return void
     */
    public function boot(
        ResponseFactory $responseFactory,
        Factory $validatorFactory,
        SerializerInterface $serializer
    ) {
        $this->registerResponseMacro($responseFactory, $serializer);
        $this->registerValidationRules($validatorFactory);
    }

    private function registerResponseMacro(ResponseFactory $responseFactory, SerializerInterface $serializer)
    {
        $responseFactory->macro('success', function ($data = null, $meta = [], $headers = []) use ($serializer) {
            if ($data instanceof JsonSerializable) {
                $data = $data->jsonSerialize();
            }
            $meta['success'] = true;
            $responseData = $serializer->serialize($data, 'api', $meta);
            $headers['Content-Type'] = 'application/json';

            return new Response($responseData, 200, $headers);
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
