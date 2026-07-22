<?php

namespace App\Providers;

use App\Models\Orphan;
use App\Models\Payment;
use App\Models\Sponsorship;
use App\Models\User;
use App\Policies\OrphanPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\SponsorshipPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Orphan::class => OrphanPolicy::class,
        Payment::class => PaymentPolicy::class,
        Sponsorship::class => SponsorshipPolicy::class,
        User::class => UserPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
