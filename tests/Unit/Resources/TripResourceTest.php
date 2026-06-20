<?php

namespace Tests\Unit\Resources;

use App\Enums\TripStatus;
use App\Http\Resources\TripResource;
use App\Models\Destination;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class TripResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_the_expected_shape(): void
    {
        $trip = Trip::factory()->for(User::factory())->create([
            'name' => 'Japan 2026',
            'status' => TripStatus::PLANNED,
        ]);

        $array = new TripResource($trip)->toArray(Request::create('/'));

        $this->assertSame($trip->id, $array['id']);
        $this->assertSame('Japan 2026', $array['name']);
        $this->assertSame($trip->start_date->toDateString(), $array['start_date']);
        $this->assertSame($trip->end_date->toDateString(), $array['end_date']);
        $this->assertSame(TripStatus::PLANNED, $array['status']);
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
}
