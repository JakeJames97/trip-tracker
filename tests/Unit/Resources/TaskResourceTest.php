<?php

namespace Tests\Unit\Resources;

use App\Http\Resources\TaskResource;
use App\Models\Destination;
use App\Models\Task;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class TaskResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_the_expected_shape(): void
    {
        $task = Task::factory()
            ->for(Destination::factory()->for(Trip::factory()->for(User::factory())))
            ->create(['title' => 'Book hotel', 'is_completed' => true]);

        $array = new TaskResource($task)->toArray(Request::create('/'));

        $this->assertSame($task->id, $array['id']);
        $this->assertSame('Book hotel', $array['title']);
        $this->assertTrue($array['is_completed']);
    }
}
