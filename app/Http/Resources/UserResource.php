<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'profile_picture' => $this->profile_picture != null ? asset('storage/images/'.$this->profile_picture) : null,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at
        ];
    }
}
