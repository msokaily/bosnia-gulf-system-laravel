<?php

namespace App\Http\Resources;

use Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogsResource extends JsonResource
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
            'user' => $this->user,
            'order' => $this->order,
            'item' => $this->item,
            'item_product' => $this->item->product ?? null,
            'data' => $this->data,
            'type' => $this->type,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
