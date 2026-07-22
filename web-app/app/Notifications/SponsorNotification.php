<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SponsorNotification extends Notification
{
    use Queueable;

    protected string $title;
    protected string $body;
    protected string $type;
    protected ?string $key;

    public function __construct(string $title, string $body, string $type = 'info', ?string $key = null)
    {
        $this->title = $title;
        $this->body = $body;
        $this->type = $type;
        $this->key = $key;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $data = [
            'title' => $this->title,
            'body' => $this->body,
            'type' => $this->type,
            'format' => 'filament',
        ];

        if ($this->key !== null) {
            $data['key'] = $this->key;
        }

        return $data;
    }
}
