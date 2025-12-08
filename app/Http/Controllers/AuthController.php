<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailVerificationRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Responses\Response;
use App\Notifications\EmailVerificationNotification;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    private AuthService $authService;
    

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        $data = [];
        try {
            $validatedData = $request->validated();
            $data = $this->authService->register($validatedData, 'citizen');
            return Response::Success([
                'user'   => $data['user'],
                'tokens' => $data['tokens'] ?? null,
            ], $data['message'], $data['code']);
        } catch (\Throwable $exception) {
            $message = $exception->getMessage();
            return Response::Error($data, $message);
        }
    }


    public function loginCitizen(LoginRequest $request)
    {
        return $this->login($request, 'citizen');
    }

    public function loginEmployee(LoginRequest $request)
    {
        return $this->login($request, 'employee');
    }

    public function loginAdmin(LoginRequest $request)
    {
        return $this->login($request, 'admin');
    }

    public function login(LoginRequest $request, string $role)
    {


        $data = [];
        try {
            $key = 'login:' . $request->ip() . ':' . $request->email;
            if (RateLimiter::tooManyAttempts($key, (int)config('auth.rate_limit_login', 5))) {
                $seconds = RateLimiter::availableIn($key);
                return Response::Error([], "Too many attempts. Try again in $seconds seconds.", 429);
            }
            $validatedData = $request->validated();
            $data = $this->authService->login($validatedData, $role);
            RateLimiter::hit($key, 60);
            return Response::Success([
                'user'   => $data['user'],
                'tokens' => $data['tokens'] ?? null,
            ], $data['message'], $data['code']);
        } catch (\Throwable $exception) {
            $message = $exception->getMessage();
            return Response::Error($data, $message);
        }
    }


    public function logout(Request $request)
    {
        try {
            $token = $request->bearerToken();

            $data = $this->authService->logout($token);

            if (!$data['success']) {
                return Response::Error([], $data['message'], $data['code']);
            }

            return Response::Success(true, $data['message'], $data['code']);
        } catch (\Throwable $exception) {
            return Response::Error([], 'Failed to logout: ' . $exception->getMessage(), 500);
        }
    }


    public function emailVerification(EmailVerificationRequest $request)
    {
        $data = [];
        try {
            $validatedData = $request->validated();
            $data = $this->authService->emailVerification($validatedData);
            return Response::Success($data['user'], $data['message'], $data['code']);
        } catch (\Throwable $exception) {
            $message = $exception->getMessage();
            return Response::Error($data, $message);
        }
    }

    public function sendEmailVerification(Request $request)
    {
        $request->user()->notify(new EmailVerificationNotification());
        return Response::Success(true, 'success', 200);
    }
}
