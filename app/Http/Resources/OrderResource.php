<?php

namespace App\Http\Resources;

use Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'user_id' => $this->user_id,
            'name' => $this->name,
            'phone' => $this->phone,
            'status' => $this->status,
            'status_name' => Helper::orderStatusName($this->status),
            'cost' => $this->cost,
            'price' => $this->price,
            'total' => $this->total,
            'total_special' => $this->total_special,
            'products' => OrderProductResource::collection($this->products),
            'payments' => $this->payments,
            'arrive_at' => $this->arrive_at,
            'leave_at' => $this->leave_at,
            'paid_at' => $this->paid_at,
            'arrive_time' => $this->arrive_time,
            'airline' => $this->airline,
            'logs' => $this->logs,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d h:i A') : null,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d h:i A') : null,
        ];
    }
}
