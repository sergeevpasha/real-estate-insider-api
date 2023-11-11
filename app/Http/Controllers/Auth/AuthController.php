<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\v1\PasskeyResource;
use App\Http\Resources\Api\v1\UserResource;
use App\Models\Passkey;
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
        if (!$request->user()) {
            return $this->jsonResponse([], "notification.users.unauthorized", 401);
        }

        return $this->jsonResponse(new UserResource($request->user()), "notification.users.login");
    }

    /**
     * @param Request $request
     * @param Passkey $passkey
     * @return JsonResponse
     */
    public function deleteUserPasskey(Request $request, Passkey $passkey): JsonResponse
    {
        $user = $request->user();
        $passkey = $user->passkeys()->findOrFail($passkey->id);
        $passkey->delete();

        return $this->jsonResponse();
    }

    /**
     * @param Request $request
     * @param Passkey $passkey
     * @return JsonResponse
     */
    public function updateUserPasskey(Request $request, Passkey $passkey): JsonResponse
    {
        $user = $request->user();
        $passkey = $user->passkeys()->findOrFail($passkey->id);
        $passkey->update($request->all());

        return $this->jsonResponse(New PasskeyResource($passkey));
    }
}
