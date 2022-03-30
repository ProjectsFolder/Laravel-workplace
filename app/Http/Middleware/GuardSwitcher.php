<?php

namespace App\Http\Middleware;

use Closure;
use Exception;

class GuardSwitcher
{
    /**
     * @param $request
     * @param Closure $next
     * @param null $defaultGuard
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function handle($request, Closure $next, $defaultGuard = null)
    {
        if (in_array($defaultGuard, array_keys(config('auth.guards')))) {
            config(['auth.defaults.guard' => $defaultGuard]);

            return $next($request);
        }

        throw new Exception("Guard '$defaultGuard' not found");
    }
}
