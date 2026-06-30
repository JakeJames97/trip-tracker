<?php

namespace Tests\Feature;

use App\Enums\TripStatus;
use App\Models\Destination;
use App\Models\Task;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_the_correct_stats_for_the_user(): void
    {
        $user = User::factory()->create();

        $planned = Trip::factory()->count(2)->for($user)->create([
            'status' => TripStatus::PLANNED,
        ]);
        $completed = Trip::factory()->for($user)->create([
            'status' => TripStatus::COMPLETED,
        ]);

        $destinationPlanned = Destination::factory()->for($planned[0])->create(['country_code' => 'FR']);
        Destination::factory()->for($planned[0])->create(['country_code' => 'FR']);
        Destination::factory()->for($planned[1])->create(['country_code' => 'JP']);
        $destinationCompleted = Destination::factory()->count(2)->for($completed)->create(['country_code' => 'ES']);

        User::factory()->count(3)->create()->each(
            fn ($liker) => $planned[0]->likes()->attach($liker)
        );

        Task::factory()->count(5)->for($destinationPlanned)->create();
        Task::factory()->count(3)->for($destinationCompleted[0])->create();

        Sanctum::actingAs($user);

        $this->getJson('/api/dashboard')
            ->assertOk()
            ->assertJsonPath('data.stats', [
                'total_trips' => 3,
                'total_destinations_planned' => 3,
                'countries' => [
                    'ES',
                    'FR',
                    'JP',
                ],
                'likes_received' => 3,
                'tasks_to_do' => 5,
            ]);
    }

    #[Test]
    public function it_returns_the_soonest_upcoming_trip_as_next_trip(): void
    {
        $user = User::factory()->create();

        Trip::factory()->for($user)->create(['start_date' => now()->addDays(30)]);
        $soonest = Trip::factory()->for($user)->create(['start_date' => now()->addDays(5)]);

        Sanctum::actingAs($user);

        $this->getJson('/api/dashboard')
            ->assertJsonPath('data.next_trip.id', $soonest->id);
    }

    #[Test]
    public function it_returns_null_next_trip_when_none_upcoming(): void
    {
        $user = User::factory()->create();
        Trip::factory()->for($user)->create(['start_date' => now()->subDays(5)]);

        Sanctum::actingAs($user);

        $this->getJson('/api/dashboard')
            ->assertJsonPath('data.next_trip', null);
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $this->getJson('/api/dashboard')->assertUnauthorized();
    }
}
