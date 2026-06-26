<?php

namespace App\Notifications;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TripLiked extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Trip $trip,
        public User $liker,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'trip_liked',
            'trip_id' => $this->trip->id,
            'trip_name' => $this->trip->name,
            'message' => "{$this->liker->username} liked your trip “{$this->trip->name}”",
        ];
    }
}
