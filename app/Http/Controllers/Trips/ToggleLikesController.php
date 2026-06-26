<?php

namespace App\Http\Controllers\Trips;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Notifications\TripLiked;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ToggleLikesController extends Controller
{
    public function __invoke(Request $request, Trip $trip): JsonResponse
    {
        $user = $request->user();

        $user->likedTrips()->toggle($trip->id);

        $isLiked = $user->likedTrips()->where('trip_id', $trip->id)->exists();

        if ($isLiked && $trip->user_id !== $user->id) {
            $trip->user->notify(new TripLiked($trip, $user));
        }

        return response()->json([
            'data' => [
                'is_liked' => $isLiked,
                'likes_count' => $trip->likes()->count(),
            ],
        ]);
    }
}
