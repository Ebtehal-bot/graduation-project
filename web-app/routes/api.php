<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrphanController;
use App\Http\Controllers\Api\SponsorshipController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SettingsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// =====================
// Auth APIs
// =====================

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// =====================
// Protected APIs (all require auth)
// =====================

Route::middleware('auth:sanctum')->group(function () {

    // Profile
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    Route::post('/user/change-password', [AuthController::class, 'changePassword']);

    // Orphans (role-aware: sponsor sees only available, admin sees all)
    Route::get('/orphans', [OrphanController::class, 'index']);
    Route::get('/orphans/available', [OrphanController::class, 'available']);
    Route::get('/orphans/search', [OrphanController::class, 'search']);
    Route::get('/orphans/{id}', [OrphanController::class, 'show']);

    // Sponsorships
    Route::get('/user/sponsorships', [SponsorshipController::class, 'index']);
    Route::get('/sponsorships/{id}', [SponsorshipController::class, 'show']);
    Route::post('/sponsorships', [SponsorshipController::class, 'store']);
    Route::get('/sponsorships/active', [SponsorshipController::class, 'active']);
    Route::get('/sponsorships/ended', [SponsorshipController::class, 'ended']);

    // Sponsor-specific: my sponsored orphans
    Route::get('/sponsor/orphans', [OrphanController::class, 'sponsorOrphans']);
    Route::get('/sponsorships/my-orphans', [SponsorshipController::class, 'myOrphans']);
    Route::get('/sponsorships/available', [OrphanController::class, 'available']);

    // Sponsor-specific: payment history
    Route::get('/payments/my-history', [PaymentController::class, 'myHistory']);

    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('/dashboard/recent-activities', [DashboardController::class, 'recentActivities']);
    Route::get('/dashboard/growth-trends', [DashboardController::class, 'growthTrends']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);

    // Settings
    Route::get('/settings', [SettingsController::class, 'index']);

    // Reports
    Route::get('/reports/sponsor', [ReportController::class, 'sponsorReports']);
});