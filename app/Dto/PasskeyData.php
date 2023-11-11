<?php

declare(strict_types=1);

namespace App\Dto;

class PasskeyData extends AbstractDto
{
    private readonly ?int $userId;
    private readonly ?string $credentialId;
    private readonly ?array $publicKey;
    private readonly ?string $name;
    private readonly ?string $lastUsedAt;

    public function __construct(array $data)
    {
        $this->userId = $data['user_id'] ?? null;
        $this->credentialId = $data['credential_id'] ?? null;
        $this->publicKey = $data['public_key'] ?? [];
        $this->name = $data['name'] ?? null;
        $this->lastUsedAt = $data['last_used_at'] ?? null;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getCredentialId(): string
    {
        return $this->credentialId;
    }

    /**
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLastUsedAt(): string
    {
        return $this->lastUsedAt;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'user_id'       => $this->userId,
            'credential_id' => $this->credentialId,
            'public_key'    => $this->publicKey,
            'name'          => $this->name,
            'last_used_at'  => $this->lastUsedAt,
        ];
    }
}