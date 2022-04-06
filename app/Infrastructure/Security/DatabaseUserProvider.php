<?php

namespace App\Infrastructure\Security;

use App\Model\Repository\UserRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Hashing\Hasher;

class DatabaseUserProvider implements UserProvider
{
    private $userRepository;
    private $hasher;

    public function __construct(UserRepository $userRepository, Hasher $hasher)
    {
        $this->userRepository = $userRepository;
        $this->hasher = $hasher;
    }

    public function retrieveById($identifier): ?Authenticatable
    {
        return $this->userRepository->find($identifier);
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
        if (!isset($credentials['name'])) {
            return null;
        }

        return $this->userRepository->findByName($credentials['name']);
    }

    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        if (!isset($credentials['password'])) {
            return false;
        }

        return $this->hasher->check($credentials['password'], $user->getAuthPassword());
    }
}
