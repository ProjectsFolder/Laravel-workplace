<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCredentialsRequest;
use App\Model\Entity\User;
use App\Model\Repository\UserRepository;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request, JWTAuth $auth): Response
    {
        $input = $request->only(['name', 'password']);
        if (!$token = $auth->attempt($input)) {
            throw new HttpException(400, 'Login or password is incorrect');
        }

        return response()->success([
            'token' => $token,
            'expires_in' => $auth->factory()->getTTL() * 60,
        ]);
    }

    public function customLogin(Guard $guard, JWTAuth $auth): Response
    {
        if ($guard->validate()) {
            /** @var User $user */
            $user = $guard->user();
            $token = $auth->fromUser($user);

            return response()->success([
                'token' => $token,
                'expires_in' => $auth->factory()->getTTL() * 60,
            ]);
        }

        throw new HttpException(400, 'Login or password is incorrect');
    }

    public function register(UserCredentialsRequest $request, UserRepository $userRepository): Response
    {
        $userRepository->create($request);

        return response()->success();
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
