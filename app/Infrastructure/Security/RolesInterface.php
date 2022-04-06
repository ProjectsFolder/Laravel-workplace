<?php

namespace App\Infrastructure\Security;

interface RolesInterface
{
    public function getRoles(): array;
    public function addRole(string $role);
    public function removeRole(string $role);
    public function hasRole(string $role): bool;
}
