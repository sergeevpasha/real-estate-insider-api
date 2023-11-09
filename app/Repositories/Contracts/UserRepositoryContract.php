<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Dto\UserData;
use App\Models\User;
use Laravel\Sanctum\NewAccessToken;

interface UserRepositoryContract
{
    /**
     * Create User
     *
     * @param UserData $userData
     * @return User
     */
    public function create(UserData $userData): User;

    /**
     * @param User $user
     * @return NewAccessToken
     */
    public function createToken(User $user): NewAccessToken;

}
