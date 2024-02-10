<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "cart_item_id" => $this->id,
            "quantity" => $this->quantity,
            "total_price" => $this->price,
            "product_variants" => new ProductVariantResource($this->whenLoaded('product_variant')),
        ];
    }
}
