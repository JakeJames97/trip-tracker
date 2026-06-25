<?php

namespace App\Policies;

use App\Models\Trip;
use App\Models\User;

class TripPolicy
{
    public function view(): bool
    {
        return true;
    }

    public function update(User $user, Trip $trip): bool
    {
        return $user->id === $trip->user_id;
    }

    public function delete(User $user, Trip $trip): bool
    {
        return $user->id === $trip->user_id;
    }

    public function clone(User $user, Trip $trip): bool
    {
        return $trip->is_public && $trip->user_id !== $user->id;
    }
}
