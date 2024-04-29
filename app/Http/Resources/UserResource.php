<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $resource = [
            'id' => $this->resource->id,
            'first_name' => $this->resource->first_name,
            'last_name' => $this->resource->last_name,
            'phone_number' => $this->resource->phone_number,
            'email' => $this->resource->email,
        ];

        if ($this->resource->relationLoaded('roles') && !empty($this->resource->roles) && isset($this->resource->roles[0])) {
            $resource['role'] = RoleResource::make($this->resource->roles[0]);
        }

        return array_merge($resource, [
            'images' => $this->whenLoaded('images'),
            'email_verified_at' => $this->resource->email_verified_at,
            'deleted_at' => $this->resource->deleted_at,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ]);
    }
}
