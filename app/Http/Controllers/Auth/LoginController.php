<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Api\v1\UserResource;
use App\Services\Auth\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    /**
     * @param UserService $userService
     */
    public function __construct(private readonly UserService $userService)
    {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = $this->userService->login($request->email, $request->password);

        if (!$user) {
            return $this->jsonResponse([],  "notification.users.invalid_credentials", 401);
        }

        if ($request->input('trust_device')) {
            config(['session.lifetime' => 60 * 24 * 60]);
        }

        $request->session()->regenerate();

        return $this->jsonResponse(new UserResource($user),  "notification.users.login");
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::logout();

        $request->session()->invalidate();

        return $this->jsonResponse([],  "notification.users.logout");
    }
}
