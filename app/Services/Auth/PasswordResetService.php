<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Dto\PasswordResetData;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use App\Repositories\Contracts\UserRepositoryContract;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

readonly class PasswordResetService
{
    /**
     * @param UserRepositoryContract $userRepository
     */
    public function __construct(
        private UserRepositoryContract $userRepository
    ) {
    }

    /**
     * @param string $email
     * @param string $domain
     * @return void
     */
    public function sendResetLink(string $email, string $domain): void {
        $user = $this->userRepository->findByEmail($email);
        $token = Password::createToken($user);
        $url = trim($domain, '/') . '/reset-password?token=' . $token . '&email=' . $email;

        $user->notify(new ResetPasswordNotification($url));
    }

    /**
     * @param string $email
     * @param string $token
     * @return bool
     */
    public function isValidToken(string $email, string $token): bool
    {
        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', '=', $email)
            ->first();

        if (!$token || !Hash::check($token, $passwordReset->token)) {
            return false;
        }

        $tokenCreationDate = Carbon::parse($passwordReset->created_at);
        $tokenLifespan = config('auth.passwords.users.expire');
        return !(Carbon::now()->diffInMinutes($tokenCreationDate) > $tokenLifespan);
    }

    /**
     * @param PasswordResetData $passwordResetData
     * @return User|null
     */
    public function resetPassword(PasswordResetData $passwordResetData): ?User
    {
        $status = Password::reset(
            $passwordResetData->toArray(),
            function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return null;
        }

        return $this->userRepository->findByEmail($passwordResetData->getEmail());
    }
}