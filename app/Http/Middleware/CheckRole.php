<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CheckRole
{
    protected $gate;

    public function __construct(Gate $gate)
    {
        $this->gate = $gate;
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
        if (!$this->gate->allows('check-role', [$roles])) {
            throw new AccessDeniedHttpException('This action is unauthorized.');
        }

        return $next($request);
    }
}
