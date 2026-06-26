<?php

namespace App\Notifications;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TripCloned extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Trip $trip,
        public User $cloner,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'trip_cloned',
            'trip_id' => $this->trip->id,
            'trip_name' => $this->trip->name,
            'message' => "{$this->cloner->username} cloned your trip “{$this->trip->name}”",
        ];
    }
}
