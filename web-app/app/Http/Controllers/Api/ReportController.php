<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sponsorship;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function sponsorReports(Request $request)
    {
        $user = $request->user();

        if (!$user->sponsor) {
            return response()->json([
                'status' => true,
                'data' => [
                    'total_sponsorships' => 0,
                    'active_sponsorships' => 0,
                    'completed_sponsorships' => 0,
                    'total_donated' => 0,
                    'average_duration' => 0,
                    'last_activity' => null,
                    'recent_sponsorships' => [],
                ]
            ]);
        }

        $sponsorships = Sponsorship::where('sponsor_id', $user->sponsor->id);

        $total = (clone $sponsorships)->count();
        $active = (clone $sponsorships)->where('status', 'active')->count();
        $ended = (clone $sponsorships)->whereIn('status', ['ended', 'inactive'])->count();
        $totalDonated = (clone $sponsorships)->sum('monthly_amount');

        $lastActivity = (clone $sponsorships)->latest()->first();

        $avgDuration = (clone $sponsorships)
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->get()
            ->avg(function ($s) {
                return $s->start_date->diffInDays($s->end_date);
            });

        $recent = (clone $sponsorships)
            ->with(['orphan'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'orphan_name' => $s->orphan?->name ?? '---',
                    'orphan_image' => $s->orphan?->photo ?? null,
                    'amount' => $s->monthly_amount,
                    'status' => $s->status,
                    'start_date' => $s->start_date,
                    'end_date' => $s->end_date,
                    'created_at' => $s->created_at,
                ];
            });

        return response()->json([
            'status' => true,
            'data' => [
                'total_sponsorships' => $total,
                'active_sponsorships' => $active,
                'ended_sponsorships' => $ended,
                'total_donated' => (float) $totalDonated,
                'average_duration' => round($avgDuration ?? 0),
                'last_activity' => $lastActivity?->created_at,
                'recent_sponsorships' => $recent,
            ]
        ]);
    }
}