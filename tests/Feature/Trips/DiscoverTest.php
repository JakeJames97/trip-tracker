<?php

namespace Tests\Feature\Trips;

use App\Enums\TripStatus;
use App\Models\Destination;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DiscoverTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_only_public_trips(): void
    {
        $user = User::factory()->create();

        $publicTrip = Trip::factory()->create([
            'status' => TripStatus::PLANNED,
            'is_public' => true,
        ]);

        Trip::factory()->create([
            'status' => TripStatus::PLANNED,
            'is_public' => false,
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/discover')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $publicTrip->id);
    }

    #[Test]
    public function it_returns_destinations_count(): void
    {
        $user = User::factory()->create();

        $trip = Trip::factory()->create([
            'is_public' => true,
        ]);

        Destination::factory()->count(5)->for($trip)->create();

        Sanctum::actingAs($user);

        $this->getJson('/api/discover')
            ->assertOk()
            ->assertJsonPath('data.0.destinations_count', 5);
    }

    #[Test]
    public function it_returns_pagination_and_status_filtering_as_expected(): void
    {
        $user = User::factory()->create();

        Trip::factory()->times(30)->create([
            'user_id' => $user->id,
            'status' => TripStatus::COMPLETED,
            'is_public' => true,
        ]);

        Trip::factory()->times(20)->create([
            'user_id' => $user->id,
            'status' => TripStatus::PLANNED,
            'is_public' => true,
        ]);

        Trip::factory()->times(20)->create([
            'user_id' => $user->id,
            'status' => TripStatus::COMPLETED,
            'is_public' => false,
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/discover?page=2&status=' . TripStatus::COMPLETED->value)
            ->assertOk()
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonPath('meta.last_page', 3)
            ->assertJsonPath('meta.total', 30);
    }

    #[Test]
    public function it_filters_trips_by_country_code(): void
    {
        $user = User::factory()->create();

        $franceTrip = Trip::factory()->create([
            'is_public' => true,
        ]);

        Destination::factory()->for($franceTrip)->create([
            'country_code' => 'FR',
        ]);

        $spainTrip = Trip::factory()->create([
            'is_public' => true,
        ]);

        Destination::factory()->for($spainTrip)->create([
            'country_code' => 'ES',
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/discover?country=FR')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $franceTrip->id);
    }

    #[Test]
    public function it_searches_trips_by_name(): void
    {
        $user = User::factory()->create();

        $match = Trip::factory()->create([
            'is_public' => true,
            'name' => 'Tokyo Adventure',
        ]);

        Trip::factory()->create([
            'is_public' => true,
            'name' => 'Paris Getaway',
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/discover?search=tokyo')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $match->id);
    }

    #[Test]
    public function it_searches_trips_by_destination_city(): void
    {
        $user = User::factory()->create();

        $match = Trip::factory()->create([
            'is_public' => true,
            'name' => 'Summer Trip',
        ]);
        Destination::factory()->for($match)->create(['city' => 'Kyoto']);

        $other = Trip::factory()->create(['is_public' => true, 'name' => 'Winter Trip']);
        Destination::factory()->for($other)->create(['city' => 'Berlin']);

        Sanctum::actingAs($user);

        $this->getJson('/api/discover?search=kyoto')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $match->id);
    }

    #[Test]
    public function it_searches_trips_by_destination_country_code(): void
    {
        $user = User::factory()->create();

        $match = Trip::factory()->create(['is_public' => true, 'name' => 'A trip']);
        Destination::factory()->for($match)->create(['country_code' => 'JP']);

        $other = Trip::factory()->create(['is_public' => true, 'name' => 'Another trip']);
        Destination::factory()->for($other)->create(['country_code' => 'DE']);

        Sanctum::actingAs($user);

        $this->getJson('/api/discover?search=JP')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $match->id);
    }

    #[Test]
    public function it_returns_empty_when_no_public_trips_exist(): void
    {
        $user = User::factory()->create();

        Trip::factory()->count(5)->create([
            'is_public' => false,
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/discover')
            ->assertOk()
            ->assertJson([
                'data' => [],
            ]);
    }
}
