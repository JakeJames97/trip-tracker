<?php

namespace Tests\Unit\Resources;

use App\Http\Resources\DestinationResource;
use App\Models\Country;
use App\Models\Destination;
use App\Models\Trip;
use App\Models\User;
use App\Services\CurrencyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Mockery\MockInterface;
use Tests\TestCase;

class DestinationResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_the_expected_shape(): void
    {
        $destination = $this->makeDestination([
            'city' => 'Rome',
            'country_code' => 'IT',
        ]);

        $array = new DestinationResource($destination)->toArray(Request::create('/'));

        $this->assertSame($destination->id, $array['id']);
        $this->assertSame('Rome', $array['city']);
        $this->assertSame('IT', $array['country_code']);
    }

    public function test_it_returns_null_budget_formatted_when_budget_is_null(): void
    {
        $destination = $this->makeDestination(['budget' => null]);

        $array = new DestinationResource($destination)->toArray(Request::create('/'));

        $this->assertNull($array['budget_formatted']);
        $this->assertNull($array['converted_budget_formatted']);
    }

    public function test_it_converts_budget_to_local_currency(): void
    {
        $this->mock(CurrencyService::class, function (MockInterface $mock) {
            $mock->shouldReceive('convert')
                ->once()
                ->with('GBP', 'EUR', 740)
                ->andReturn(85400);
        });

        Country::factory()->create(['code' => 'IT', 'currency' => 'EUR']);
        $destination = $this->makeDestination(['budget' => 740, 'country_code' => 'IT']);
        $destination->load('country');

        $array = new DestinationResource($destination)->toArray(Request::create('/'));

        $this->assertSame('€854,00', $array['converted_budget_formatted']);
        $this->assertSame('£740.00', $array['budget_formatted']);
    }

    private function makeDestination(array $attributes = []): Destination
    {
        return Destination::factory()
            ->for(Trip::factory()->for(User::factory()))
            ->create($attributes);
    }
}
