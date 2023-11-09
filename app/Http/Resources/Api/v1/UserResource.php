<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int $id
 * @property string $email
 * @property string $application_language
 * @property ?int $github_id
 * @property ?string google_id
 * @property ?string $first_name
 * @property ?string $last_name
 * @property ?string $middle_name
 * @property bool password_not_set
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'email'                => $this->email,
            'application_language' => $this->application_language,
            'github_id'            => $this->github_id,
            'google_id'            => $this->google_id,
            'first_name'           => (string) $this->first_name,
            'last_name'            => (string) $this->last_name,
            'middle_name'          => (string) $this->middle_name,
            'password_not_set'     => $this->password_not_set
        ];
    }
}
