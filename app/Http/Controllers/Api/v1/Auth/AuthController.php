<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\v1\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function fetchUser(Request $request): JsonResponse
    {
        return $this->jsonResponse(new UserResource($request->user()), "notification.users.login");
    }
}
