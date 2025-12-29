<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\User
 */
class UserSharedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * Only expose data that should be globally available across all pages.
     * Sensitive data like email should only be exposed on specific pages.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'role' => $this->role->value,
            'role_label' => $this->role->label(),
            // Email is NOT exposed globally for security reasons
            // Include it only on specific pages that need it (e.g., profile settings)
        ];
    }
}
