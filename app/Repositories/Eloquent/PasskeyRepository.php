<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Dto\PasskeyData;
use App\Models\Passkey;
use App\Models\User;
use App\Repositories\Contracts\PasskeyRepositoryContract;
use Illuminate\Database\Eloquent\Collection;

readonly class PasskeyRepository implements PasskeyRepositoryContract
{
    /**
     * @param Passkey $passkey
     */
    public function __construct(private Passkey $passkey)
    {
    }

    /**
     * @param string $publicKeyCredentialId
     * @return Passkey|null
     */
    public function getByCredentialId(string $publicKeyCredentialId): ?Passkey
    {
        return $this->passkey->where('credential_id', '=', $publicKeyCredentialId)->first();
    }

    /**
     * Get User associated passkeys
     *
     * @param User $user
     * @return Collection|null
     */
    public function getUserPasskeys(User $user): ?Collection
    {
        return $user->passkeys()->get();
    }

    /**
     * Create Passkey
     *
     * @param PasskeyData $passkeyData
     * @return Passkey
     */
    public function create(PasskeyData $passkeyData): Passkey
    {
        return $this->passkey->create($passkeyData->toArray());
    }
}
