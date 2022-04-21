<?php

namespace App\Events;

use App\Model\Entity\User;

class UserLogin
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
