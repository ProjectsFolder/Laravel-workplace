<?php

namespace App\Infrastructure\Security;

trait WithRoles
{
    protected $rolesIdentifierName = 'roles';

    public function getRoles(): array
    {
        return $this->{$this->rolesIdentifierName};
    }

    public function addRole(string $role)
    {
        $this->{$this->rolesIdentifierName} = array_merge(
            $this->{$this->rolesIdentifierName},
            [$role]
        );
    }

    public function removeRole(string $role)
    {
        $roles = $this->{$this->rolesIdentifierName};
        foreach ($roles as $key => $r) {
            if ($r == $role) {
                unset($roles[$key]);
            }
        }
        $this->{$this->rolesIdentifierName} = $roles;
    }

    public function hasRole(string $role): bool
    {
        $roles = $this->{$this->rolesIdentifierName};

        return in_array($role, $roles);
    }
}
