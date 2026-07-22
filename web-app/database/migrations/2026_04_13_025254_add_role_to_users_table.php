<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الميجريشن لإضافة الحقل.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // إضافة حقل الصلاحية بعد البريد الإلكتروني مع قيمة افتراضية
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('staff')->after('email');
            }
        });
    }

    /**
     * التراجع عن الميجريشن وحذف الحقل.
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // حذف الحقل في حال عمل Rollback
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};