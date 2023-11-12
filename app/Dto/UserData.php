<?php

declare(strict_types=1);

namespace App\Dto;

class UserData extends AbstractDto
{

    private readonly ?string $firstName;
    private readonly ?string $lastName;
    private readonly ?string $email;
    private readonly ?string $avatar;
    private ?string $systemName;
    private readonly ?string $applicationLanguage;
    private ?string $password;
    private ?bool $passwordNotSet;
    private readonly ?int $githubId;
    private readonly ?string $githubToken;
    private readonly ?string $googleId;
    private readonly ?string $googleToken;


    public function __construct(array $data)
    {
        $this->firstName = $data['first_name'] ?? null;
        $this->lastName = $data['last_name'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->avatar = $data['avatar'] ?? null;
        $this->systemName = $data['system_name'] ?? null;
        $this->applicationLanguage = $data['application_language'] ?? null;
        $this->password = $data['password'] ?? null;
        $this->passwordNotSet = $data['password_not_set'] ?? false;
        $this->githubId = $data['github_id'] ?? null;
        $this->githubToken = $data['github_token'] ?? null;
        $this->googleId = $data['google_id'] ?? null;
        $this->googleToken = $data['google_token'] ?? null;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return "$this->firstName $this->lastName";
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Get avatar url
     *
     * @return string|null
     */
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    /**
     * @return string
     */
    public function getApplicationLanguage(): string
    {
        return $this->applicationLanguage;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return void
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return bool
     */
    public function getPasswordNotSet(): bool
    {
        return $this->passwordNotSet;
    }

    /**
     * @return int
     */
    public function getGithubId(): int
    {
        return $this->githubId;
    }

    /**
     * @return string
     */
    public function getGithubToken(): string
    {
        return $this->githubToken;
    }

    /**
     * @return int
     */
    public function getGoogleId(): int
    {
        return $this->googleId;
    }

    /**
     * @return string
     */
    public function getGoogleToken(): string
    {
        return $this->googleToken;
    }

    /**
     * @param string $systemName
     * @return void
     */
    public function setSystemName(string $systemName): void
    {
        $this->systemName = $systemName;
    }

    /**
     * @return string
     */
    public function getSystemName(): string
    {
        return $this->systemName;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'first_name'           => $this->firstName,
            'last_name'            => $this->lastName,
            'email'                => $this->email,
            'avatar'               => $this->avatar,
            'system_name'          => $this->systemName,
            'application_language' => $this->applicationLanguage,
            'password'             => $this->password,
            'password_not_set'     => $this->passwordNotSet,
            'github_id'            => $this->githubId,
            'github_token'         => $this->githubToken,
            'google_id'            => $this->googleId,
            'google_token'         => $this->googleToken
        ];
    }
}