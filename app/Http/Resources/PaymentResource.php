<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'order_id' => $this->order_id,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'paid_at' => $this->paid_at,
            'note' => $this->note,
            'type' => $this->type,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d h:i A') : null,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d h:i A') : null,
        ];
    }
}
