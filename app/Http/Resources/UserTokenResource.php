<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserTokenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "access_token" => $this->resource,
            "token_type" => 'Bearer',
            'expired_in' => config('jwt.ttl') * 60
        ];
    }
}
