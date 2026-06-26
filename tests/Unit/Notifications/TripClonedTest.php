<?php

namespace Tests\Unit\Notifications;

use App\Models\Trip;
use App\Models\User;
use App\Notifications\TripCloned;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TripClonedTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_stores_the_expected_data(): void
    {
        $owner = User::factory()->create();
        $cloner = User::factory()->create(['username' => 'test']);
        $trip = Trip::factory()->for($owner)->create(['name' => 'Italy Trip']);

        $data = new TripCloned($trip, $cloner)->toArray($owner);

        $this->assertSame('trip_cloned', $data['type']);
        $this->assertSame($trip->id, $data['trip_id']);
        $this->assertSame('Italy Trip', $data['trip_name']);
        $this->assertSame("{$cloner->username} cloned your trip “{$trip->name}”", $data['message']);
    }
}
