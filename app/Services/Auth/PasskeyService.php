<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Dto\PasskeyData;
use App\Models\User;
use App\Repositories\Contracts\PasskeyRepositoryContract;
use App\Repositories\Contracts\UserRepositoryContract;
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
use Cose\Algorithms;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Throwable;
use Webauthn\AttestationStatement\AttestationObjectLoader;
use Webauthn\AttestationStatement\AttestationStatementSupportManager;
use Webauthn\AttestationStatement\NoneAttestationStatementSupport;
use Webauthn\AuthenticationExtensions\ExtensionOutputCheckerHandler;
use Webauthn\AuthenticatorAssertionResponse;
use Webauthn\AuthenticatorAssertionResponseValidator;
use Webauthn\AuthenticatorAttestationResponse;
use Webauthn\AuthenticatorAttestationResponseValidator;
use Webauthn\AuthenticatorSelectionCriteria;
use Webauthn\Exception\InvalidDataException;
use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialDescriptor;
use Webauthn\PublicKeyCredentialLoader;
use Webauthn\PublicKeyCredentialParameters;
use Webauthn\PublicKeyCredentialRequestOptions;
use Webauthn\PublicKeyCredentialRpEntity;
use Webauthn\PublicKeyCredentialSource;
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
     * @param User $user
     * @return Collection|null
     */
    public function getUserPasskeys(User $user): ?Collection
    {
        return $this->passkeyRepository->getUserPasskeys($user);
    }

    /**
     * @param string $email
     * @return array
     * @throws Exception
     */
    public function generateRegistrationOptions(string $email): array
    {
        $rpEntity = PublicKeyCredentialRpEntity::create(
            'Real Estate Insider Webauthn',
            config('app.domain')
        );

        $user = $this->userRepository->findByEmail($email);

        $userEntity = PublicKeyCredentialUserEntity::create(
            $user->system_name,
            $user->system_name,
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
    public function verifyRegistration(array $data, string $session): ?User
    {
        $attestationStatementSupportManager = AttestationStatementSupportManager::create();
        $attestationStatementSupportManager->add(NoneAttestationStatementSupport::create());

        $attestationObjectLoader = AttestationObjectLoader::create(
            $attestationStatementSupportManager
        );

        $publicKeyCredentialLoader = PublicKeyCredentialLoader::create(
            $attestationObjectLoader
        );

        logger(json_decode(urldecode($data['attributes'])));
        $publicKeyCredential = $publicKeyCredentialLoader->load(urldecode($data['attributes']));

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
        logger('ddd');

        $user = $this->userRepository->getBySystemName($publicKeyCredentialSource->userHandle);
        $this->passkeyRepository->create(
            new PasskeyData([
                'user_id'       => $user->id,
                'credential_id' => $publicKeyCredentialSource->publicKeyCredentialId,
                'public_key'    => $publicKeyCredentialSource->jsonSerialize(),
                'name'          => $data['name'],
            ])
        );

        logger($user);
        return $user;
    }

    /**
     * @param string $email
     * @return array
     * @throws Exception
     */
    public function generateLoginOptions(string $email): array
    {
        $user = $this->userRepository->findByEmail($email);

        $passkeys = $this->passkeyRepository->getUserPasskeys($user);

        $allowedCredentials = array_map(
            static function (array $passkey): PublicKeyCredentialDescriptor {
                $credential = PublicKeyCredentialSource::createFromArray($passkey['public_key']);
                return $credential->getPublicKeyCredentialDescriptor();
            },
            $passkeys->toArray()
        );


        $publicKeyCredentialRequestOptions =
            PublicKeyCredentialRequestOptions::create(
                challenge: random_bytes(32),
                allowCredentials: $allowedCredentials
            );

        return $publicKeyCredentialRequestOptions->jsonSerialize();
    }

    /**
     * @param array $data
     * @param string $session
     * @return User|null
     * @throws InvalidDataException
     * @throws Throwable
     * @throws ValidationException
     */
    public function verifyLogin(array $data, string $session): ?User
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

        if (!$publicKeyCredential->response instanceof AuthenticatorAssertionResponse) {
            throw ValidationException::withMessages([
                'passkey' => 'Invalid response type',
            ]);
        }

        $extensionOutputCheckerHandler = ExtensionOutputCheckerHandler::create();

        $algorithmManager = Manager::create()
            ->add(
                ES256::create(),
                ES256K::create(),
                ES384::create(),
                ES512::create(),
                RS256::create(),
                RS384::create(),
                RS512::create(),
                PS256::create(),
                PS384::create(),
                PS512::create(),
                Ed256::create(),
                Ed512::create(),
            );

        $authenticatorAssertionResponseValidator = AuthenticatorAssertionResponseValidator::create(
            null,
            null,
            $extensionOutputCheckerHandler,
            $algorithmManager
        );

        $passkey = $this->passkeyRepository->getByCredentialId($publicKeyCredential->rawId);

        $publicKeyCredentialSource = $authenticatorAssertionResponseValidator->check(
            PublicKeyCredentialSource::createFromArray($passkey->public_key),
            $publicKeyCredential->response,
            PublicKeyCredentialRequestOptions::createFromArray(json_decode($session, true)),
            config('app.domain'),
            $publicKeyCredential->response->userHandle
        );

        $this->passkeyRepository->update(
            $passkey,
            new PasskeyData([
                'last_used_at' => Carbon::now(),
            ])
        );

        return $this->userRepository->getBySystemName($publicKeyCredentialSource->userHandle);
    }
}