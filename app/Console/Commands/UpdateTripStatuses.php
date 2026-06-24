<?php

namespace App\Console\Commands;

use App\Enums\TripStatus;
use App\Models\Trip;
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
        return Trip::query()
            ->planned()
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->update(['status' => TripStatus::PROGRESS->value]);
    }

    private function handleCompletedTrips(Carbon $date): int
    {
        return Trip::query()
            ->whereIn('status', [TripStatus::PLANNED->value, TripStatus::PROGRESS->value])
            ->whereDate('end_date', '<', $date)
            ->update(['status' => TripStatus::COMPLETED->value]);
    }
}
