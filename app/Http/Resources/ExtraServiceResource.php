<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExtraServiceResource extends JsonResource
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
            'price' => $this->price,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d h:i A') : null,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d h:i A') : null,
            'deleted_at' => $this->deleted_at ? $this->deleted_at->format('Y-m-d h:i A') : null,
        ];
    }
}
