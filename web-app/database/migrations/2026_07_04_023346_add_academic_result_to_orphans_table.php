<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('orphans', function (Blueprint $table) {
            // إضافة حقل نصي لتخزين مسار صورة النتيجة المدرسية
            $table->string('academic_result')->nullable()->after('education_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('orphans', function (Blueprint $table) {
            // حذف الحقل في حال التراجع
            $table->dropColumn('academic_result');
        });
    }
};