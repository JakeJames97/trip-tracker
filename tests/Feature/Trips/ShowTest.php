<?php

namespace Tests\Feature\Trips;

use App\Models\Destination;
use App\Models\Task;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_an_owned_trip_with_nested_destinations_and_tasks(): void
    {
        $user = User::factory()->create();
        $trip = Trip::factory()->for($user)->create();
        $destination = Destination::factory()->for($trip)->create();
        Task::factory()->count(2)->for($destination)->create();

        Sanctum::actingAs($user);

        $this->getJson("/api/trips/{$trip->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $trip->id)
            ->assertJsonCount(1, 'data.destinations')
            ->assertJsonCount(2, 'data.destinations.0.tasks');
    }
}
