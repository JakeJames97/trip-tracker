<?php

namespace App\Http\Controllers\Trips;

use App\Exceptions\TripException;
use App\Http\Controllers\Controller;
use App\Http\Resources\TripResource;
use App\Models\Trip;
use App\Notifications\TripCloned;
use App\Services\TripService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CloneController extends Controller
{
    public function __invoke(Request $request, Trip $trip, TripService $service): JsonResponse
    {
        $user = $request->user();
        if ($user->cannot('clone', $trip)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        try {
            $clone = $service->clone($trip, $user);
        } catch (Throwable $exception) {
            Log::error('An exception was thrown when attempting to clone a trip', [
                'exception' => $exception,
            ]);

            throw new TripException('Unexpected error fetching when cloning trip');
        }

        if ($trip->user_id !== $user->id) {
            $trip->user->notify(new TripCloned($trip, $user));
        }

        $clone->load('destinations.tasks', 'destinations.country');

        return new TripResource($clone)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
