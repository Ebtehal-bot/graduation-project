<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SyncExistingRolesToSpatie extends Migration
{
    public function up()
    {
        $roleMap = [
            'admin' => 'super_admin',
            'supervisor' => 'supervisor',
            'staff' => 'employee',
            'sponsor' => 'sponsor',
        ];

        $users = DB::table('users')->whereNotNull('role')->get();

        foreach ($users as $user) {
            $spatieRole = $roleMap[$user->role] ?? 'employee';

            $role = DB::table('roles')->where('name', $spatieRole)->first();
            if ($role) {
                DB::table('model_has_roles')->updateOrInsert([
                    'role_id' => $role->id,
                    'model_type' => 'App\Models\User',
                    'model_id' => $user->id,
                ]);
            }
        }
    }

    public function down()
    {
        DB::table('model_has_roles')->where('model_type', 'App\Models\User')->delete();
    }
}
