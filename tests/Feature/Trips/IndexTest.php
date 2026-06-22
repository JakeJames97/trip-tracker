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

class IndexTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_only_the_authenticated_users_trips(): void
    {
        $user = User::factory()->create();

        $withDestinations = Trip::factory()->for($user)->create(['start_date' => '2026-12-01']);
        Trip::factory()->for($user)->create(['start_date' => '2026-01-01']);
        Destination::factory()->count(4)->for($withDestinations)->create();

        Trip::factory()->count(3)->create();

        Sanctum::actingAs($user);

        $this->getJson('/api/trips')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.destinations_count', 4);
    }

    #[Test]
    public function it_returns_pagination_as_expected(): void
    {
        $user = User::factory()->create();

        Trip::factory()->times(20)->create([
            'user_id' => $user->id,
        ]);

        Trip::factory()->count(3)->create();

        Sanctum::actingAs($user);

        $this->getJson('/api/trips?page=2')
            ->assertOk()
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonPath('meta.last_page', 2)
            ->assertJsonPath('meta.total', 20);
    }

    #[Test]
    public function it_returns_pagination_and_status_filtering_as_expected(): void
    {
        $user = User::factory()->create();

        Trip::factory()->times(30)->create([
            'user_id' => $user->id,
            'status' => TripStatus::COMPLETED,
        ]);

        Trip::factory()->times(20)->create([
            'user_id' => $user->id,
            'status' => TripStatus::PLANNED,
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/trips?page=2&status=' . TripStatus::COMPLETED->value)
            ->assertOk()
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonPath('meta.last_page', 3)
            ->assertJsonPath('meta.total', 30);
    }

    #[Test]
    public function it_returns_empty_trips(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->getJson('/api/trips')
            ->assertOk()
            ->assertJson([
                'data' => [],
            ]);
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $this->getJson('/api/trips')->assertUnauthorized();
    }
}
