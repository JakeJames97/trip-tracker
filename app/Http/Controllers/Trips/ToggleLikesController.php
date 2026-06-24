<?php

namespace App\Http\Controllers\Trips;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ToggleLikesController extends Controller
{
    public function __invoke(Request $request, Trip $trip): JsonResponse
    {
        $request->user()->likedTrips()->toggle($trip->id);

        return response()->json([
            'data' => [
                'is_liked' => $request->user()->likedTrips()->where('trip_id', $trip->id)->exists(),
                'likes_count' => $trip->likes()->count(),
            ],
        ]);
    }
}
