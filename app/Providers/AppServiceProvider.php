<?php

namespace App\Providers;

use App\Jobs\FetchExchangeRateJob;
use App\Models\Brand;
use App\Models\CheckIn;
use App\Models\CheckInChecklistItem;
use App\Models\Estimate;
use App\Models\FormTemplate;
use App\Models\Part;
use App\Models\PartBrand;
use App\Models\PartCategory;
use App\Models\Party;
use App\Models\RepairService;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use App\Models\Warehouse;
use App\Models\ProviderSettlement;
use App\Models\ServiceVoucher;
use App\Models\WarehouseStock;
use App\Models\WorkOrder;
use App\Models\InventoryGuide;
use App\Models\PartOrder;
use App\Models\PurchaseOrder;
use App\Policies\BrandPolicy;
use App\Policies\CheckInChecklistItemPolicy;
use App\Policies\CheckInPolicy;
use App\Policies\EstimatePolicy;
use App\Policies\FormTemplatePolicy;
use App\Policies\PartBrandPolicy;
use App\Policies\PartCategoryPolicy;
use App\Policies\PartPolicy;
use App\Policies\PartyPolicy;
use App\Policies\RepairServicePolicy;
use App\Policies\ServiceCategoryPolicy;
use App\Policies\StockPolicy;
use App\Policies\UserPolicy;
use App\Policies\VehicleModelPolicy;
use App\Policies\VehiclePolicy;
use App\Policies\WarehousePolicy;
use App\Policies\ProviderSettlementPolicy;
use App\Policies\ServiceVoucherPolicy;
use App\Policies\WorkOrderPolicy;
use App\Policies\InventoryGuidePolicy;
use App\Policies\PartOrderPolicy;
use App\Policies\PurchaseOrderPolicy;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
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
        Gate::policy(Estimate::class, EstimatePolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Part::class, PartPolicy::class);
        Gate::policy(RepairService::class, RepairServicePolicy::class);
        Gate::policy(Warehouse::class, WarehousePolicy::class);
        Gate::policy(WarehouseStock::class, StockPolicy::class);
        Gate::policy(ServiceCategory::class, ServiceCategoryPolicy::class);
        Gate::policy(PartCategory::class, PartCategoryPolicy::class);
        Gate::policy(PartBrand::class, PartBrandPolicy::class);
        Gate::policy(WorkOrder::class, WorkOrderPolicy::class);
        Gate::policy(ServiceVoucher::class, ServiceVoucherPolicy::class);
        Gate::policy(ProviderSettlement::class, ProviderSettlementPolicy::class);
        Gate::policy(FormTemplate::class, FormTemplatePolicy::class);
        Gate::policy(CheckInChecklistItem::class, CheckInChecklistItemPolicy::class);
        Gate::policy(PurchaseOrder::class, PurchaseOrderPolicy::class);
        Gate::policy(InventoryGuide::class, InventoryGuidePolicy::class);
        Gate::policy(PartOrder::class, PartOrderPolicy::class);

        // Precarga el tipo de cambio del día al iniciar sesión (cola database).
        Event::listen(Login::class, function (): void {
            FetchExchangeRateJob::dispatch();
        });
    }
}