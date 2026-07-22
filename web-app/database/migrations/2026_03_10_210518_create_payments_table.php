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
        Schema::create('payments', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('sponsorship_id')->constrained('sponsorships')->onDelete('cascade');
            
            // المبلغ المدفوع
            $table->decimal('amount', 12, 2); // زدنا الطول لـ 12 ليتناسب مع العملة اليمنية والمبالغ الكبيرة
            
            // تاريخ الدفع مع إضافة Index لتسريع التقارير السنوية
            $table->date('date')->index(); 
            
            // حالة الدفع
            $table->string('payment_status')->default('paid'); 

            // إضافة حقل ملاحظات (اختياري لكنه احترافي للتقارير)
            $table->text('notes')->nullable(); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
};