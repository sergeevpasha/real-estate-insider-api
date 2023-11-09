<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * @property string $email
 * @property string $password
 * @property bool|null $trust_device
 */
class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guest();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email'        => ['required', 'string', 'email', 'max:255'],
            'password'     => ['required', 'string', 'min:8'],
            'trust_device' => ['boolean']
        ];
    }
}
