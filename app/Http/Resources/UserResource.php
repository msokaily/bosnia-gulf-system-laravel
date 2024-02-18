<?php

namespace App\Http\Resources;

use Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'abilities' => Helper::permissions($this->role),
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d h:i A') : null,
        ];
    }
}
