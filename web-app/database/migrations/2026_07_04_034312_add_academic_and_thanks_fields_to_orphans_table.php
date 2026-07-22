<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orphans', function (Blueprint $table) {
            // التحقق من حقل النتيجة المدرسية قبل إضافته تجنباً للتكرار
            if (!Schema::hasColumn('orphans', 'academic_result')) {
                $table->string('academic_result')->nullable()->after('quran_memorization');
            }
            
            // إضافة حقل رسالة الشكر بخط اليتيم
            if (!Schema::hasColumn('orphans', 'thank_you_letter')) {
                $table->string('thank_you_letter')->nullable()->after('academic_result');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orphans', function (Blueprint $table) {
            // حذف الحقول في حال التراجع عن الـ Migration
            $table->dropColumn(['academic_result', 'thank_you_letter']);
        });
    }
};