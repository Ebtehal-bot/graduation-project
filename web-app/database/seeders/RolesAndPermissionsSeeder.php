<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(PermissionRegistrar $permissionRegistrar)
    {
        app()['cache']->forget('spatie.permission.cache');

        $resources = ['orphans', 'sponsors', 'sponsorships', 'payments', 'users', 'branches', 'settings'];
        $actions = ['view_any', 'view', 'create', 'update', 'delete'];

        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                Permission::findOrCreate("{$action}_{$resource}", 'web');
            }
        }

        $superAdmin = Role::findOrCreate('super_admin', 'web');
        $superAdmin->syncPermissions(Permission::all());

        $supervisor = Role::findOrCreate('supervisor', 'web');
        $supervisor->syncPermissions([
            'view_any_orphans', 'view_orphans', 'update_orphans',
            'view_any_sponsors', 'view_sponsors', 'update_sponsors',
            'view_any_sponsorships', 'view_sponsorships', 'update_sponsorships',
            'view_any_payments', 'view_payments', 'update_payments',
            'view_any_branches', 'view_branches',
        ]);

        $employee = Role::findOrCreate('employee', 'web');
        $employee->syncPermissions([
            'view_any_orphans', 'view_orphans', 'create_orphans',
            'view_any_sponsors', 'view_sponsors',
            'view_any_sponsorships', 'view_sponsorships', 'create_sponsorships',
            'view_any_payments', 'view_payments', 'create_payments',
            'view_any_branches', 'view_branches',
        ]);

        Role::findOrCreate('sponsor', 'web');

        $user = User::find(1);

        if (!$user) {
            $user = User::where('email', '123@gmail.com')->first();
        }

        if ($user) {
            $user->syncRoles('super_admin');
            $msg = "Super Admin assigned to: {$user->name} ({$user->email})";
            if ($this->command) {
                $this->command->info($msg);
            } else {
                echo $msg . PHP_EOL;
            }
        }

        $permissionRegistrar->forgetCachedPermissions();
    }
}
