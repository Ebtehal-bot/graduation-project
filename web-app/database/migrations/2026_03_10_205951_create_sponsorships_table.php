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
    Schema::create('sponsorships', function (Blueprint $table) {
        $table->id(); // SponsorshipID
        $table->foreignId('orphan_id')->constrained('orphans')->onDelete('cascade');
        $table->foreignId('sponsor_id')->constrained('sponsors')->onDelete('cascade');
        $table->date('start_date'); // تاريخ البداية
        $table->date('end_date')->nullable(); // تاريخ النهاية
        $table->decimal('monthly_amount', 10, 2); // المبلغ الشهري
        $table->string('status'); // الحالة (نشط، متوقف، إلخ)
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
        Schema::dropIfExists('sponsorships');
    }
};
