<?php

declare(strict_types=1);

namespace App\Dto;

class PasskeyData extends AbstractDto
{

    private readonly int $userId;
    private readonly string $credentialId;
    private readonly array $publicKey;
    private readonly string $name;

    public function __construct(array $data)
    {
        $this->userId = $data['user_id'] ?? '';
        $this->credentialId = $data['credential_id'] ?? '';
        $this->publicKey = $data['public_key'] ?? [];
        $this->name = $data['name'] ?? null;
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
     * @return array
     */
    public function toArray(): array
    {
        return [
            'user_id'       => $this->userId,
            'credential_id' => $this->credentialId,
            'public_key'    => $this->publicKey,
            'name'          => $this->name,
        ];
    }
}