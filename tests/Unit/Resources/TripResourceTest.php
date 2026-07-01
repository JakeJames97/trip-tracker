<?php

namespace Tests\Unit\Resources;

use App\Enums\TripStatus;
use App\Http\Resources\TripResource;
use App\Models\Destination;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TripResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_the_expected_shape(): void
    {
        $trip = Trip::factory()->for(User::factory())->create([
            'name' => 'Japan 2026',
            'status' => TripStatus::PLANNED,
            'is_public' => true,
        ]);

        $array = new TripResource($trip)->toArray(Request::create('/'));

        $this->assertSame($trip->id, $array['id']);
        $this->assertSame('Japan 2026', $array['name']);
        $this->assertSame($trip->start_date->toDateString(), $array['start_date']);
        $this->assertSame($trip->end_date->toDateString(), $array['end_date']);
        $this->assertSame(TripStatus::PLANNED, $array['status']);
        $this->assertTrue($array['is_public']);
    }

    public function test_it_includes_destinations_when_loaded(): void
    {
        $trip = Trip::factory()->for(User::factory())->create();
        Destination::factory()->count(2)->for($trip)->create();
        $trip->load('destinations');

        $array = new TripResource($trip)->toArray(Request::create('/'));

        $this->assertCount(2, $array['destinations']);
    }

    public function test_it_includes_destinations_count_when_counted(): void
    {
        $trip = Trip::factory()->for(User::factory())->create();
        Destination::factory()->count(3)->for($trip)->create();
        $trip->loadCount('destinations');

        $array = new TripResource($trip)->toArray(Request::create('/'));

        $this->assertSame(3, $array['destinations_count']);
    }

    public function test_it_includes_likes_count_when_counted(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $trip = Trip::factory()->for($user)->create();

        $user->likedTrips()->attach($trip);
        $user2->likedTrips()->attach($trip);

        $trip->loadCount('likes');

        $request = request();
        $request->setUserResolver(fn () => $user);

        $array = new TripResource($trip)->toArray($request);

        $this->assertSame(2, $array['likes_count']);
        $this->assertTrue($array['is_liked']);
    }

    public function test_it_returns_true_for_is_owner_when_user_is_owner(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $trip = Trip::factory()->create([
            'user_id' => $user->id,
        ]);

        $request = request();
        $request->setUserResolver(fn () => $user);

        $array = new TripResource($trip)->toArray($request);

        $this->assertTrue($array['is_owner']);
    }

    public function test_it_returns_false_for_is_owner_when_user_is_not_owner(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $trip = Trip::factory()->for(User::factory())->create();

        $request = request();
        $request->setUserResolver(fn () => $user);

        $array = new TripResource($trip)->toArray($request);

        $this->assertFalse($array['is_owner']);
    }

    public function test_it_sums_destination_budgets(): void
    {
        $trip = Trip::factory()->for(User::factory())->create();
        Destination::factory()->for($trip)->create(['budget' => 2000]);
        Destination::factory()->for($trip)->create(['budget' => 3500]);
        Destination::factory()->for($trip)->create(['budget' => 1000]);
        $trip->load('destinations');

        $array = new TripResource($trip)->toArray(Request::create('/'));

        $array = $array[0]->data;

        $this->assertSame(6500, $array['budget']);
        $this->assertSame('£6,500.00', $array['budget_formatted']);
    }

    public function test_it_returns_zero_budget_when_no_destinations(): void
    {
        $trip = Trip::factory()->for(User::factory())->create();
        $trip->load('destinations');

        $array = new TripResource($trip)->toArray(Request::create('/'));

        $array = $array[0]->data;

        $this->assertArrayHasKey('budget', $array);
        $this->assertSame(0, $array['budget']);
        $this->assertSame('£0.00', $array['budget_formatted']);
    }
}
