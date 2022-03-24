<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request, JWTAuth $auth): Response
    {
        $input = $request->only(['email', 'password']);
        if (!$token = $auth->attempt($input)) {
            throw new HttpException(400, 'Login or password is incorrect');
        }

        return response()->success([
            'token' => $token,
            'expires_in' => $auth->factory()->getTTL() * 60,
        ]);
    }

    public function refresh(JWTAuth $auth): Response
    {
        $token = $auth->refresh();

        return response()->success([
            'token' => $token,
            'expires_in' => $auth->factory()->getTTL() * 60,
        ]);
    }
}
