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
        Schema::table('sponsorships', function (Blueprint $table) {
            // إضافة حقل نوع الكفالة بعد حقل المبلغ الشهري
            // وضعنا القيمة الافتراضية 'financial' لضمان عدم حدوث خطأ في البيانات القديمة
            $table->string('sponsorship_type')->default('financial')->after('monthly_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sponsorships', function (Blueprint $table) {
            // حذف الحقل في حال أردتِ التراجع عن التعديل
            $table->dropColumn('sponsorship_type');
        });
    }
};