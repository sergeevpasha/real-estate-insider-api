<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasskeyGenerateOptionsRequest;
use App\Http\Requests\Auth\PasskeyRegisterVerifyRequest;
use App\Http\Resources\Api\v1\UserResource;
use App\Services\Auth\PasskeyService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Throwable;
use Webauthn\Exception\InvalidDataException;

class PasskeyRegisterController extends Controller
{
    public const CREDENTIAL_CREATION_OPTIONS_SESSION_KEY = 'publicKeyCredentialCreationOptions';

    /**
     * @param PasskeyService $passkeyService
     */
    public function __construct(private readonly PasskeyService $passkeyService)
    {
    }

    /**
     * @param PasskeyGenerateOptionsRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function generateOptions(PasskeyGenerateOptionsRequest $request): JsonResponse
    {
        $data = $request->validated();
        $options = $this->passkeyService->generateRegistrationOptions($data['email']);

        $request->session()->forget(self::CREDENTIAL_CREATION_OPTIONS_SESSION_KEY);
        $request->session()->put(self::CREDENTIAL_CREATION_OPTIONS_SESSION_KEY, json_encode($options));

        return $this->jsonResponse($options);
    }

    /**
     * @param PasskeyRegisterVerifyRequest $request
     * @return JsonResponse
     * @throws InvalidDataException
     * @throws Throwable
     * @throws ValidationException
     */
    public function verify(PasskeyRegisterVerifyRequest $request): JsonResponse
    {
        $data = $request->validated();

        $session = $request->session()->get(self::CREDENTIAL_CREATION_OPTIONS_SESSION_KEY);

        $user = $this->passkeyService->verifyRegistration($data, $session);

        $request->session()->forget(self::CREDENTIAL_CREATION_OPTIONS_SESSION_KEY);

        return $this->jsonResponse(new UserResource($user));
    }
}
