<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "name" => $this->name,
            "description" => $this->description,
            "price" => $this->price,
            "category_id" => $this->category_id,
            "image_path" => asset("storage/products/".$this->image_path),
            "product_galleries" => ProductGalleryResource::collection($this->product_galleries),
            "product_variants" => $this->product_variants
        ];
    }
}
