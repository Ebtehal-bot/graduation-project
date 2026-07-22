<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class FixAdminRole extends Command
{
    protected $signature = 'rbac:fix-admin {email?}';
    protected $description = 'Assign super_admin role to an admin user by email to fix 403 lockout';

    public function handle(PermissionRegistrar $permissionRegistrar)
    {
        $rolesExist = Role::whereIn('name', ['super_admin', 'supervisor', 'employee', 'sponsor'])->count();
        if ($rolesExist < 4) {
            $this->call('db:seed', ['--class' => 'RoleSeeder']);
            $this->info('Roles seeded.');
        }

        $email = $this->argument('email') ?? $this->ask('Enter admin email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("No user found with email: $email");
            return 1;
        }

        $user->assignRole('super_admin');
        $permissionRegistrar->forgetCachedPermissions();

        $this->info("Super Admin role assigned to: {$user->name} ({$user->email})");
        $this->warn('Clear your browser cache and re-login to Filament admin panel.');
        return 0;
    }
}
