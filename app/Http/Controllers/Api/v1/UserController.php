<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Dto\UserData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\User\UpdateUserRequest;
use App\Http\Resources\Api\v1\UserResource;
use App\Services\Auth\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{

    /**
     * @param UserService $userService
     */
    public function __construct(private readonly UserService $userService)
    {
    }

    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $this->userService->update(new UserData($request->validated()), $id);
        return $this->jsonResponse(new UserResource($request->user()), "notification.users.login");
    }
}
