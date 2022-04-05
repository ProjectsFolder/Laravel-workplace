<?php

namespace App\Infrastructure;

use App\Utils\StringUtils;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;

class JsonGuard implements Guard
{
    protected $provider;
    protected $request;
    protected $user;

    public function __construct(UserProvider $userProvider, Request $request)
    {
        $this->provider = $userProvider;
        $this->request = $request;
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
        if (empty($credentials['email']) || empty($credentials['password'])) {
            $json = $this->request->get('jsondata', '');
            $credentials = StringUtils::isJson($json) ? json_decode($json, true) : null;
            if (empty($credentials)) {
                return false;
            }
        }

        $user = $this->provider->retrieveByCredentials($credentials);
        if (!empty($user) && $this->provider->validateCredentials($user, $credentials)) {
            $this->setUser($user);

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
