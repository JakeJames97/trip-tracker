<?php

namespace Tests\Feature\Trips;

use App\Models\Destination;
use App\Models\Trip;
use App\Models\User;
use App\Notifications\TripCloned;
use App\Services\TripService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

class CloneTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
    }

    #[Test]
    public function it_clones_a_public_trip(): void
    {
        $owner = User::factory()->create();
        $trip = Trip::factory()->for($owner)->create(['is_public' => true, 'name' => 'Japan 2026']);
        Destination::factory()->count(2)->for($trip)->create();

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson("/api/trips/{$trip->id}/clone")
            ->assertCreated()
            ->assertJsonPath('data.name', 'Japan 2026 (copy)')
            ->assertJsonPath('data.is_owner', true)
            ->assertJsonCount(2, 'data.destinations');

        $this->assertDatabaseHas('trips', [
            'name' => 'Japan 2026 (copy)',
            'user_id' => $user->id,
        ]);

        Notification::assertSentTo(
            $owner,
            TripCloned::class,
            fn ($notification) => $notification->cloner->id === $user->id,
        );
    }

    #[Test]
    public function it_forbids_cloning_another_users_private_trip(): void
    {
        $trip = Trip::factory()->for(User::factory())->create(['is_public' => false]);

        Sanctum::actingAs(User::factory()->create());

        $this->postJson("/api/trips/{$trip->id}/clone")->assertForbidden();

        $this->assertDatabaseMissing('trips', ['name' => "{$trip->name} (copy)"]);
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $trip = Trip::factory()->for(User::factory())->create(['is_public' => true]);

        $this->postJson("/api/trips/{$trip->id}/clone")->assertUnauthorized();
    }

    #[Test]
    public function it_handles_a_failure_during_cloning(): void
    {
        $owner = User::factory()->create();
        $trip = Trip::factory()->for($owner)->create(['is_public' => true]);

        $cloner = User::factory()->create();
        Sanctum::actingAs($cloner);

        $this->mock(TripService::class, function ($mock) {
            $mock->shouldReceive('clone')
                ->once()
                ->andThrow(new RuntimeException('Simulated clone failure'));
        });

        $response = $this->postJson("/api/trips/{$trip->id}/clone");

        $response->assertInternalServerError();

        $this->assertDatabaseMissing('trips', ['name' => "{$trip->name} (copy)"]);
    }
}
