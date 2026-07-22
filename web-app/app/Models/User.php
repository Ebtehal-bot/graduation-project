<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'role',
        'sponsor_id',
    ];

    /**
     * الحقول المخفية عند جلب البيانات (مثل الـ API)
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * تحويل أنواع الحقول تلقائياً
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
    }

    /**
     * التحكم في صلاحية دخول لوحة تحكم Filament
     * تم إغلاق البوابة وتفعيل الفحص الحقيقي للأدوار بنجاح 🔐
     */
    public function canAccessFilament(): bool
    {
        // يسمح فقط للمدير، المشرف، والموظف بدخول اللوحة، ويطرد الكفيل (Sponsor) بـ 403
        return $this->hasAnyRole(['super_admin', 'supervisor', 'employee']);
    }

    public function sponsor()
    {
        return $this->belongsTo(Sponsor::class, 'sponsor_id');
    }

    public function sponsorships()
    {
        return $this->hasMany(Sponsorship::class, 'sponsor_id', 'sponsor_id');
    }

    protected static function booted()
    {
        static::deleting(function (User $user) {
            if ($user->sponsor) {
                $user->sponsor->delete();
            }
        });
    }
}