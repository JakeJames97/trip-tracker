<?php

namespace App\Http\Controllers\Trips;

use App\Http\Controllers\Controller;
use App\Http\Requests\Trips\GetTripsRequest;
use App\Http\Resources\TripResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexController extends Controller
{
    public function __invoke(GetTripsRequest $request): AnonymousResourceCollection
    {
        $params = $request->validated();

        $page = array_key_exists('page', $params) ? $params['page'] : 1;

        $trips = $request->user()
            ->trips()
            ->withCount('destinations')
            ->when($params['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->latest('start_date')
            ->paginate(page: $page);

        return TripResource::collection($trips);
    }
}
