<?php

namespace App\Policies;

use App\Models\Orphan;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrphanPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'supervisor', 'employee']);
    }

    public function view(User $user, Orphan $orphan): bool
    {
        return $user->hasAnyRole(['super_admin', 'supervisor', 'employee']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'supervisor', 'employee']);
    }

    public function update(User $user, Orphan $orphan): bool
    {
        return $user->hasAnyRole(['super_admin', 'supervisor']);
    }

    public function delete(User $user, Orphan $orphan): bool
    {
        return $user->hasRole('super_admin');
    }

    public function restore(User $user, Orphan $orphan): bool
    {
        return $user->hasRole('super_admin');
    }

    public function forceDelete(User $user, Orphan $orphan): bool
    {
        return $user->hasRole('super_admin');
    }
}
