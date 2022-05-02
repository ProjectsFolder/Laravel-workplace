<?php

namespace App\Providers;

use App\Infrastructure\Security\DatabaseUserProvider;
use App\Infrastructure\Security\JsonGuard;
use App\Infrastructure\Security\Policies\PostPolicy;
use App\Model\Entity\Post;
use App\Model\Repository\UserRepository;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Auth\Access\Gate;
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
        Post::class => PostPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @param AuthManager $auth
     * @param Gate $gate
     *
     * @return void
     */
    public function boot(AuthManager $auth, Gate $gate)
    {
        $this->registerPolicies();
        $this->registerAuth($auth, $gate);
    }

    private function registerAuth(AuthManager $auth, Gate $gate)
    {
        $auth->provider('mysql', function ($app, array $config) {
            return new DatabaseUserProvider(resolve(UserRepository::class), resolve(Hasher::class));
        });
        $auth->extend('json', function ($app, $name, array $config) use ($auth) {
            return new JsonGuard($auth->createUserProvider($config['provider']), $app->make('request'), $app->make('events'));
        });
        $gate->define('check-role', 'App\Infrastructure\Security\CheckRolePolicy@check');
    }
}
