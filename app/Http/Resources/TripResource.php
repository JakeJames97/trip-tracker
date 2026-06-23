<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'start_date' => $this->resource->start_date?->toDateString(),
            'end_date' => $this->resource->end_date?->toDateString(),
            'status' => $this->resource->status,
            'is_public' => $this->resource->is_public,
            'is_owner' => $request->user()?->id === $this->resource->user_id,
            'created_at' => $this->resource->created_at->toDateTimeString(),
            'user' => $this->whenLoaded('user', fn () => [
                'username' => $this->resource->user->username,
            ]),
            'destinations_count' => $this->whenCounted('destinations'),
            'destinations' => DestinationResource::collection($this->whenLoaded('destinations')),
        ];
    }
}
