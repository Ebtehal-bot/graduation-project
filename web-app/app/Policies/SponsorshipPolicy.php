<?php

namespace App\Policies;

use App\Models\Sponsorship;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SponsorshipPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'supervisor', 'employee']);
    }

    public function view(User $user, Sponsorship $sponsorship): bool
    {
        return $user->hasAnyRole(['super_admin', 'supervisor', 'employee']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'employee']);
    }

    public function update(User $user, Sponsorship $sponsorship): bool
    {
        return $user->hasAnyRole(['super_admin', 'supervisor']);
    }

    public function delete(User $user, Sponsorship $sponsorship): bool
    {
        return $user->hasRole('super_admin');
    }

    public function restore(User $user, Sponsorship $sponsorship): bool
    {
        return $user->hasRole('super_admin');
    }

    public function forceDelete(User $user, Sponsorship $sponsorship): bool
    {
        return $user->hasRole('super_admin');
    }
}
