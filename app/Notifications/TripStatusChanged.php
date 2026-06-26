<?php

namespace App\Notifications;

use App\Enums\TripStatus;
use App\Models\Trip;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TripStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Trip $trip,
        public TripStatus $status,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $label = $this->status === TripStatus::PROGRESS ? 'now in progress' : 'now complete';

        return [
            'type' => 'trip_status_changed',
            'trip_id' => $this->trip->id,
            'trip_name' => $this->trip->name,
            'status' => $this->status->value,
            'message' => "Your trip “{$this->trip->name}” is {$label}",
        ];
    }
}
