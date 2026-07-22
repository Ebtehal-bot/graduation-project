<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sponsorship;
use App\Notifications\SponsorNotification;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Auto-generate expiration warnings for sponsor's expiring sponsorships
        if ($user->role === 'sponsor' && $user->sponsor) {
            $expiringSponsorships = Sponsorship::where('sponsor_id', $user->sponsor->id)
                ->where('status', 'active')
                ->whereNotNull('end_date')
                ->where('end_date', '<=', now()->addDays(30))
                ->where('end_date', '>=', now())
                ->get();

            foreach ($expiringSponsorships as $sponsorship) {
                $key = 'expiration_' . $sponsorship->id;
                $exists = $user->notifications()
                    ->where('data->type', 'expiration_warning')
                    ->where('data->key', $key)
                    ->exists();

                if (!$exists) {
                    $orphanName = $sponsorship->orphan?->name ?? '---';
                    $daysLeft = now()->diffInDays($sponsorship->end_date) + 1;

                    $user->notify(new SponsorNotification(
                        title: 'اقتراب انتهاء الكفالة',
                        body: "كفالة اليتيم {$orphanName} ستنتهي بعد {$daysLeft} يوم",
                        type: 'expiration_warning',
                        key: $key,
                    ));
                }
            }
        }

        $notifications = $user
            ->notifications()
            ->latest()
            ->limit(50)
            ->get()
            ->map(function ($n) {
                $data = $n->data;
                return [
                    'id' => $n->id,
                    'title' => $data['title'] ?? 'إشعار جديد',
                    'body' => $data['body'] ?? '',
                    'type' => $data['type'] ?? 'info',
                    'is_read' => $n->read_at !== null,
                    'created_at' => $n->created_at,
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $notifications
        ]);
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث الإشعار'
        ]);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث جميع الإشعارات'
        ]);
    }

    public function unreadCount(Request $request)
    {
        return response()->json([
            'status' => true,
            'data' => [
                'count' => $request->user()->unreadNotifications->count()
            ]
        ]);
    }
}
