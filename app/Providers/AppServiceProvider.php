<?php

namespace App\Providers;

use App\Models\Party;
use App\Models\Vehicle;
use App\Policies\PartyPolicy;
use App\Policies\VehiclePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Party::class, PartyPolicy::class);
        Gate::policy(Vehicle::class, VehiclePolicy::class);
    }
}
