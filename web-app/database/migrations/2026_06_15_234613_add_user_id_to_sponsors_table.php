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
        Schema::table('sponsors', function (Blueprint $table) {
            if (!Schema::hasColumn('sponsors', 'user_id')) {
                $table->foreignId('user_id')->nullable()->unique()->constrained('users')->nullOnDelete()->after('id');
            }
        });

        DB::statement('ALTER TABLE sponsors MODIFY phone VARCHAR(255) NULL');
        DB::statement('ALTER TABLE sponsors MODIFY address VARCHAR(255) NULL');
    }

    public function down()
    {
        Schema::table('sponsors', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        DB::statement('ALTER TABLE sponsors MODIFY phone VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE sponsors MODIFY address VARCHAR(255) NOT NULL');
    }
};
