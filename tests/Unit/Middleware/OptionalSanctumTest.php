<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\OptionalSanctumAuth;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OptionalSanctumTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_resolves_the_user_when_authenticated(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('spa')->plainTextToken;

        $request = Request::create('/discover');
        $request->headers->set('Authorization', 'Bearer ' . $token);
        $this->app->instance('request', $request);

        new OptionalSanctumAuth()->handle($request, fn () => response('ok'));

        $this->assertNotNull($request->user());
        $this->assertSame($user->id, $request->user()->id);
    }

    #[Test]
    public function it_allows_guests_through_with_no_user(): void
    {
        $request = Request::create('/discover');
        $this->app->instance('request', $request);

        $response = new OptionalSanctumAuth()->handle($request, fn () => response('ok'));

        $this->assertNull($request->user());
        $this->assertSame('ok', $response->getContent());
    }

    #[Test]
    public function it_does_not_reject_an_invalid_token(): void
    {
        $request = Request::create('/discover');
        $request->headers->set('Authorization', 'Bearer invalid-token');
        $this->app->instance('request', $request);

        $response = new OptionalSanctumAuth()->handle($request, fn () => response('ok'));

        $this->assertSame('ok', $response->getContent());
    }
}
