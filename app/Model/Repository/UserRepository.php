<?php

namespace App\Model\Repository;

use App\Http\Requests\UserCredentialsRequest;
use App\Model\Entity\User;
use Illuminate\Contracts\Hashing\Hasher;

class UserRepository
{
    private $hasher;

    public function __construct(Hasher $hasher)
    {
        $this->hasher = $hasher;
    }

    public function create(UserCredentialsRequest $data): User
    {
        $user = new User();
        $validated = $data->validated();
        $user->fill($validated);
        $user->password = $this->hasher->make($user->password);
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
