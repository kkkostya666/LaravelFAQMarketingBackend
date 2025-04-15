<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Throwable;

class AuthController extends Controller {

    public function login(AuthLoginRequest $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']);

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Неверный адрес электронной почты или пароль.'], 401);
        }

        return response()->json([
            'access_token' => $token,
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'token_type' => 'bearer',
        ]);
    }

    public function store(AuthRegisterRequest $request): JsonResponse {
        try {
            $data = $request->validated();
            User::create($data);
        } catch (Throwable $exception) {
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);
        }

        return response()->json([
            'message' => 'Успешная регистрация пользователя'
        ]);
    }

    public function logout(): JsonResponse {
        auth()->logout(true);
        return response()->json(['message' => 'Successfully logged out'])
            ->withCookie(cookie()->forget('token'));
    }

    public function refresh(Request $request): JsonResponse {
        try {
            $refreshToken = $request->cookie('token');

            if (!$refreshToken) {
                return response()->json([
                    'error' => 'Refresh token not provided',
                ], 400);
            }

            auth()->setToken($refreshToken);

            $newAccessToken = auth()->refresh();
            $newRefreshToken = auth()->getToken();

            return response()->json([
                'access_token' => $newAccessToken,
                'expires_in' => auth()->factory()->getTTL() * 60
            ])->withCookie(cookie('token',
                    $newRefreshToken,
                    60 * 24 * 30,
                    '/',
                    config('app.APP_FRONT_URL'),
                    false,
                    true,
                    false,
//                    'Lax')
                    'Strict')
            );
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Invalid refresh token',
                'message' => $e->getMessage()
            ], 401);
        }
    }

    public function forgotPassword(AuthForgotPasswordRequest $request): JsonResponse {
        try {
            $data = $request->validated();

            $status = Password::sendResetLink(
                ["email" => $data['email']]
            );

            if ($status !== Password::RESET_LINK_SENT) {
                throw new Exception('Unable to send reset link.');
            }
        } catch (Throwable $exception) {
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);
        }

        return response()->json([
            'message' => 'Письмо для сброса пароля выслано на почту'
        ]);
    }

    public function resetPassword(AuthResetPasswordRequest $request): JsonResponse {
        try {
            $request->validated();

            $status = Password::reset(
                $request->only(
                    'email',
                    'password',
                    'password_confirmation',
                    'token'
                ),
                function (User $user, string $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                    ])->save();
                }
            );

            if ($status !== Password::PASSWORD_RESET) {
                throw new Exception('Failed to reset password');
            }
        } catch (Throwable $exception) {
            return response()->json([
                'error' => $exception->getMessage()
            ], 400);
        }

        return response()->json([
            'message' => 'Пароль успешно сброшен!'
        ]);
    }

    public function currentPassword(AuthLoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (!auth()->attempt($credentials)) {
            return response()->json([
                'current_password' => false
            ]);        }
        return response()->json([
            'current_password' => true
        ]);
    }
}
