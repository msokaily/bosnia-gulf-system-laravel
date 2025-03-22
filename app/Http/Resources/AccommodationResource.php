<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccommodationResource extends JsonResource
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
            'partner_id' => $this->partner_id,
            'type' => $this->type,
            'address' => $this->address,
            'location' => $this->location,
            'status' => $this->status,
            'image' => $this->image,
            'cost' => $this->cost,
            'price' => $this->price,
            'multiple' => $this->multiple,
            'active_reservations' => $this->active_reservations,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d h:i A') : null,
        ];
    }
}
