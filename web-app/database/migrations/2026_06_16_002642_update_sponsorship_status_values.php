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
        DB::statement("ALTER TABLE sponsorships MODIFY status VARCHAR(20) NOT NULL DEFAULT 'active'");
        DB::statement("UPDATE sponsorships SET status = 'ended' WHERE status IN ('stopped', 'cancelled', 'completed')");
        DB::statement("UPDATE sponsorships SET status = 'inactive' WHERE status = 'pending'");
    }

    public function down()
    {
        DB::statement("UPDATE sponsorships SET status = 'stopped' WHERE status = 'ended'");
        DB::statement("UPDATE sponsorships SET status = 'pending' WHERE status = 'inactive'");
        DB::statement("ALTER TABLE sponsorships MODIFY status VARCHAR(255) NOT NULL DEFAULT 'active'");
    }
};
