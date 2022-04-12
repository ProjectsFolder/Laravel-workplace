<?php

namespace App\Model\Repository;

use App\Http\Requests\UserCredentialsRequest;
use App\Model\Entity\User;

class UserRepository
{
    public function create(UserCredentialsRequest $data): User
    {
        $user = new User();
        $validated = $data->validated();
        $user->fill($validated);
        foreach ($validated['roles'] ?? [] as $role) {
            $user->addRole($role);
        }
        $user->save();

        return $user;
    }

    public function find(int $Id): ?User
    {
        /** @var User $user */
        $user = User::query()->find($Id);

        return $user;
    }

    public function findByName(string $name): ?User
    {
        /** @var User $user */
        $user = User::query()->where('name', $name)->first();

        return $user;
    }
}
