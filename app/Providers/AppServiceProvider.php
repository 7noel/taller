<?php

namespace App\Providers;

use App\Models\Brand;
use App\Models\CheckIn;
use App\Models\Party;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use App\Policies\BrandPolicy;
use App\Policies\CheckInPolicy;
use App\Policies\PartyPolicy;
use App\Policies\VehicleModelPolicy;
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
        Gate::policy(Brand::class, BrandPolicy::class);
        Gate::policy(VehicleModel::class, VehicleModelPolicy::class);
        Gate::policy(Party::class, PartyPolicy::class);
        Gate::policy(Vehicle::class, VehiclePolicy::class);
        Gate::policy(CheckIn::class, CheckInPolicy::class);
    }
}
