<?php

namespace Tests\Unit\Commands;

use App\Enums\TripStatus;
use App\Models\Trip;
use App\Models\User;
use App\Notifications\TripStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateTripStatusesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
    }

    #[Test]
    public function it_marks_a_started_trip_in_progress(): void
    {
        $owner = User::factory()->create();
        $trip = Trip::factory()->for($owner)->create([
            'status' => TripStatus::PLANNED,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay(),
        ]);

        $this->artisan('trips:update-statuses')->assertSuccessful();

        $this->assertDatabaseHas('trips', [
            'id' => $trip->id,
            'status' => TripStatus::PROGRESS,
        ]);

        Notification::assertSentTo(
            $owner,
            TripStatusChanged::class,
            fn ($notification) => $notification->trip->id === $trip->id
                && $notification->status === TripStatus::PROGRESS,
        );
    }

    #[Test]
    public function it_marks_an_ended_trip_completed(): void
    {
        $owner = User::factory()->create();
        $trip = Trip::factory()->for($owner)->create([
            'status' => TripStatus::PROGRESS,
            'start_date' => now()->subDays(5),
            'end_date' => now()->subDay(),
        ]);

        $this->artisan('trips:update-statuses')->assertSuccessful();

        $this->assertDatabaseHas('trips', [
            'id' => $trip->id,
            'status' => TripStatus::COMPLETED,
        ]);

        Notification::assertSentTo(
            $owner,
            TripStatusChanged::class,
            fn ($notification) => $notification->trip->id === $trip->id
                && $notification->status === TripStatus::COMPLETED,
        );
    }

    #[Test]
    public function it_leaves_a_future_trip_planned(): void
    {
        $trip = Trip::factory()->for(User::factory())->create([
            'status' => TripStatus::PLANNED,
            'start_date' => now()->addWeek(),
            'end_date' => now()->addWeeks(2),
        ]);

        $this->artisan('trips:update-statuses')->assertSuccessful();

        $this->assertDatabaseHas('trips', [
            'id' => $trip->id,
            'status' => TripStatus::PLANNED,
        ]);

        Notification::assertNothingSent();
    }

    #[Test]
    public function it_completes_a_planned_trip_whose_end_date_already_passed(): void
    {
        $owner = User::factory()->create();
        $trip = Trip::factory()->for($owner)->create([
            'status' => TripStatus::PLANNED,
            'start_date' => now()->subDays(3),
            'end_date' => now()->subDays(2),
        ]);

        $this->artisan('trips:update-statuses')->assertSuccessful();

        $this->assertDatabaseHas('trips', [
            'id' => $trip->id,
            'status' => TripStatus::COMPLETED,
        ]);

        Notification::assertSentTo(
            $owner,
            TripStatusChanged::class,
            fn ($notification) => $notification->trip->id === $trip->id
                && $notification->status === TripStatus::COMPLETED,
        );
    }

    #[Test]
    public function it_handles_multiple_transitions_in_one_run_and_reports_counts(): void
    {
        Trip::factory()->for(User::factory())->create([
            'status' => TripStatus::PLANNED,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay(),
        ]);

        Trip::factory()->for(User::factory())->create([
            'status' => TripStatus::PLANNED,
            'start_date' => now(),
            'end_date' => now()->addDays(2),
        ]);

        Trip::factory()->for(User::factory())->create([
            'status' => TripStatus::PROGRESS,
            'start_date' => now()->subDays(5),
            'end_date' => now()->subDay(),
        ]);

        Trip::factory()->for(User::factory())->create([
            'status' => TripStatus::PROGRESS,
            'start_date' => now()->subWeek(),
            'end_date' => now()->subDays(2),
        ]);

        Trip::factory()->for(User::factory())->create([
            'status' => TripStatus::PROGRESS,
            'start_date' => now()->subWeeks(2),
            'end_date' => now()->subDays(5),
        ]);

        Trip::factory()->for(User::factory())->create([
            'status' => TripStatus::PLANNED,
            'start_date' => now()->addWeek(),
            'end_date' => now()->addWeeks(2),
        ]);

        $this->artisan('trips:update-statuses')
            ->expectsOutputToContain('Updated 2 to in progress, 3 to completed.')
            ->assertSuccessful();

        $this->assertSame(2, Trip::where('status', TripStatus::PROGRESS)->count());
        $this->assertSame(3, Trip::where('status', TripStatus::COMPLETED)->count());
        $this->assertSame(1, Trip::where('status', TripStatus::PLANNED)->count());

        Notification::assertSentTimes(TripStatusChanged::class, 5);
    }
}
