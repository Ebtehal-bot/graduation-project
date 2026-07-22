<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function myHistory(Request $request)
    {
        $user = $request->user();

        if (!$user->sponsor) {
            return response()->json([
                'status' => true,
                'data' => []
            ]);
        }

        $sponsorIds = $user->sponsor->sponsorships()->pluck('id');

        $payments = Payment::whereIn('sponsorship_id', $sponsorIds)
            ->with('sponsorship.orphan')
            ->latest()
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'amount' => (float) $p->amount,
                    'date' => $p->date,
                    'payment_status' => $p->payment_status,
                    'orphan_name' => $p->sponsorship?->orphan?->name ?? '---',
                    'orphan_image' => $p->sponsorship?->orphan?->photo
                        ? asset('storage/' . $p->sponsorship->orphan->photo)
                        : null,
                    'sponsorship_id' => $p->sponsorship_id,
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $payments
        ]);
    }
}
