<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Dto\UserAuthFields;
use App\Dto\UserData;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryContract;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Sanctum\PersonalAccessToken;

class UserRepository implements UserRepositoryContract
{
    public const TOKEN_NAME = 'rei-api-token';
    public const TOKEN_EXPIRATION = 60;

    /**
     * @param User $user
     */
    public function __construct(private readonly User $user)
    {
    }

    /**
     * Create User
     *
     * @param array $userData
     * @return User
     */
    public function create(array $userData): User
    {
        return $this->user->create($userData);
    }

    /**
     * Find User by ID
     *
     * @param int $userId
     * @param array $with
     * @return User|null
     */
    public function getById(int $userId, array $with = []): ?User
    {
        return $this->user->where('id', '=', $userId)
            ->when(count($with) > 0, fn($query) => $query->with($with))
            ->first();
    }

    /**
     * Find User by System Name
     *
     * @param string $systemName
     * @return User|null
     */
    public function getBySystemName(string $systemName): ?User
    {
        return $this->user->where('system_name', '=', $systemName)->first();
    }

    /**
     * Find User by Email
     *
     * @param string $email
     * @return ?User
     */
    public function findByEmail(string $email): ?User
    {
        return $this->user->where('email', '=', $email)->first();
    }

    /**
     * Find User by GitHub ID
     *
     * @param string $githubId
     * @return ?User
     */
    public function findByGithubId(string $githubId): ?User
    {
        return $this->user->where('github_id', '=', $githubId)->first();
    }

    /**
     * Find User by Google ID
     *
     * @param string $googleId
     * @return ?User
     */
    public function findByGoogleId(string $googleId): ?User
    {
        return $this->user->where('google_id', '=', $googleId)->first();
    }

    /**
     * Find User by Any Auth field
     *
     * @param UserAuthFields $fields
     * @return User|null
     */
    public function findByAuthFields(UserAuthFields $fields): ?User
    {
        return $this->user
            ->when($fields->getEmail(), fn($query) => $query->where('email', '=', $fields->getEmail()))
            ->when($fields->getGithubId(), fn($query) => $query->orWhere('github_id', '=', $fields->getGithubId()))
            ->when($fields->getGoogleId(), fn($query) => $query->orWhere('google_id', '=', $fields->getGoogleId()))
            ->first();
    }

    /**
     * Update User
     *
     * @param User $user
     * @param UserData $userData
     * @return User
     */
    public function update(User $user, array $userData): User
    {
        $user->update($userData);
        return $user->refresh();
    }

    /**
     * @param User $user
     * @return NewAccessToken
     */
    public function createToken(User $user): NewAccessToken
    {
        return $user->createToken(self::TOKEN_NAME);
    }

    /**
     * @param PersonalAccessToken $token
     * @return void
     */
    public function setTokenDefaultExpiration(PersonalAccessToken $token): void
    {
        $token->update([
            'expires_at' => Carbon::now()->addDays(self::TOKEN_EXPIRATION)
        ]);
    }
}
