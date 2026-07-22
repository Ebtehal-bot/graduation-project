<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SponsorshipRequestNotification extends Notification
{
    use Queueable;

    protected string $title;
    protected string $body;
    protected string $sponsorName;
    protected string $orphanName;

    public function __construct(string $title, string $body, string $sponsorName, string $orphanName)
    {
        $this->title = $title;
        $this->body = $body;
        $this->sponsorName = $sponsorName;
        $this->orphanName = $orphanName;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'type' => 'sponsorship_request',
            'sponsor_name' => $this->sponsorName,
            'orphan_name' => $this->orphanName,
            'format' => 'filament',
        ];
    }
}
