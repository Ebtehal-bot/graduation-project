<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Orphan;
use App\Models\Sponsorship;
use Illuminate\Http\Request;

class OrphanController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $isSponsor = $user && $user->role === 'sponsor';

        $allOrphansQuery = Orphan::with('branch');
        $myOrphans = collect();

        if ($isSponsor) {
            if ($user->sponsor) {
                $sponsoredOrphanIds = Sponsorship::where('sponsor_id', $user->sponsor->id)
                    ->whereIn('status', ['active', 'inactive'])
                    ->pluck('orphan_id');

                $allOrphansQuery->where(function ($q) use ($sponsoredOrphanIds) {
                    $q->whereIn('id', $sponsoredOrphanIds)
                      ->orWhere(function ($subQ) {
                          $subQ->whereNotIn('status', ['inactive', 'graduated'])
                               ->whereDoesntHave('sponsorships', function ($sq) {
                                   $sq->whereIn('status', ['active', 'inactive']);
                               });
                      });
                });

                $myOrphans = Orphan::with('branch')
                    ->whereIn('id', $sponsoredOrphanIds)
                    ->get()
                    ->map(fn($o) => $this->formatOrphan($o));
            } else {
                $allOrphansQuery->whereNotIn('status', ['inactive', 'graduated'])
                    ->whereDoesntHave('sponsorships', function ($q) {
                        $q->whereIn('status', ['active', 'inactive']);
                    });
            }
        }

        $allOrphans = $allOrphansQuery->get()->map(fn($o) => $this->formatOrphan($o));

        $sponsoredIds = Sponsorship::whereIn('status', ['active', 'inactive'])
            ->pluck('orphan_id')
            ->unique();

        $availableOrphans = $allOrphans->filter(fn($o) => !$sponsoredIds->contains($o['id']))->values();

        return response()->json([
            'status' => true,
            'data' => [
                'all_orphans' => $allOrphans,
                'my_orphans' => $myOrphans,
                'available_orphans' => $availableOrphans,
            ]
        ]);
    }

    public function show($id, Request $request)
    {
        $orphan = Orphan::with(['branch', 'sponsorships.sponsor', 'attachments'])->findOrFail($id);

        $user = $request->user();

        if ($user && $user->role === 'sponsor') {
            $sponsorId = $user->sponsor?->id;

            if ($sponsorId) {
                $sponsoredByOther = $orphan->sponsorships()
                    ->whereIn('status', ['active', 'inactive'])
                    ->where('sponsor_id', '!=', $sponsorId)
                    ->exists();

                if ($sponsoredByOther) {
                    return response()->json([
                        'status' => false,
                        'message' => 'هذا اليتيم غير متاح للكفالة حالياً'
                    ], 403);
                }
            }
        }

        return response()->json([
            'status' => true,
            'data' => $this->formatOrphanDetail($orphan)
        ]);
    }

    public function available(Request $request)
    {
        $query = Orphan::with('branch')
            ->whereDoesntHave('sponsorships', function ($q) {
                $q->whereIn('status', ['active', 'inactive']);
            });

        if ($request->user() && $request->user()->role === 'sponsor') {
            $query->whereNotIn('status', ['inactive', 'graduated']);
        }

        $orphans = $query->get()->map(function ($orphan) {
            return $this->formatOrphan($orphan);
        });

        return response()->json([
            'status' => true,
            'data' => $orphans
        ]);
    }

    public function search(Request $request)
    {
        $query = Orphan::query()->with('branch');

        if ($request->user() && $request->user()->role === 'sponsor') {
            $user = $request->user();

            if ($user->sponsor) {
                $sponsoredOrphanIds = Sponsorship::where('sponsor_id', $user->sponsor->id)
                    ->whereIn('status', ['active', 'inactive'])
                    ->pluck('orphan_id');

                $query->where(function ($q) use ($sponsoredOrphanIds) {
                    $q->whereIn('id', $sponsoredOrphanIds)
                      ->orWhere(function ($subQ) {
                          $subQ->whereNotIn('status', ['inactive', 'graduated'])
                               ->whereDoesntHave('sponsorships', function ($sq) {
                                   $sq->whereIn('status', ['active', 'inactive']);
                               });
                      });
                });
            } else {
                $query->whereNotIn('status', ['inactive', 'graduated'])
                    ->whereDoesntHave('sponsorships', function ($q) {
                        $q->whereIn('status', ['active', 'inactive']);
                    });
            }
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('file_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $orphans = $query->get()->map(function ($orphan) {
            return $this->formatOrphan($orphan);
        });

        return response()->json([
            'status' => true,
            'data' => $orphans
        ]);
    }

    public function sponsorOrphans(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'sponsor') {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $myOrphans = collect();
        if ($user->sponsor) {
            $myOrphans = Sponsorship::where('sponsor_id', $user->sponsor->id)
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
                        'is_sponsored' => true,
                    ];
                })
                ->filter()
                ->values();
        }

        $availableOrphans = Orphan::with('branch')
            ->whereNotIn('status', ['inactive', 'graduated'])
            ->whereDoesntHave('sponsorships', function ($q) {
                $q->whereIn('status', ['active', 'inactive']);
            })
            ->get()
            ->map(function ($orphan) {
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
                    'is_sponsored' => false,
                ];
            });

        return response()->json([
            'status' => true,
            'data' => [
                'my_orphans' => $myOrphans,
                'available_orphans' => $availableOrphans,
            ]
        ]);
    }

    private function formatOrphan($orphan)
    {
        $hasActiveSponsorship = $orphan->sponsorships()
            ->whereIn('status', ['active', 'inactive'])
            ->exists();
        $latestSponsorship = $orphan->sponsorships()->latest()->first();
        return [
            'id' => $orphan->id,
            'name' => $orphan->name,
            'file_number' => $orphan->file_number,
            'photo' => $orphan->photo ? asset('storage/' . $orphan->photo) : null,
            'gender' => $orphan->gender,
            'birth_date' => $orphan->birth_date,
            'age' => $orphan->birth_date ? now()->diffInYears($orphan->birth_date) : null,
            'status' => $orphan->status,
            'branch_name' => $orphan->branch?->name,
            'branch_id' => $orphan->branch_id,
            'education_status' => $orphan->education_status,
            'health_status' => $orphan->health_status,
            'is_sponsored' => $hasActiveSponsorship,
            'sponsorship_status' => $latestSponsorship?->status,
        ];
    }

    private function formatOrphanDetail($orphan)
    {
        $hasActiveSponsorship = $orphan->sponsorships()
            ->whereIn('status', ['active', 'inactive'])
            ->exists();
        $latestSponsorship = $orphan->sponsorships()->with('sponsor')->latest()->first();
        return [
            'id' => $orphan->id,
            'name' => $orphan->name,
            'file_number' => $orphan->file_number,
            'photo' => $orphan->photo ? asset('storage/' . $orphan->photo) : null,
            'gender' => $orphan->gender,
            'religion' => $orphan->religion,
            'nationality' => $orphan->nationality,
            'birth_date' => $orphan->birth_date,
            'birth_place' => $orphan->birth_place,
            'age' => $orphan->birth_date ? now()->diffInYears($orphan->birth_date) : null,
            'status' => $orphan->status,
            'address_gov' => $orphan->address_gov,
            'address_dist' => $orphan->address_dist,
            'address_village' => $orphan->address_village,
            'branch' => $orphan->branch ? [
                'id' => $orphan->branch->id,
                'name' => $orphan->branch->name,
                'governorate' => $orphan->branch->governorate,
                'phone' => $orphan->branch->phone,
            ] : null,
            'education_status' => $orphan->education_status,
            'school_name' => $orphan->school_name,
            'academic_level' => $orphan->academic_level,
            'school_phone' => $orphan->school_phone,
            'health_status' => $orphan->health_status,
            'talents' => $orphan->talents,
            'quran_memorization' => $orphan->quran_memorization,
            'father_death_cause' => $orphan->father_death_cause,
            'father_death_date' => $orphan->father_death_date,
            'mother_name' => $orphan->mother_name,
            'mother_status' => $orphan->mother_status,
            'mother_job' => $orphan->mother_job,
            'guardian_name' => $orphan->guardian_name,
            'guardian_relation' => $orphan->guardian_relation,
            'guardian_phone' => $orphan->guardian_phone,
            'is_sponsored' => $hasActiveSponsorship,
            'sponsorship' => $latestSponsorship ? [
                'id' => $latestSponsorship->id,
                'sponsor_name' => $latestSponsorship->sponsor?->name,
                'monthly_amount' => $latestSponsorship->monthly_amount,
                'sponsorship_type' => $latestSponsorship->sponsorship_type,
                'status' => $latestSponsorship->status,
                'start_date' => $latestSponsorship->start_date,
                'end_date' => $latestSponsorship->end_date,
            ] : null,
            'sponsorships' => $orphan->sponsorships?->map(function ($s) {
                return [
                    'id' => $s->id,
                    'monthly_amount' => $s->monthly_amount,
                    'status' => $s->status,
                    'start_date' => $s->start_date,
                    'end_date' => $s->end_date,
                    'sponsorship_type' => $s->sponsorship_type,
                ];
            }) ?? [],
            'attachments' => $orphan->attachments?->map(function ($a) {
                $filePath = \App\Models\Attachment::safeFilePath($a->file_path);
                return [
                    'id' => $a->id,
                    'document_type' => $a->document_type,
                    'file_path' => $filePath ? asset('storage/' . $filePath) : null,
                ];
            }) ?? [],
        ];
    }
}