<?php

namespace App\Providers;

use App\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

class ExternalUserProvider implements UserProvider
{
    public function retrieveById($identifier): ?Authenticatable
    {
        return new User([
            'id' => $identifier,
            'email' => $identifier,
        ]);
    }

    public function retrieveByToken($identifier, $token): ?Authenticatable
    {
        return null;
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
    }

    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        if (!isset($credentials['email'])) {
            return null;
        }

        return new User([
            'id' => $credentials['email'],
            'email' => $credentials['email'],
        ]);
    }

    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        if (!isset($credentials['password'])) {
            return false;
        }

        return
            'admin' == $user->getAuthIdentifier() &&
            'secret' == $credentials['password']
        ;
    }
}
