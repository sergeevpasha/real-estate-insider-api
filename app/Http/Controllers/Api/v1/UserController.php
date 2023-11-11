<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Dto\UserData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\User\UpdateUserRequest;
use App\Http\Resources\Api\v1\Collections\PasskeyResourceCollection;
use App\Http\Resources\Api\v1\PasskeyResource;
use App\Http\Resources\Api\v1\UserResource;
use App\Models\Passkey;
use App\Models\User;
use App\Services\Auth\PasskeyService;
use App\Services\Auth\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{

    /**
     * @param UserService $userService
     * @param PasskeyService $passkeyService
     */
    public function __construct(private readonly UserService $userService,
    private readonly PasskeyService $passkeyService)
    {
    }

    /**
     * @param UpdateUserRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $this->userService->update(new UserData($request->validated()), $user);
        return $this->jsonResponse(new UserResource($request->user()), "notification.users.update");
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function getUserPasskeys(User $user): JsonResponse
    {
        return $this->jsonResponse(New PasskeyResourceCollection($this->passkeyService->getUserPasskeys($user)));
    }
}
