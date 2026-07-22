<?php

namespace App\Policies;

use App\Models\Sponsor;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SponsorPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'supervisor', 'employee']);
    }

    public function view(User $user, Sponsor $sponsor): bool
    {
        return $user->hasAnyRole(['super_admin', 'supervisor', 'employee']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasRole('employee');
    }

    public function update(User $user, Sponsor $sponsor): bool
    {
        return $user->hasRole('super_admin') || $user->hasRole('supervisor');
    }

    public function delete(User $user, Sponsor $sponsor): bool
    {
        return $user->hasRole('super_admin');
    }
}
