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
        Schema::create('thank_you_messages', function (Blueprint $table) {
    $table->id();
    $table->foreignId('orphan_id')->constrained('orphans')->onDelete('cascade');
    $table->foreignId('sponsor_id')->constrained('sponsors')->onDelete('cascade');
    $table->text('text'); // نص الرسالة
    $table->string('image')->nullable(); // صورة الرسالة إن وجدت
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
        Schema::dropIfExists('thank_you_messages');
    }
};
