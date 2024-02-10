<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "cart_id" => $this->id,
            "user_id" => $this->user_id,
            "cart_items" => CartItemResource::collection($this->whenLoaded('cart_items')),
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at
        ];
    }
}
