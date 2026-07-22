<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orphan extends Model
{
    use HasFactory;

    /**
     * الحقول المسموح بتعبئتها (Mass Assignment)
     * تم تحديث القائمة لتشمل كافة حقول استمارة اليتيم والأسرة والمعيل
     */
    protected $fillable = [
        // 1. بيانات اليتيم الأساسية
        'name',
        'file_number',
        'photo',
        'gender',
        'religion',
        'nationality',
        'birth_date',
        'birth_place',
        'address_gov',
        'address_dist',
        'address_village',
        'status',
        'branch_id',

        // 2. التعليم والصحة
        'education_status',
        'school_name',
        'academic_level',
        'school_phone',
        'health_status',
        'talents',
        'quran_memorization',
        'academic_result',     // تم الإضافة هنا لحفظ نتيجة اليتيم
        'thank_you_letter',    // تم الإضافة هنا لحفظ رسالة الشكر بخط اليتيم

        // 3. بيانات الوالدين
        'father_death_cause',
        'father_death_date',
        'father_job_before',
        'mother_name',
        'mother_status',
        'mother_job',
        'mother_income',

        // 4. بيانات المعيل
        'guardian_name',
        'guardian_relation',
        'guardian_card_id',
        'guardian_phone',
    ];

    /**
     * علاقة اليتيم بالفرع
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * علاقة اليتيم بالكفالات
     */
    public function sponsorships()
    {
        return $this->hasMany(Sponsorship::class);
    }

    /**
     * الكفالة الحالية (آخر كفالة مسجلة)
     */
    public function sponsorship()
    {
        return $this->hasOne(Sponsorship::class)->latestOfMany();
    }

    /**
     * جلب المدفوعات المرتبطة باليتيم عبر جدول الكفالات
     */
    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Sponsorship::class);
    }

    /**
     * علاقة اليتيم بالمرفقات الرسمية
     */
    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}