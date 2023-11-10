<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Dto\PasskeyData;
use App\Models\Passkey;
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
     * Get User associated passkeys
     *
     * @param int $userId
     * @return Collection|null
     */
    public function getUserPasskeys(int $userId): ?Collection
    {
        return $this->passkey->where('user_id', '=', $userId)->get();
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
