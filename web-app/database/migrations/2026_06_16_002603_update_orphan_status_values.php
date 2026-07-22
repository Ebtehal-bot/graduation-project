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
        DB::statement("ALTER TABLE orphans MODIFY status VARCHAR(50) NOT NULL DEFAULT 'available'");
        DB::statement("UPDATE orphans SET status = 'available' WHERE status IN ('waiting', 'pending')");
        DB::statement("UPDATE orphans SET status = 'sponsored' WHERE id IN (SELECT orphan_id FROM sponsorships WHERE status = 'active')");
    }

    public function down()
    {
        DB::statement("UPDATE orphans SET status = 'waiting' WHERE status IN ('available', 'sponsored', 'inactive', 'graduated')");
        DB::statement("ALTER TABLE orphans MODIFY status VARCHAR(255) NOT NULL DEFAULT 'waiting'");
    }
};
