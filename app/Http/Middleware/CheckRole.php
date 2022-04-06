<?php

namespace App\Http\Middleware;

use App\Infrastructure\Security\RoleTreeParser;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CheckRole
{
    protected $roleTreeParser;

    public function __construct(RoleTreeParser $roleTreeParser)
    {
        $this->roleTreeParser = $roleTreeParser;
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @param string $role
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function handle($request, Closure $next, string $role)
    {
        $roles = explode('|', $role);
        $userRoles = $this->roleTreeParser->getRolesByUser($request->user());
        if (count($roles) > 1) {
            $can = false;
            foreach ($roles as $r) {
                if (in_array($r, $userRoles)) {
                    $can = true;
                    break;
                }
            }
            if (!$can) {
                throw new AccessDeniedHttpException('This action is unauthorized.');
            }
        } else {
            if (!in_array($role, $userRoles)) {
                throw new AccessDeniedHttpException('This action is unauthorized.');
            }
        }

        return $next($request);
    }
}
