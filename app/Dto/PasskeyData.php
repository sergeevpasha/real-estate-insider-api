<?php

declare(strict_types=1);

namespace App\Dto;

class PasskeyData extends AbstractDto
{

    private readonly int $userId;
    private readonly string $credentialId;
    private readonly string $publicKey;

    public function __construct(array $data)
    {
        $this->userId = $data['user_id'] ?? '';
        $this->credentialId = $data['credential_id'] ?? '';
        $this->publicKey = $data['public_key'] ?? '';
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
     * @return array
     */
    public function toArray(): array
    {
        return [
            'user_id'       => $this->userId,
            'credential_id' => $this->credentialId,
            'public_key'    => $this->publicKey
        ];
    }
}