<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Dto\PasswordResetData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordResetEmailLinkRequest;
use App\Http\Requests\Auth\PasswordResetRequest;
use App\Http\Requests\Auth\PasswordResetValidateTokenRequest;
use App\Services\Auth\PasswordResetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PasswordResetController extends Controller
{

    /**
     * @param PasswordResetService $passwordResetService
     */
    public function __construct(private readonly PasswordResetService $passwordResetService)
    {
    }

    /**
     * @param PasswordResetEmailLinkRequest $request
     * @return JsonResponse
     */
    public function sendResetLinkEmail(PasswordResetEmailLinkRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->passwordResetService->sendResetLink($data['email'], $data['domain']);

        return $this->jsonResponse([], "notification.users.resetLinkSent");
    }

    /**
     * @param PasswordResetValidateTokenRequest $request
     * @return JsonResponse
     */
    public function validateExpirationToken(PasswordResetValidateTokenRequest $request): JsonResponse
    {
        $data = $request->validated();

        $isValid = $this->passwordResetService->isValidToken($data['email'], $data['token']);

        if (!$isValid) {
            return $this->jsonResponse(['valid' => false], "notification.users.invalidToken", 422);
        }

        return $this->jsonResponse(['valid' => true], "notification.users.validToken");
    }

    /**
     * @param PasswordResetRequest $request
     * @return JsonResponse
     */
    public function resetPassword(PasswordResetRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = $this->passwordResetService->resetPassword(new PasswordResetData($data));

        if (!$user) {
            return $this->jsonResponse([], "notification.users.failToResetPassword", 422);
        }

        Auth::login($user);

        return $this->jsonResponse([], "notification.users.passwordResetSuccess");
    }
}
