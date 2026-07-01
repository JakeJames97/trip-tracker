<?php

namespace App\Http\Resources;

use Akaunting\Money\Money;
use App\Models\Trip;
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
            'is_liked' => $this->when($request->user() !== null, fn () => $this->resource->likes->contains($request->user()->id)),
            'likes_count' => $this->whenCounted('likes'),
            'destinations_count' => $this->whenCounted('destinations'),
            'destinations' => DestinationResource::collection($this->whenLoaded('destinations')),
            $this->mergeWhen($this->resource->relationLoaded('destinations'), fn () => $this->budgetData($this->resource)),
        ];
    }

    private function budgetData(Trip $trip): array
    {
        $total = $trip->destinations->sum(fn ($destination) => $destination->budget);

        return [
            'budget' => $total,
            'budget_formatted' => Money::GBP((int) round($total) * 100)->format(),
        ];
    }
}
