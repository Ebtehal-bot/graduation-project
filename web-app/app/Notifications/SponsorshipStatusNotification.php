<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SponsorshipStatusNotification extends Notification
{
    use Queueable;

    protected string $title;
    protected string $body;
    protected string $status;
    protected string $orphanName;

    public function __construct(string $title, string $body, string $status, string $orphanName)
    {
        $this->title = $title;
        $this->body = $body;
        $this->status = $status;
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
            'type' => 'sponsorship_status',
            'status' => $this->status,
            'orphan_name' => $this->orphanName,
            'format' => 'filament',
        ];
    }
}
