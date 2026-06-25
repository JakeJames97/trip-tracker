<?php

namespace App\Services;

use App\Enums\TripStatus;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

class TripService
{
    /**
     * @throws Throwable
     */
    public function clone(Trip $trip, User $user): Trip
    {
        $trip->load('destinations.tasks');

        return DB::transaction(static function () use ($trip, $user) {
            $newTrip = $user->trips()->create([
                'name' => "{$trip->name} (copy)",
                'description' => $trip->description,
                'start_date' => $trip->start_date,
                'end_date' => $trip->end_date,
                'status' => TripStatus::PLANNED,
                'is_public' => false,
            ]);

            foreach ($trip->destinations as $destination) {
                $newDestination = $newTrip->destinations()->create([
                    'city' => $destination->city,
                    'country_code' => $destination->country_code,
                    'budget' => $destination->budget,
                    'arrival_date' => $destination->arrival_date,
                    'departure_date' => $destination->departure_date,
                ]);

                foreach ($destination->tasks as $task) {
                    $newDestination->tasks()->create([
                        'title' => $task->title,
                        'is_completed' => false,
                    ]);
                }
            }

            return $newTrip;
        });
    }
}
