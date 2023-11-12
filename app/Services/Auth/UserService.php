<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Dto\UserAuthFields;
use App\Dto\UserData;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryContract;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Sanctum\NewAccessToken;

readonly class UserService
{
    /**
     * @param UserRepositoryContract $userRepository
     * @param FileStorageService $fileStorageService
     */
    public function __construct(
        private UserRepositoryContract $userRepository,
        private FileStorageService $fileStorageService
    ) {
    }

    /**
     * @param UserData $registerData
     * @return User
     */
    public function register(UserData $registerData): User
    {
        $registerData->setSystemName($this->generateSystemName($registerData->getEmail()));
        $registerData->setPassword(Hash::make($registerData->getPassword()));
        return $this->userRepository->create($registerData);
    }

    /**
     * @param string $email
     * @param string $password
     * @return User|null
     */
    public function login(string $email, string $password): ?User
    {
        if (!Auth::attempt(compact('email', 'password'))) {
            return null;
        }

        return $this->userRepository->findByEmail($email);
    }

    /**
     * @param UserAuthFields $fields
     * @return User|null
     */
    public function setSocialUser(UserAuthFields $fields): ?User
    {
        $user = $this->userRepository->findByAuthFields($fields);

        if (!$user) {
            $user = $this->userRepository->create(
                new UserData([
                    ...$fields->toNotNullableArray(),
                    'password' => Hash::make(Str::random(32)),
                    'password_not_set' => true,
                    'system_name' => $this->generateSystemName($fields->getEmail())
                ])
            );
        } else {
            $user = $this->userRepository->update(
                $user,
                new UserData(Arr::except($fields->toNotNullableArray(), ['application_language']))
            );
        }

        if ($avatarUrl = $fields->getAvatarUrl()) {
            return $this->setUserAvatarFromSocial($user, $avatarUrl);
        }

        return $user;
    }

    /**
     * @param UserData $fields
     * @param User $user
     * @return User
     */
    public function update(UserData $fields, User $user): User
    {
        return $this->userRepository->update($user, $fields);
    }

    /**
     * @param User $user
     * @return NewAccessToken
     */
    public function createToken(User $user): NewAccessToken
    {
        return $this->userRepository->createToken($user);
    }

    private function generateSystemName(string $email): string
    {
        $systemName = strtolower(stristr($email, '@', true));

        try {
            $systemName = $systemName . bin2hex(random_bytes(4));
        } catch (Exception) {
            $systemName = $systemName . '01234567';
        }

        if (!$this->userRepository->getBySystemName($systemName)) {
            return $systemName;
        }

        return $this->generateSystemName($email);
    }

    /**
     * @param User $user
     * @param string $avatarUrl
     * @return User
     */
    private function setUserAvatarFromSocial(User $user, string $avatarUrl): User
    {
        $fullAvatarUrl = strrpos($avatarUrl, '=') !== false ? substr(
            $avatarUrl,
            0,
            strrpos($avatarUrl, '=')
        ) : $avatarUrl;

        $oldAvatar = $user->avatar;
        $avatar = $this->fileStorageService->downloadExternalImage($fullAvatarUrl);
        $avatarStorageLink = $this->fileStorageService->storeFile($avatar, "users/$user->id/avatar");

        $user = $this->userRepository->update(
            $user,
            new UserData([
                'avatar' => $avatarStorageLink
            ])
        );

        if ($oldAvatar) {
            $this->fileStorageService->deleteFile($oldAvatar);
        }

        return $user;
    }
}