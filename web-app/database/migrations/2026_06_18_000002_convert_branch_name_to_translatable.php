<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up()
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->text('name_i18n')->nullable()->after('id');
        });

        DB::table('branches')->orderBy('id')->chunk(100, function ($branches) {
            foreach ($branches as $branch) {
                $name = $branch->name;
                $jsonValue = null;

                if (!empty($name)) {
                    if ($this->isJson($name)) {
                        $decoded = json_decode($name, true);
                        if (!isset($decoded['ar'])) {
                            $decoded['ar'] = $name;
                        }
                        $jsonValue = json_encode($decoded, JSON_UNESCAPED_UNICODE);
                    } else {
                        $jsonValue = json_encode(['ar' => $name], JSON_UNESCAPED_UNICODE);
                    }
                } else {
                    $jsonValue = json_encode(['ar' => ''], JSON_UNESCAPED_UNICODE);
                }

                DB::table('branches')
                    ->where('id', $branch->id)
                    ->update(['name_i18n' => $jsonValue]);
            }
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn('name');
        });

        DB::statement('ALTER TABLE branches CHANGE COLUMN name_i18n name TEXT NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE branches ADD COLUMN name_old VARCHAR(255) NULL AFTER id');

        DB::table('branches')->orderBy('id')->chunk(100, function ($branches) {
            foreach ($branches as $branch) {
                $original = $branch->name;
                if ($this->isJson($original)) {
                    $decoded = json_decode($original, true);
                    $original = $decoded['ar'] ?? (reset($decoded) ?: '');
                }
                DB::table('branches')
                    ->where('id', $branch->id)
                    ->update(['name_old' => $original]);
            }
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn('name');
        });

        DB::statement('ALTER TABLE branches CHANGE COLUMN name_old name VARCHAR(255) NULL');
    }

    private function isJson(string $value): bool
    {
        json_decode($value);
        return json_last_error() === JSON_ERROR_NONE;
    }
};
