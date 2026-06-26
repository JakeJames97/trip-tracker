<?php

namespace Tests\Unit\Notifications;

use App\Models\Trip;
use App\Models\User;
use App\Notifications\TripLiked;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TripLikedTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_stores_the_expected_data(): void
    {
        $owner = User::factory()->create();
        $liker = User::factory()->create(['username' => 'test']);
        $trip = Trip::factory()->for($owner)->create(['name' => 'Japan 2026']);

        $data = new TripLiked($trip, $liker)->toArray($owner);

        $this->assertSame('trip_liked', $data['type']);
        $this->assertSame($trip->id, $data['trip_id']);
        $this->assertSame('Japan 2026', $data['trip_name']);
        $this->assertSame("{$liker->username} liked your trip “{$trip->name}”", $data['message']);
    }
}
