<?php

namespace App\Providers;

use App\Domain\Interfaces\Output\VatGetterInterface;
use App\Domain\Interfaces\Output\VatSaverInterface;
use App\Domain\VatSaver;
use App\External\Config\ApiConfig;
use App\External\Http\HttpClient;
use App\External\Http\SmsClient;
use App\External\Interfaces\HttpClientInterface;
use App\External\Interfaces\RabbitClientInterface;
use App\External\Interfaces\SmsClientInterface;
use App\External\Mq\RabbitClient;
use App\External\Soap\ViesClient;
use App\Infrastructure\Security\RoleTreeParser;
use App\Infrastructure\Serializer\ApiEncoder;
use App\Infrastructure\Serializer\ApiNormalizer;
use App\Infrastructure\Storage\FileStorage;
use App\Infrastructure\Storage\FileStorageInterface;
use App\Model\Repository\VatRepository;
use App\Infrastructure\Rule\Vat;
use App\Utils\Mapper\Mappers\VatDataToVatEntity;
use App\Utils\Mapper\TypeMapper;
use App\Utils\Paginator;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Factory;
use JsonSerializable;
use Symfony\Component\HttpFoundation\StreamedResponse;
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
        $this->app->singleton(HttpClientInterface::class, function () {
            return new HttpClient();
        });
        $this->app->singleton(SmsClientInterface::class, function () {
            return new SmsClient(
                (new ApiConfig())->setBaseUrl(env('SMS_URL'))->setApiKey(env('SMS_API_KEY')),
                resolve(HttpClientInterface::class)
            );
        });
        $this->app->singleton(FileStorageInterface::class, function () {
            return new FileStorage(Storage::disk('local'), Cache::store('redis'));
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

        $responseFactory->macro('attachment', function ($content, $filename, $headers = []) use ($serializer) {
            $headers = array_merge($headers, [
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ]);
            if (is_resource($content)) {
                return new StreamedResponse(function () use ($content) {
                    fpassthru($content);
                    if (is_resource($content)) {
                        fclose($content);
                    }
                }, 200, $headers);
            } else {
                return new Response($content, 200, $headers);
            }
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
