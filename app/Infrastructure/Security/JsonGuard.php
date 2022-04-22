<?php

namespace App\Infrastructure\Security;

use App\Model\Entity\User;
use App\Utils\StringUtils;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;

class JsonGuard implements Guard
{
    protected $provider;
    protected $request;
    protected $dispatcher;
    protected $user;
    protected $name = 'json';

    public function __construct(UserProvider $userProvider, Request $request, Dispatcher $dispatcher)
    {
        $this->provider = $userProvider;
        $this->request = $request;
        $this->dispatcher = $dispatcher;
    }

    public function check(): bool
    {
        return !empty($this->user);
    }

    public function guest(): bool
    {
        return !$this->check();
    }

    public function user(): ?Authenticatable
    {
        return $this->user;
    }

    public function id()
    {
        if ($this->check()) {
            return $this->user()->getAuthIdentifier();
        }

        return null;
    }

    public function validate(array $credentials = []): bool
    {
        if (empty($credentials['name']) || empty($credentials['password'])) {
            $json = $this->request->get('jsondata', '');
            $credentials = StringUtils::isJson($json) ? json_decode($json, true) : null;
            if (empty($credentials)) {
                return false;
            }
        }

        /** @var User $user */
        $user = $this->provider->retrieveByCredentials($credentials);
        if (!empty($user) && $this->provider->validateCredentials($user, $credentials)) {
            $this->setUser($user);
            $this->dispatcher->dispatch(new Login($this->name, $user, false));

            return true;
        } else {
            return false;
        }
    }

    public function setUser(Authenticatable $user)
    {
        $this->user = $user;
    }
}
