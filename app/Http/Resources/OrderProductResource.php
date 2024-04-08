<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductResource extends JsonResource
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
            'type' => $this->type,
            'order_id' => $this->order_id,
            'item_id' => $this->item_id,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'note' => $this->note,
            'cost' => $this->cost,
            'price' => $this->price,
            'total' => $this->total,
            'product' => $this->product,
            'extra' => $this->extra,
            'extra_value' => $this->extraValue,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d h:i A') : null,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d h:i A') : null,
        ];
    }
}
