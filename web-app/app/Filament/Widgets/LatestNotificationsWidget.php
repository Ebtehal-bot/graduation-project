<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class LatestNotificationsWidget extends Widget
{
    protected static string $view = 'filament.widgets.latest-notifications-widget';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user() && config('filament.database_notifications.enabled');
    }

    public function markAsRead(string $notificationId): void
    {
        $user = auth()->user();

        $notification = $user->unreadNotifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
        }
    }

    protected function getViewData(): array
    {
        $user = auth()->user();

        $unreadNotifications = $user->unreadNotifications()->latest()->take(5)->get()->map(function ($notification) {
            $data = $notification->data;
            return [
                'id' => $notification->id,
                'title' => $data['title'] ?? '',
                'body' => $data['body'] ?? '',
                'type' => $data['type'] ?? 'info',
                'created_at' => $notification->created_at->diffForHumans(),
            ];
        });

        $unreadCount = $user->unreadNotifications()->count();

        return [
            'unreadNotifications' => $unreadNotifications,
            'unreadCount' => $unreadCount,
        ];
    }
}
