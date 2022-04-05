<?php

namespace App\Providers;

use App\Infrastructure\JsonGuard;
use Illuminate\Auth\AuthManager;
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
        $auth->provider('external', function ($app, array $config) {
            return new ExternalUserProvider();
        });
        $auth->extend('json', function ($app, $name, array $config) use ($auth) {
            return new JsonGuard($auth->createUserProvider($config['provider']), $app->make('request'));
        });
    }
}
