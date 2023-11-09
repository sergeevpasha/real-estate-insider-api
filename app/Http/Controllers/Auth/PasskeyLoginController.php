<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasskeyGenerateOptionsRequest;
use App\Http\Requests\Auth\PasskeyVerifyRequest;
use App\Http\Resources\Api\v1\UserResource;
use App\Models\User;
use App\Services\Auth\PasskeyService;
use Cose\Algorithm\Manager;
use Cose\Algorithm\Signature\ECDSA\ES256;
use Cose\Algorithm\Signature\ECDSA\ES256K;
use Cose\Algorithm\Signature\ECDSA\ES384;
use Cose\Algorithm\Signature\ECDSA\ES512;
use Cose\Algorithm\Signature\EdDSA\Ed256;
use Cose\Algorithm\Signature\EdDSA\Ed512;
use Cose\Algorithm\Signature\RSA\PS256;
use Cose\Algorithm\Signature\RSA\PS384;
use Cose\Algorithm\Signature\RSA\PS512;
use Cose\Algorithm\Signature\RSA\RS256;
use Cose\Algorithm\Signature\RSA\RS384;
use Cose\Algorithm\Signature\RSA\RS512;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Psr\Http\Message\ServerRequestInterface;
use Webauthn\AttestationStatement\AttestationObjectLoader;
use Webauthn\AttestationStatement\AttestationStatementSupportManager;
use Webauthn\AttestationStatement\NoneAttestationStatementSupport;
use Webauthn\AuthenticationExtensions\ExtensionOutputCheckerHandler;
use Webauthn\AuthenticatorAssertionResponse;
use Webauthn\AuthenticatorAssertionResponseValidator;
use Webauthn\AuthenticatorAttestationResponse;
use Webauthn\AuthenticatorAttestationResponseValidator;
use Webauthn\Exception\InvalidDataException;
use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialLoader;
use Webauthn\PublicKeyCredentialRequestOptions;
use Webauthn\TokenBinding\IgnoreTokenBindingHandler;

class PasskeyLoginController extends Controller
{
    public const CREDENTIAL_REQUEST_OPTIONS_SESSION_KEY = 'publicKeyCredentialRequestOptions';
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
        $options = $this->passkeyService->generateOptions($data['email']);

        $request->session()->forget(self::CREDENTIAL_REQUEST_OPTIONS_SESSION_KEY);

        $request->session()->put(
            self::CREDENTIAL_REQUEST_OPTIONS_SESSION_KEY,
            json_encode($options)
        );

        return $this->jsonResponse($options);

    }

    /**
     * @param PasskeyVerifyRequest $request
     * @return JsonResponse
     * @throws InvalidDataException
     * @throws ValidationException
     * @throws \Throwable
     */
    public function verify(PasskeyVerifyRequest $request): JsonResponse
    {
        $data = $request->validated();

        $session = $request->session()->get(self::CREDENTIAL_REQUEST_OPTIONS_SESSION_KEY);

        $user = $this->passkeyService->verify($data, $session);

        $request->session()->forget(self::CREDENTIAL_REQUEST_OPTIONS_SESSION_KEY);


        return $this->jsonResponse(new UserResource($user));

    }
}
