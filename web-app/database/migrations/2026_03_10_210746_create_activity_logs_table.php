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
        Schema::create('activity_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users'); // المستخدم اللي قام بالعملية
    $table->string('action'); // (إضافة، تعديل، حذف)
    $table->string('entity_type'); // (يتيم، كفيل، فرع)
    $table->unsignedBigInteger('entity_id'); // رقم السجل المتأثر
    $table->timestamps(); // الوقت والتاريخ
});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
};
