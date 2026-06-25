<?php

namespace Tests\Unit\Services;

use App\Enums\TripStatus;
use App\Models\Destination;
use App\Models\Task;
use App\Models\Trip;
use App\Models\User;
use App\Services\TripService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

class TripServiceTest extends TestCase
{
    use RefreshDatabase;

    private TripService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TripService;
    }

    #[Test]
    public function it_assigns_the_clone_to_the_given_user_with_the_expected_data(): void
    {
        $original = Trip::factory()->for(User::factory())->create([
            'name' => 'Japan 2026',
            'status' => TripStatus::COMPLETED,
        ]);
        $destinations = Destination::factory()->count(3)->for($original)->create();
        Task::factory()->count(2)->for($destinations->first())->create();

        $cloner = User::factory()->create();

        $clone = $this->service->clone($original, $cloner);
        $clone->load('destinations.tasks');

        $this->assertSame($cloner->id, $clone->user_id);
        $this->assertNotSame($original->id, $clone->id);
        $this->assertSame(TripStatus::PLANNED, $clone->status);
        $this->assertFalse($clone->is_public);
        $this->assertSame('Japan 2026 (copy)', $clone->name);
        $this->assertCount(3, $clone->destinations);
        $this->assertCount(2, $clone->destinations->first()->tasks);
        $this->assertSame(
            $original->destinations->first()->budget,
            $clone->destinations->first()->budget,
        );
        $this->assertSame(0, $clone->likes()->count());
    }

    #[Test]
    public function it_rolls_back_everything_if_cloning_fails(): void
    {
        $original = Trip::factory()->for(User::factory())->create();
        $destination = Destination::factory()->for($original)->create();
        Task::factory()->for($destination)->create([
            'title' => str_repeat('a', 700000000),
        ]);

        $user = User::factory()->create();

        $tripsBefore = Trip::count();
        $destinationsBefore = Destination::count();
        $tasksBefore = Task::count();

        Task::creating(function () {
            throw new RuntimeException('Simulated task failure');
        });

        try {
            $this->service->clone($original, $user);
        } catch (RuntimeException) {
        }

        $this->assertSame($tripsBefore, Trip::count());
        $this->assertSame($destinationsBefore, Destination::count());
        $this->assertSame($tasksBefore, Task::count());
    }
}
