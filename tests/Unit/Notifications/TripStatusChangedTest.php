<?php

namespace Tests\Unit\Notifications;

use App\Enums\TripStatus;
use App\Models\Trip;
use App\Models\User;
use App\Notifications\TripStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TripStatusChangedTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_stores_the_expected_data(): void
    {
        $owner = User::factory()->create();
        $trip = Trip::factory()->for($owner)->create(['name' => 'Japan 2026']);

        $data = new TripStatusChanged($trip, TripStatus::PROGRESS)->toArray($owner);

        $this->assertSame('trip_status_changed', $data['type']);
        $this->assertSame($trip->id, $data['trip_id']);
        $this->assertSame('Japan 2026', $data['trip_name']);
        $this->assertSame(TripStatus::PROGRESS->value, $data['status']);
        $this->assertSame("Your trip “{$trip->name}” is now in progress", $data['message']);
    }

    #[Test]
    public function it_describes_the_completed_transition(): void
    {
        $trip = Trip::factory()->make(['name' => 'Japan']);

        $data = new TripStatusChanged($trip, TripStatus::COMPLETED)->toArray(new User);

        $this->assertSame("Your trip “{$trip->name}” is now complete", $data['message']);
    }
}
