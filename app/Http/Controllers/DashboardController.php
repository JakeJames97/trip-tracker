<?php

namespace App\Http\Controllers;

use App\Enums\TripStatus;
use App\Http\Resources\TripResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        $stats = [
            'total_trips' => $user->trips()->count(),
            'total_destinations_planned' => $user->destinations()
                ->where('trips.status', '!=', TripStatus::COMPLETED)
                ->count(),
            'countries' => $user->destinations()->distinct('country_code')->pluck('country_code')->all(),
            'likes_received' => $user->trips()
                ->withCount('likes')
                ->get()
                ->sum('likes_count'),
            'tasks_to_do' => $user->tasks()
                ->where('tasks.is_completed', false)
                ->where('trips.status', '!=', TripStatus::COMPLETED)
                ->count(),
        ];

        $nextTrip = $user->trips()
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->with(['destinations.country'])
            ->first();

        return response()->json([
            'data' => [
                'stats' => $stats,
                'next_trip' => $nextTrip ? new TripResource($nextTrip) : null,
            ],
        ]);
    }
}
