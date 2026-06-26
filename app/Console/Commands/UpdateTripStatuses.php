<?php

namespace App\Console\Commands;

use App\Enums\TripStatus;
use App\Models\Trip;
use App\Notifications\TripStatusChanged;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class UpdateTripStatuses extends Command
{
    protected $signature = 'trips:update-statuses';

    protected $description = 'Update trip statuses based on their start and end dates';

    public function handle(): int
    {
        $date = Carbon::today();

        $startedCount = $this->handleStartedTrips($date);

        $completedCount = $this->handleCompletedTrips($date);

        $this->info("Updated {$startedCount} to in progress, {$completedCount} to completed.");

        return self::SUCCESS;
    }

    private function handleStartedTrips(Carbon $date): int
    {
        $trips = Trip::query()
            ->with('user')
            ->planned()
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->get();

        foreach ($trips as $trip) {
            $trip->user->notify(new TripStatusChanged($trip, TripStatus::PROGRESS));
        }

        Trip::whereIn('id', $trips->pluck('id'))->update(['status' => TripStatus::PROGRESS->value]);

        return $trips->count();
    }

    private function handleCompletedTrips(Carbon $date): int
    {
        $trips = Trip::query()
            ->with('user')
            ->whereIn('status', [TripStatus::PLANNED->value, TripStatus::PROGRESS->value])
            ->whereDate('end_date', '<', $date)
            ->get();

        foreach ($trips as $trip) {
            $trip->user->notify(new TripStatusChanged($trip, TripStatus::COMPLETED));
        }

        Trip::whereIn('id', $trips->pluck('id'))->update(['status' => TripStatus::COMPLETED->value]);

        return $trips->count();
    }
}
