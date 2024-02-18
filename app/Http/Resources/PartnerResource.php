<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartnerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'contact_name' => $this->contact_name,
            'phone' => $this->phone,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d h:i A') : null,
        ];
    }
}
