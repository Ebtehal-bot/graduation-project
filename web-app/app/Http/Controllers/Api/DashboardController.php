<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Orphan;
use App\Models\Sponsor;
use App\Models\Sponsorship;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $user = $request->user();
        $today = now();
        $nextMonth = now()->addDays(30);

        if ($user->role === 'sponsor') {
            $sponsorId = $user->sponsor?->id;

            $totalOrphans = Sponsorship::where('sponsor_id', $sponsorId)
                ->distinct('orphan_id')
                ->count('orphan_id');
            $sponsoredOrphans = $totalOrphans;
            $unsponsoredOrphans = Orphan::whereNotIn('status', ['inactive', 'graduated'])
                ->whereDoesntHave('sponsorships', function ($q) {
                    $q->whereIn('status', ['active', 'inactive']);
                })->count();
            $totalSponsorships = Sponsorship::where('sponsor_id', $sponsorId)->count();
            $activeSponsorships = Sponsorship::where('sponsor_id', $sponsorId)->where('status', 'active')->count();
            $pendingSponsorships = Sponsorship::where('sponsor_id', $sponsorId)->whereIn('status', ['pending', 'inactive'])->count();
            $completedSponsorships = Sponsorship::where('sponsor_id', $sponsorId)
                ->whereIn('status', ['completed', 'stopped', 'ended'])
                ->count();
            $totalSponsors = 1;
            $expiringSoon = Sponsorship::where('sponsor_id', $sponsorId)
                ->where('status', 'active')
                ->whereNotNull('end_date')
                ->where('end_date', '<=', $nextMonth)
                ->where('end_date', '>=', $today)
                ->count();
        } else {
            $totalOrphans = Orphan::count();
            $sponsoredOrphans = Orphan::whereHas('sponsorships')->count();
            $unsponsoredOrphans = Orphan::whereDoesntHave('sponsorships')->count();
            $totalSponsorships = Sponsorship::count();
            $activeSponsorships = Sponsorship::where('status', 'active')->count();
            $pendingSponsorships = Sponsorship::whereIn('status', ['pending', 'inactive'])->count();
            $completedSponsorships = Sponsorship::whereIn('status', ['completed', 'stopped', 'ended'])->count();
            $totalSponsors = Sponsor::count();
            $expiringSoon = Sponsorship::where('status', 'active')
                ->whereNotNull('end_date')
                ->where('end_date', '<=', $nextMonth)
                ->where('end_date', '>=', $today)
                ->count();
        }

        return response()->json([
            'status' => true,
            'data' => [
                'total_orphans' => $totalOrphans,
                'sponsored_orphans' => $sponsoredOrphans,
                'unsponsored_orphans' => $unsponsoredOrphans,
                'total_sponsorships' => $totalSponsorships,
                'active_sponsorships' => $activeSponsorships,
                'pending_sponsorships' => $pendingSponsorships,
                'completed_sponsorships' => $completedSponsorships,
                'total_sponsors' => $totalSponsors,
                'expiring_soon' => $expiringSoon,
            ]
        ]);
    }

    public function recentActivities(Request $request)
    {
        $user = $request->user();

        $query = Sponsorship::with(['orphan', 'sponsor']);

        if ($user->role === 'sponsor') {
            $query->where('sponsor_id', $user->sponsor?->id);
        }

        $activities = $query->latest()
            ->limit(10)
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'action' => 'تم تسجيل كفالة جديدة',
                    'orphan_name' => $s->orphan?->name ?? '---',
                    'sponsor_name' => $s->sponsor?->name ?? '---',
                    'amount' => $s->monthly_amount,
                    'status' => $s->status,
                    'created_at' => $s->created_at,
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $activities
        ]);
    }

    public function growthTrends()
    {
        $monthlyData = Sponsorship::selectRaw('
            YEAR(created_at) as year,
            MONTH(created_at) as month,
            COUNT(*) as total
        ')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->limit(12)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $monthlyData
        ]);
    }
}
