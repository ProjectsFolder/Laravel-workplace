<?php

namespace App\Infrastructure\Security;

class CheckRolePolicy
{
    protected $roleTreeParser;

    public function __construct(RoleTreeParser $roleTreeParser)
    {
        $this->roleTreeParser = $roleTreeParser;
    }

    public function check($user, $role): bool
    {
        $roleUsers = $this->roleTreeParser->getRolesByUser($user);
        if (is_array($role)) {
            $can = false;
            foreach ($role as $r) {
                if (in_array($r, $roleUsers)) {
                    $can = true;
                    break;
                }
            }
            if (!$can) {
                return false;
            }
        } else {
            if (!in_array($role, $roleUsers)) {
                return false;
            }
        }

        return true;
    }
}
