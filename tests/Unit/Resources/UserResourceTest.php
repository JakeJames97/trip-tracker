<?php

namespace Tests\Unit\Resources;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_the_expected_shape(): void
    {
        $user = User::factory()->create(['username' => 'demo', 'email' => 'demo@example.com']);

        $array = new UserResource($user)->toArray(Request::create('/'));

        $this->assertSame($user->id, $array['id']);
        $this->assertSame('demo', $array['username']);
        $this->assertSame('demo@example.com', $array['email']);
        $this->assertArrayNotHasKey('password', $array);
    }
}
