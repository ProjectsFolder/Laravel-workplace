<?php

namespace App\Infrastructure\Security;

class RoleTreeParser
{
    protected $appRoles;

    public function __construct(array $appRoles)
    {
        $this->appRoles = $appRoles;
    }

    public function getRolesByUser(RolesInterface $user): array
    {
        $result = [];
        foreach ($user->getRoles() as $role) {
            $result = array_merge($result, $this->getInheritedRoles($role));
        }

        return array_values(array_unique($result));
    }

    public function getInheritedRoles(string $role): array
    {
        $result = [];
        foreach ($this->appRoles as $k => $v) {
            if ($role == $k) {
                if (is_array($this->appRoles[$k])) {
                    foreach ($this->appRoles[$k] as $childRole) {
                        $result[] = $childRole;
                        $result = array_merge($result, $this->getInheritedRoles($childRole));
                    }
                } else {
                    $result[] = $this->appRoles[$k];
                    $result = array_merge($result, $this->getInheritedRoles($this->appRoles[$k]));
                }
            }
        }
        $result[] = $role;

        return array_values(array_unique($result));
    }
}
