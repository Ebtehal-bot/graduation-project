<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'email',
        'address',
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function scopeSponsorOnly($query)
    {
        return $query->whereDoesntHave('user', function ($q) {
            $q->whereIn('role', ['super_admin', 'supervisor', 'employee']);
        });
    }

    /**
     * علاقة الكفيل بالكفالات: الكفيل الواحد يمكنه كفالة أكثر من يتيم
     */
    public function sponsorships()
    {
        return $this->hasMany(Sponsorship::class);
    }
}