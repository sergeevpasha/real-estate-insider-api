<?php

declare(strict_types=1);

namespace App\Dto;

class PasswordResetData extends AbstractDto
{

    private string $email;
    private string $password;
    private string $token;


    public function __construct(array $data)
    {
        $this->email = $data['email'] ?? null;
        $this->password = $data['password'] ?? null;
        $this->token = $data['token'] ?? null;
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
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'email'    => $this->email,
            'password' => $this->password,
            'token'    => $this->token
        ];
    }
}