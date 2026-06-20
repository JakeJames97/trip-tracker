<?php

namespace Tests\Unit\Resources;

use App\Http\Resources\CountryResource;
use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class CountryResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_the_expected_shape(): void
    {
        $country = Country::factory()->create();

        $array = new CountryResource($country)->toArray(Request::create('/'));

        $this->assertSame($country->name, $array['name']);
        $this->assertSame($country->code, $array['code']);
    }
}
