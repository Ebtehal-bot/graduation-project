<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orphans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->string('file_number')->nullable();
            $table->string('photo')->nullable();
            
            // البيانات الأساسية
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('religion')->default('الإسلام');
            $table->string('nationality')->default('يمني');
            $table->date('birth_date')->nullable();
            // حذفنا birth_order لأنك طلبتِ إلغاءه من الواجهة
            $table->string('birth_place')->nullable();
            
            // --- إضافة هامة للتقارير ---
            $table->decimal('sponsorship_amount', 10, 2)->default(0)->comment('مبلغ الكفالة الشهري');

            // العنوان
            $table->string('address_gov')->nullable();
            $table->string('address_dist')->nullable();
            $table->string('address_village')->nullable();
            
            // التعليم والصحة
            $table->string('education_status')->nullable();
            $table->string('school_name')->nullable();
            $table->string('academic_level')->nullable();
            $table->string('school_phone')->nullable();
            $table->string('quran_school')->nullable();
            $table->string('quran_memorization')->nullable();
            $table->text('health_status')->nullable();
            $table->string('talents')->nullable();

            // بيانات الأب والأم
            $table->string('father_death_cause')->nullable();
            $table->date('father_death_date')->nullable();
            $table->string('father_job_before')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_status')->nullable();
            $table->string('mother_job')->nullable();
            $table->decimal('mother_income', 10, 2)->nullable();
            $table->string('housing_type')->nullable();
            $table->decimal('family_income_avg', 10, 2)->nullable();

            // بيانات المعيل
            $table->string('guardian_name')->nullable();
            $table->string('guardian_relation')->nullable();
            $table->string('guardian_card_id')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->text('guardian_reason')->nullable();

            // المعرفون والمرفقات (JSON لتسهيل التخزين السريع)
            $table->json('witnesses')->nullable(); 
            $table->json('attachments')->nullable();

            // الحالة والربط بالفرع
            // عدلنا الحالة لتبدأ بـ waiting لتتوافق مع الـ Badge في الواجهة
            $table->string('status')->default('waiting'); 
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade'); 
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orphans');
    }
};