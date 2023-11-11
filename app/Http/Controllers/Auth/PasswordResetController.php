<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordResetEmailLinkRequest;
use App\Http\Requests\Auth\PasswordResetRequest;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
    /**
     * @param PasswordResetEmailLinkRequest $request
     * @return JsonResponse
     */
    public function sendResetLinkEmail(PasswordResetEmailLinkRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = User::where('email', '=', $data['email'])->first();
        $token = Password::createToken($user);
        $url = trim($data['domain'], '/') . '/reset-password?token=' . $token;

        $user->notify(new ResetPasswordNotification($url));

        return $this->jsonResponse([], "notification.users.resetLinkSent");
    }

    /**
     * @param PasswordResetRequest $request
     * @return JsonResponse
     */
    public function resetPassword(PasswordResetRequest $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password has been reset.'])
            : response()->json(['message' => 'Failed to reset password.'], 422);
    }
}
