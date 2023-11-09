<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Dto\UserAuthFields;
use App\Dto\UserData;
use App\Models\Passkey;
use App\Models\User;
use App\Repositories\Contracts\PasskeyRepositoryContract;
use App\Repositories\Contracts\UserRepositoryContract;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Sanctum\PersonalAccessToken;

class PasskeyRepository implements PasskeyRepositoryContract
{
    /**
     * @param Passkey $passkey
     */
    public function __construct(private readonly Passkey $passkey)
    {
    }

    /**
     * Create Passkey
     *
     * @param UserData $userData
     * @return User
     */
    public function create(UserData $userData): User
    {
        return $this->user->create($userData->toArray());
    }
}
