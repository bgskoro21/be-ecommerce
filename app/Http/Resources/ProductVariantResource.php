<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "product_variant_id" => $this->id,
            "product" => new ProductResource($this->whenLoaded('product')),
            "size" => $this->size,
            "stock" => $this->stock,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at
        ];
    }
}
