<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\Contracts\PasskeyRepositoryContract;
use App\Repositories\Contracts\UserRepositoryContract;
use Cose\Algorithms;
use Exception;
use Illuminate\Validation\ValidationException;
use Throwable;
use Webauthn\AttestationStatement\AttestationObjectLoader;
use Webauthn\AttestationStatement\AttestationStatementSupportManager;
use Webauthn\AttestationStatement\NoneAttestationStatementSupport;
use Webauthn\AuthenticationExtensions\ExtensionOutputCheckerHandler;
use Webauthn\AuthenticatorAttestationResponse;
use Webauthn\AuthenticatorAttestationResponseValidator;
use Webauthn\AuthenticatorSelectionCriteria;
use Webauthn\Exception\InvalidDataException;
use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialLoader;
use Webauthn\PublicKeyCredentialParameters;
use Webauthn\PublicKeyCredentialRpEntity;
use Webauthn\PublicKeyCredentialUserEntity;

readonly class PasskeyService
{
    /**
     * @param PasskeyRepositoryContract $passkeyRepository
     * @param UserRepositoryContract $userRepository
     */
    public function __construct(
        private PasskeyRepositoryContract $passkeyRepository,
        private UserRepositoryContract $userRepository
    ) {
    }

    /**
     * @param string $email
     * @return array
     * @throws Exception
     */
    public function generateOptions(string $email): array
    {
        $rpEntity = PublicKeyCredentialRpEntity::create(
            'Real Estate Insider Webauthn',
            config('app.domain')
        );

        $user = $this->userRepository->findByEmail($email);

        $userEntity = PublicKeyCredentialUserEntity::create(
            $user->system_name,
            (string) $user->id,
            $user->full_name ?? 'Secret Agent'
        );

        $challenge = random_bytes(16);

        $supportedPublicKeyParams = collect([
            Algorithms::COSE_ALGORITHM_ES256,
            Algorithms::COSE_ALGORITHM_ES256K,
            Algorithms::COSE_ALGORITHM_ES384,
            Algorithms::COSE_ALGORITHM_ES512,
            Algorithms::COSE_ALGORITHM_RS256,
            Algorithms::COSE_ALGORITHM_RS384,
            Algorithms::COSE_ALGORITHM_RS512,
            Algorithms::COSE_ALGORITHM_PS256,
            Algorithms::COSE_ALGORITHM_PS384,
            Algorithms::COSE_ALGORITHM_PS512,
            Algorithms::COSE_ALGORITHM_ED256,
            Algorithms::COSE_ALGORITHM_ED512,
        ])->map(
            fn($algorithm) => PublicKeyCredentialParameters::create('public-key', $algorithm)
        )->toArray();

        $publicKeyCredentialCreationOptions =
            PublicKeyCredentialCreationOptions::create(
                rp: $rpEntity,
                user: $userEntity,
                challenge: $challenge,
                pubKeyCredParams: $supportedPublicKeyParams,
                authenticatorSelection: AuthenticatorSelectionCriteria::create(),
                attestation: PublicKeyCredentialCreationOptions::ATTESTATION_CONVEYANCE_PREFERENCE_NONE,
                timeout: 30_000,
            );

        return $publicKeyCredentialCreationOptions->jsonSerialize();
    }


    /**
     * @param array $data
     * @param string $session
     * @return User|null
     * @throws InvalidDataException
     * @throws Throwable
     * @throws ValidationException
     */
    public function verify(array $data, string $session): ?User
    {
        $attestationStatementSupportManager = AttestationStatementSupportManager::create();
        $attestationStatementSupportManager->add(NoneAttestationStatementSupport::create());

        $attestationObjectLoader = AttestationObjectLoader::create(
            $attestationStatementSupportManager
        );

        $publicKeyCredentialLoader = PublicKeyCredentialLoader::create(
            $attestationObjectLoader
        );

        $publicKeyCredential = $publicKeyCredentialLoader->load(json_encode($data));

        if (!$publicKeyCredential->response instanceof AuthenticatorAttestationResponse) {
            throw ValidationException::withMessages([
                'passkey' => 'Invalid response type',
            ]);
        }

        $extensionOutputCheckerHandler = ExtensionOutputCheckerHandler::create();

        $authenticatorAttestationResponseValidator = AuthenticatorAttestationResponseValidator::create(
            $attestationStatementSupportManager,
            null,
            null,
            $extensionOutputCheckerHandler,
        );

        $publicKeyCredentialSource = $authenticatorAttestationResponseValidator->check(
            $publicKeyCredential->response,
            PublicKeyCredentialCreationOptions::createFromArray(json_decode($session, true)),
            config('app.domain')
        );

        logger(json_encode($publicKeyCredentialSource ));
        logger(json_encode($publicKeyCredential->response ));
        logger(json_encode($publicKeyCredential->response->userHandle ));
        return $this->userRepository->getBySystemName($publicKeyCredential->response->userHandle);
    }
}