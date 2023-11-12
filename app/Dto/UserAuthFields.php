<?php

declare(strict_types=1);

namespace App\Dto;

class UserAuthFields extends AbstractDto
{

    private string $email;
    private ?string $avatarUrl;
    private string $applicationLanguage;
    private ?int $githubId;
    private ?string $githubNickname;
    private ?string $githubToken;
    private ?string $googleId;
    private ?string $googleToken;


    public function __construct(array $data)
    {
        $this->email = $data['email'];
        $this->avatarUrl = $data['avatar_url'] ?? null;
        $this->applicationLanguage = $data['application_language'] ?? 'en';
        $this->githubId = $data['github_id'] ?? null;
        $this->githubNickname = $data['github_nickname'] ?? null;
        $this->githubToken = $data['github_token'] ?? null;
        $this->googleId = $data['google_id'] ?? null;
        $this->googleToken = $data['google_token'] ?? null;
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
    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    /**
     * @return string
     */
    public function getApplicationLanguage(): string
    {
        return $this->applicationLanguage;
    }

    /**
     * @return int|null
     */
    public function getGithubId(): ?int
    {
        return $this->githubId;
    }

    /**
     * @return string|null
     */
    public function getGithubNickname(): ?string
    {
        return $this->githubNickname;
    }

    /**
     * @return string|null
     */
    public function getGithubToken(): ?string
    {
        return $this->githubToken;
    }

    /**
     * @return string|null
     */
    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    /**
     * @return string|null
     */
    public function getGoogleToken(): ?string
    {
        return $this->googleToken;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'email'                => $this->email,
            'application_language' => $this->applicationLanguage,
            'github_id'            => $this->githubId,
            'github_nickname'      => $this->githubNickname,
            'github_token'         => $this->githubToken,
            'google_id'            => $this->googleId,
            'google_token'         => $this->googleToken
        ];
    }
}