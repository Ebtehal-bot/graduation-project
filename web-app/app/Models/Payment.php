<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'sponsorship_id',
        'amount',
        'date',
        'payment_status',
    ];

    /**
     * الدفعة تنتمي لكفالة معينة
     */
    public function sponsorship()
    {
        return $this->belongsTo(Sponsorship::class);
    }
}