<?php

namespace Tests\Feature\Trips;

use App\Models\Trip;
use App\Models\User;
use App\Notifications\TripLiked;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ToggleLikesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
    }

    #[Test]
    public function it_likes_a_trip(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();

        $trip = Trip::factory()->for($owner)->create();

        $trip->likes()->attach(User::factory()->count(2)->create());

        Sanctum::actingAs($user);

        $this->postJson("/api/trips/{$trip->id}/like")
            ->assertOk()
            ->assertJson([
                'data' => [
                    'is_liked' => true,
                    'likes_count' => 3,
                ],
            ]);

        $this->assertDatabaseHas('trip_likes', [
            'user_id' => $user->id,
            'trip_id' => $trip->id,
        ]);

        Notification::assertSentTo(
            $owner,
            TripLiked::class,
            fn ($notification) => $notification->trip->id === $trip->id
                && $notification->liker->id === $user->id,
        );
    }

    #[Test]
    public function it_unlikes_a_trip_it_already_liked(): void
    {
        $user = User::factory()->create();
        $trip = Trip::factory()->for(User::factory())->create();
        $user->likedTrips()->attach($trip);

        Sanctum::actingAs($user);

        $this->postJson("/api/trips/{$trip->id}/like")
            ->assertOk()
            ->assertJson([
                'data' => [
                    'is_liked' => false,
                    'likes_count' => 0,
                ],
            ]);

        $this->assertDatabaseMissing('trip_likes', [
            'user_id' => $user->id,
            'trip_id' => $trip->id,
        ]);
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $trip = Trip::factory()->for(User::factory())->create();

        $this->postJson("/api/trips/{$trip->id}/like")->assertUnauthorized();

        $this->assertDatabaseMissing('trip_likes', ['trip_id' => $trip->id]);
    }
}
