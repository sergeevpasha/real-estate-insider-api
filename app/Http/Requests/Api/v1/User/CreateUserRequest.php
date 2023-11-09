<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\v1\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<array>
     */
    public function rules(): array
    {
        return [
            'email'                => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
            ],
            'first_name'           => ['nullable', 'string'],
            'last_name'            => ['nullable', 'string'],
            'password'             => ['required', 'string', 'min:8', 'max:30', 'confirmed'],
            'application_language' => ['string'],
        ];
    }
}
