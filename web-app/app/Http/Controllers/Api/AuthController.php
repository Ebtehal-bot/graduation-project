<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Sponsor;
use App\Models\Sponsorship;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'sponsor',
        ]);

        $sponsor = Sponsor::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone ?? '',
            'address' => $request->address ?? '',
        ]);

        $user->sponsor_id = $sponsor->id;
        $user->save();

        $token = $user->createToken('mobile-app')->plainTextToken;

        $user->assignRole('sponsor');
        $user->load('sponsor');

        return response()->json([
            'status' => true,
            'message' => 'تم إنشاء الحساب بنجاح',
            'token' => $token,
            'user' => array_merge($user->toArray(), [
                'role' => 'sponsor',
                'sponsor_id' => $sponsor->id,
                'image_url' => null,
                'phone' => $sponsor->phone,
                'address' => $sponsor->address,
            ]),
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'
            ], 401);
        }

        $token = $user->createToken('mobile-app')->plainTextToken;

        $user->load('sponsor');

        return response()->json([
            'status' => true,
            'message' => 'تم تسجيل الدخول بنجاح',
            'token' => $token,
            'user' => array_merge($user->toArray(), [
                'role' => $user->getRoleNames()->first() ?? $user->role,
                'sponsor_id' => $user->sponsor_id,
                'image_url' => null,
                'phone' => $user->sponsor?->phone ?? '',
                'address' => $user->sponsor?->address ?? '',
            ]),
        ]);
    }

    public function profile(Request $request)
    {
        $user = $request->user()->load('sponsor');

        $sponsorshipsCount = 0;
        $activeSponsorshipsCount = 0;
        $sponsoredOrphansCount = 0;
        $totalDonated = 0.0;
        if ($user->sponsor) {
            $sponsorshipsCount = $user->sponsor->sponsorships()->count();
            $activeSponsorshipsCount = $user->sponsor->sponsorships()->where('status', 'active')->count();
            $sponsoredOrphansCount = $user->sponsor->sponsorships()
                ->where('status', 'active')
                ->distinct('orphan_id')
                ->count('orphan_id');
            $sponsorSponsorshipIds = $user->sponsor->sponsorships()->pluck('id');
            $totalDonated = (float) Payment::whereIn('sponsorship_id', $sponsorSponsorshipIds)->sum('amount');
        }

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->getRoleNames()->first() ?? $user->role,
                'sponsor_id' => $user->sponsor_id,
                'phone' => $user->sponsor?->phone ?? '',
                'address' => $user->sponsor?->address ?? '',
                'image_url' => null,
                'join_date' => $user->created_at,
                'total_sponsorships' => $sponsorshipsCount,
                'active_sponsorships' => $activeSponsorshipsCount,
                'sponsored_orphans' => $sponsoredOrphansCount,
                'total_donations' => $totalDonated,
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم تسجيل الخروج بنجاح'
        ]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $request->user()->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user = $request->user();

        if ($request->has('name')) {
            $user->name = $request->name;
            if ($user->sponsor) {
                $user->sponsor->update(['name' => $request->name]);
            }
        }
        if ($request->has('email')) {
            $user->email = $request->email;
            if ($user->sponsor) {
                $user->sponsor->update(['email' => $request->email]);
            }
        }

        if ($user->sponsor) {
            if ($request->has('phone')) {
                $user->sponsor->phone = $request->phone;
            }
            if ($request->has('address')) {
                $user->sponsor->address = $request->address;
            }
            $user->sponsor->save();
        }

        $user->save();
        $user->load('sponsor');

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث الملف الشخصي',
            'user' => array_merge($user->toArray(), [
                'phone' => $user->sponsor?->phone ?? '',
                'address' => $user->sponsor?->address ?? '',
            ]),
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'كلمة المرور الحالية غير صحيحة'
            ], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'تم تغيير كلمة المرور بنجاح'
        ]);
    }
}