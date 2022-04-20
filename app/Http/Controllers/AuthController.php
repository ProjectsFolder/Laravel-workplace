<?php

namespace App\Http\Controllers;

use App\Events\UserLogin;
use App\Http\Requests\UserCredentialsRequest;
use App\Model\Entity\User;
use App\Model\Repository\UserRepository;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\JWTAuth;

class AuthController extends Controller
{
    /** @var Translator $translator */
    protected $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function login(Request $request, JWTAuth $auth, Dispatcher $dispatcher): Response
    {
        $input = $request->only(['name', 'password']);
        if (!$token = $auth->attempt($input)) {
            throw new HttpException(400, $this->translator->get('messages.auth_error.login'));
        }
        /** @var User $user */
        $user = $auth->user();
        $dispatcher->dispatch(new UserLogin("user {$user->name} is login"));

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

        throw new HttpException(400, $this->translator->get('messages.auth_error.login'));
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
