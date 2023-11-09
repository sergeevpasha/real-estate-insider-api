<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\v1\User;

use Illuminate\Validation\Rule;

class UpdateUserRequest extends CreateUserRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<array>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'email'    => [
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user),
            ],
            'password' => ['string', 'min:8', 'max:30', 'confirmed'],
        ]);
    }
}

