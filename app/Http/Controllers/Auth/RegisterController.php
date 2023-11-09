<?php

namespace App\Http\Controllers\Auth;

use App\Dto\UserData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Api\v1\UserResource;
use App\Http\Traits\GuessLanguageTrait;
use App\Services\Auth\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    use GuessLanguageTrait;

    /**
     * @param UserService $userService
     */
    public function __construct(private readonly UserService $userService)
    {
    }

    /**
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $language = $this->guessLanguage($request);

        $user = $this->userService->register(new UserData([...$data, ['application_language' => $language]]));

        Auth::login($user);

        $request->session()->regenerate();

        return $this->jsonResponse(new UserResource($user), "notification.users.registration", 201);
    }
}
