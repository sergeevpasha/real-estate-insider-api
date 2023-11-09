<?php

declare(strict_types=1);

namespace App\Dto;

use App\Helpers\ArrayHelper;

class UserAuthFields extends AbstractDto
{

    private string $email;
    private string $applicationLanguage;
    private ?int $githubId;
    private ?string $googleId;


    public function __construct(array $data)
    {
        $this->email = $data['email'];
        $this->applicationLanguage = $data['application_language'] ?? 'en';
        $this->githubId = $data['github_id'] ?? null;
        $this->googleId = $data['google_id'] ?? null;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
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
    public function getGoogleId(): ?string
    {
        return $this->googleId;
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
            'google_id'            => $this->googleId
        ];
    }
}