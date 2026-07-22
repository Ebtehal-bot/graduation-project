<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sponsorship extends Model
{
    use HasFactory;

    // تحديد اسم الجدول إذا كان يختلف عن الجمع الافتراضي (اختياري)
    // protected table = 'sponsorships';

   protected $fillable = [
        'orphan_id', 
        'sponsor_id', 
        'start_date', 
        'end_date', 
        'monthly_amount', 
        'status',
        'thanks_image',
        'sponsorship_type', // أضيفي هذا السطر هنا
    ];

    /**
     * تحويل الحقول إلى أنواع بيانات محددة
     * هذا يساعد Filament في التعامل مع التواريخ بشكل صحيح
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'monthly_amount' => 'decimal:2',
    ];

    /**
     * علاقة الكفالة باليتيم (Sponsorship belongs to Orphan)
     * بناءً على ERD: Orphan (1) <---> (N) Sponsorship
     */
    public function orphan()
    {
        // إذا كان اسم العمود في قاعدة البيانات هو orphan_id
        return $this->belongsTo(Orphan::class, 'orphan_id');
    }

    /**
     * علاقة الكفالة بالكفيل (Sponsorship belongs to Sponsor)
     * بناءً على ERD: Sponsor (1) <---> (N) Sponsorship
     */
    public function sponsor()
    {
        // إذا كان اسم العمود في قاعدة البيانات هو sponsor_id
        return $this->belongsTo(Sponsor::class, 'sponsor_id');
    }

    /**
     * (إضافة اختيارية) علاقة مع المدفوعات إذا أردت عرضها لاحقاً
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'sponsorship_id');
    }

    /**
     * علاقة الكفالة بالفرع عبر اليتيم (Sponsorship -> Orphan -> Branch)
     */
    public function branch()
    {
        return $this->hasOneThrough(
            Branch::class,
            Orphan::class,
            'id',
            'id',
            'orphan_id',
            'branch_id'
        );
    }
}