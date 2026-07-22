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
        Schema::create('attachments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('orphan_id')->constrained('orphans')->onDelete('cascade');
    $table->string('file_path'); // رابط الملف في السيرفر
    $table->string('document_type'); // (شهادة ميلاد، تقرير طبي، إلخ)
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
        Schema::dropIfExists('attachments');
    }
};
