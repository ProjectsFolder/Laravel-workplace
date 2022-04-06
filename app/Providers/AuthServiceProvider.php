<?php

namespace App\Providers;

use App\Infrastructure\Security\DatabaseUserProvider;
use App\Infrastructure\Security\JsonGuard;
use App\Model\Repository\UserRepository;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @param AuthManager $auth
     *
     * @return void
     */
    public function boot(AuthManager $auth)
    {
        $this->registerPolicies();
        $this->registerAuth($auth);
    }

    private function registerAuth(AuthManager $auth)
    {
        $auth->provider('mysql', function ($app, array $config) {
            return new DatabaseUserProvider(resolve(UserRepository::class), resolve(Hasher::class));
        });
        $auth->extend('json', function ($app, $name, array $config) use ($auth) {
            return new JsonGuard($auth->createUserProvider($config['provider']), $app->make('request'));
        });
    }
}
