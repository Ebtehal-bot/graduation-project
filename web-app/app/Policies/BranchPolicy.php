<?php

namespace App\Policies;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BranchPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'supervisor', 'employee']);
    }

    public function view(User $user, Branch $branch): bool
    {
        return $user->hasAnyRole(['super_admin', 'supervisor', 'employee']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function update(User $user, Branch $branch): bool
    {
        return $user->hasRole('super_admin');
    }

    public function delete(User $user, Branch $branch): bool
    {
        return $user->hasRole('super_admin');
    }
}
