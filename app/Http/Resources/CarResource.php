<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
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
            'model' => $this->model,
            'company' => new CarCompaniesResource($this->company_ob),
            'register_no' => $this->register_no,
            'register_start' => $this->register_start ? $this->register_start->format('Y-m-d') : $this->register_start,
            'register_end' => $this->register_end ? $this->register_end->format('Y-m-d') : $this->register_end,
            'partner_id' => $this->partner_id,
            'cost' => $this->cost,
            'price' => $this->price,
            'status' => $this->status,
            'image' => $this->image,
            'active_reservations' => $this->active_reservations,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d h:i A') : null,
        ];
    }
}
