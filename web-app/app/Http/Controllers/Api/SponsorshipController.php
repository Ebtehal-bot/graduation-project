<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Orphan;
use App\Models\Sponsorship;
use App\Models\User;
use App\Notifications\SponsorNotification;
use App\Notifications\SponsorshipRequestNotification;
use Illuminate\Http\Request;

class SponsorshipController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user->sponsor) {
            return response()->json([
                'status' => true,
                'data' => []
            ]);
        }

        $sponsorships = Sponsorship::where('sponsor_id', $user->sponsor->id)
            ->with(['orphan.branch'])
            ->latest()
            ->get()
            ->map(function ($s) {
                return $this->formatSponsorship($s);
            });

        return response()->json([
            'status' => true,
            'data' => $sponsorships
        ]);
    }

    public function show($id, Request $request)
    {
        $sponsorship = Sponsorship::with(['orphan.branch', 'sponsor', 'payments'])->findOrFail($id);

        $user = $request->user();
        if ($user->role === 'sponsor' && $sponsorship->sponsor_id !== $user->sponsor?->id) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك بالاطلاع على هذه الكفالة'
            ], 403);
        }

        return response()->json([
            'status' => true,
            'data' => $this->formatSponsorshipDetail($sponsorship)
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'orphan_id' => 'required|exists:orphans,id',
            'monthly_amount' => 'required|numeric|min:1',
            'sponsorship_type' => 'required|in:financial,educational,medical',
        ]);

        $user = $request->user();

        \Log::info('[SponsorshipController::store] Authenticated user', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'sponsor_id_raw' => $user->getAttribute('sponsor_id'),
        ]);

        // Auto-create sponsor profile if it doesn't exist
        if (!$user->sponsor) {
            \Log::info('[SponsorshipController::store] No sponsor found for user, auto-creating one', [
                'user_id' => $user->id,
            ]);

            $sponsor = \App\Models\Sponsor::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => '',
                'address' => '',
            ]);

            $user->sponsor_id = $sponsor->id;
            $user->save();
            $user->load('sponsor');

            \Log::info('[SponsorshipController::store] Auto-created sponsor', [
                'sponsor_id' => $sponsor->id,
                'user_id' => $user->id,
            ]);
        }

        \Log::info('[SponsorshipController::store] Sponsor found/created', [
            'sponsor_id' => $user->sponsor->id,
        ]);

        $orphan = Orphan::findOrFail($request->orphan_id);

        // Prevent sponsoring an orphan that is inactive or graduated
        if (in_array($orphan->status, ['inactive', 'graduated'])) {
            return response()->json([
                'status' => false,
                'message' => 'هذا اليتيم غير متاح للكفالة حالياً'
            ], 400);
        }

        $hasActiveSponsorship = $orphan->sponsorships()->whereIn('status', ['active', 'inactive'])->exists();
        if ($hasActiveSponsorship) {
            return response()->json([
                'status' => false,
                'message' => 'هذا اليتيم لديه كفالة نشطة بالفعل'
            ], 400);
        }

        \Log::info('[SponsorshipController::store] Creating sponsorship', [
            'orphan_id' => $orphan->id,
            'sponsor_id' => $user->sponsor->id,
            'monthly_amount' => $request->monthly_amount,
            'sponsorship_type' => $request->sponsorship_type,
        ]);

        $sponsorship = Sponsorship::create([
            'orphan_id' => $orphan->id,
            'sponsor_id' => $user->sponsor->id,
            'monthly_amount' => $request->monthly_amount,
            'sponsorship_type' => $request->sponsorship_type,
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addYear(),
        ]);

        // Update orphan status to sponsored
        $orphan->status = 'sponsored';
        $orphan->save();

        // Send notification to the sponsor
        $user->notify(new SponsorNotification(
            title: 'Request Received',
            body: 'Your sponsorship request for orphan ' . $orphan->name . ' has been received and is under review.',
            type: 'sponsorship',
        ));

        // Send notification to all Admin users
        $adminUsers = User::role(['super_admin', 'supervisor'])->get();
        foreach ($adminUsers as $admin) {
            $admin->notify(new SponsorshipRequestNotification(
                title: 'New Sponsorship Request',
                body: 'Sponsor ' . $user->name . ' submitted a sponsorship request for orphan ' . $orphan->name . '.',
                sponsorName: $user->name,
                orphanName: $orphan->name,
            ));
        }

        return response()->json([
            'status' => true,
            'message' => 'تم إنشاء الكفالة بنجاح',
            'data' => $this->formatSponsorship($sponsorship->load('orphan.branch'))
        ], 201);
    }

    public function active(Request $request)
    {
        $user = $request->user();

        if (!$user->sponsor) {
            return response()->json([
                'status' => true,
                'data' => []
            ]);
        }

        $sponsorships = Sponsorship::where('sponsor_id', $user->sponsor->id)
            ->where('status', 'active')
            ->with(['orphan.branch'])
            ->latest()
            ->get()
            ->map(function ($s) {
                return $this->formatSponsorship($s);
            });

        return response()->json([
            'status' => true,
            'data' => $sponsorships
        ]);
    }

    public function myOrphans(Request $request)
    {
        $user = $request->user();

        if (!$user->sponsor) {
            return response()->json([
                'status' => true,
                'data' => []
            ]);
        }

        $orphans = Sponsorship::where('sponsor_id', $user->sponsor->id)
            ->with(['orphan.branch'])
            ->latest()
            ->get()
            ->map(function ($s) {
                $orphan = $s->orphan;
                if (!$orphan) return null;
                return [
                    'id' => $orphan->id,
                    'name' => $orphan->name,
                    'file_number' => $orphan->file_number,
                    'photo' => $orphan->photo ? asset('storage/' . $orphan->photo) : null,
                    'gender' => $orphan->gender,
                    'age' => $orphan->birth_date ? now()->diffInYears($orphan->birth_date) : null,
                    'branch_name' => $orphan->branch?->name,
                    'education_status' => $orphan->education_status,
                    'health_status' => $orphan->health_status,
                    'sponsorship_id' => $s->id,
                    'sponsorship_status' => $s->status,
                    'monthly_amount' => (float) $s->monthly_amount,
                    'sponsorship_type' => $s->sponsorship_type,
                    'start_date' => $s->start_date?->format('Y-m-d'),
                    'end_date' => $s->end_date?->format('Y-m-d'),
                ];
            })
            ->filter();

        return response()->json([
            'status' => true,
            'data' => $orphans->values()
        ]);
    }

    public function ended(Request $request)
    {
        $user = $request->user();

        if (!$user->sponsor) {
            return response()->json([
                'status' => true,
                'data' => []
            ]);
        }

        $sponsorships = Sponsorship::where('sponsor_id', $user->sponsor->id)
            ->where('status', 'ended')
            ->with(['orphan.branch'])
            ->latest()
            ->get()
            ->map(function ($s) {
                return $this->formatSponsorship($s);
            });

        return response()->json([
            'status' => true,
            'data' => $sponsorships
        ]);
    }

    private function formatSponsorship($s)
    {
        $remainingDays = null;
        if ($s->end_date) {
            $remainingDays = now()->lte($s->end_date)
                ? now()->diffInDays($s->end_date)
                : 0;
        }

        return [
            'id' => $s->id,
            'sponsorship_start_date' => $s->start_date?->format('Y-m-d'),
            'sponsorship_end_date' => $s->end_date?->format('Y-m-d'),
            'sponsorship_status' => $s->status,
            'monthly_amount' => (float) $s->monthly_amount,
            'sponsorship_type' => $s->sponsorship_type,
            'remaining_days' => $remainingDays,
            'created_at' => $s->created_at,
            'orphan' => $s->orphan ? [
                'id' => $s->orphan->id,
                'full_name' => $s->orphan->name ?? '---',
                'age' => $s->orphan->birth_date ? now()->diffInYears($s->orphan->birth_date) : null,
                'gender' => $s->orphan->gender,
                'image_url' => $s->orphan->photo ? asset('storage/' . $s->orphan->photo) : null,
                'branch_name' => $s->orphan->branch->name ?? null,
                'health_status' => $s->orphan->health_status,
            ] : null,
        ];
    }

    private function formatSponsorshipDetail($s)
    {
        $remainingDays = null;
        if ($s->end_date) {
            $remainingDays = now()->lte($s->end_date)
                ? now()->diffInDays($s->end_date)
                : 0;
        }

        return [
            'id' => $s->id,
            'sponsorship_start_date' => $s->start_date?->format('Y-m-d'),
            'sponsorship_end_date' => $s->end_date?->format('Y-m-d'),
            'sponsorship_status' => $s->status,
            'monthly_amount' => (float) $s->monthly_amount,
            'sponsorship_type' => $s->sponsorship_type,
            'remaining_days' => $remainingDays,
            'sponsor_name' => $s->sponsor?->name ?? '---',
            'created_at' => $s->created_at,
            'orphan' => $s->orphan ? [
                'id' => $s->orphan->id,
                'full_name' => $s->orphan->name ?? '---',
                'age' => $s->orphan->birth_date ? now()->diffInYears($s->orphan->birth_date) : null,
                'gender' => $s->orphan->gender,
                'image_url' => $s->orphan->photo ? asset('storage/' . $s->orphan->photo) : null,
                'branch_name' => $s->orphan->branch->name ?? null,
                'health_status' => $s->orphan->health_status,
            ] : null,
            'payments' => $s->payments?->map(function ($p) {
                return [
                    'id' => $p->id,
                    'amount' => $p->amount,
                    'date' => $p->date,
                    'payment_status' => $p->payment_status,
                ];
            }) ?? [],
        ];
    }
}