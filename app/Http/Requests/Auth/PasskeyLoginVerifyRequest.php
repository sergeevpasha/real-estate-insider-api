<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $email
 */
class PasskeyLoginVerifyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'id'                         => ['required', 'string'],
            'rawId'                      => ['required', 'string'],
            'response'                   => ['required', 'array'],
            'response.attestationObject' => ['required', 'string'],
            'response.clientDataJSON'    => ['required', 'string'],
            'response.signature'         => ['required', 'string'],
            'response.userHandle'        => ['required', 'string'],
            'type'                       => ['required', 'string'],
            'clientExtensionResults'     => ['array'],
            'authenticatorAttachment'    => ['required', 'string'],
        ];
    }
}
