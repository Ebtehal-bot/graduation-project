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
        // 1. تعديل جدول الأيتام: إضافة النتيجة المدرسية ورسالة الشكر
        Schema::table('orphans', function (Blueprint $table) {
            if (!Schema::hasColumn('orphans', 'academic_result')) {
                $table->string('academic_result')->nullable()->after('education_status');
            }
            if (!Schema::hasColumn('orphans', 'thanks_image')) {
                $table->string('thanks_image')->nullable()->after('academic_result');
            }
        });

        // 2. تعديل جدول الكفالات: حذف حقل رسالة الشكر القديم
        Schema::table('sponsorships', function (Blueprint $table) {
            if (Schema::hasColumn('sponsorships', 'thanks_image')) {
                $table->dropColumn('thanks_image');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // للتراجع عن الهجرة: نعيد الجداول كما كانت
        Schema::table('sponsorships', function (Blueprint $table) {
            if (!Schema::hasColumn('sponsorships', 'thanks_image')) {
                $table->string('thanks_image')->nullable();
            }
        });

        Schema::table('orphans', function (Blueprint $table) {
            $table->dropColumn(['academic_result', 'thanks_image']);
        });
    }
};