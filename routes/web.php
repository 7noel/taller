<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckInChecklistItemController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\CompanySettingController;
use App\Http\Controllers\DocumentSeriesController;
use App\Http\Controllers\EstablishmentController;
use App\Http\Controllers\EstimateController;
use App\Http\Controllers\FormTemplateController;
use App\Http\Controllers\InventoryGuideController;
use App\Http\Controllers\KanbanController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\PartOrderController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\RepairServiceController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\ProviderSettlementController;
use App\Http\Controllers\ServiceVoucherController;
use App\Http\Controllers\WorkOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('check-ins.index');
    }

    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Renueva el token CSRF (público): se usa justo antes de enviar formularios
// para evitar errores 419 cuando la sesión estuvo inactiva mucho tiempo.
Route::get('/api/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
})->name('api.csrf-token');

// ===== Portal público del cliente (un enlace por vehículo) =====
// Sin autenticación: la seguridad se basa en el token aleatorio de vehicles.access_token.
Route::prefix('c')->group(function () {
    Route::get('{token}', [PortalController::class, 'show'])->name('public.portal');
    Route::get('{token}/check-ins/{checkIn}', [PortalController::class, 'showCheckIn'])->name('public.portal.check-in');
    Route::get('{token}/estimates/{estimate}', [PortalController::class, 'showEstimate'])->name('public.portal.estimate');
    Route::get('{token}/work-orders/{workOrder}', [PortalController::class, 'showWorkOrder'])->name('public.work-order');
    Route::get('{token}/work-orders/{workOrder}/survey', [PortalController::class, 'showSurvey'])->name('public.work-order.survey');

    Route::post('{token}/check-ins/{checkIn}/approve', [PortalController::class, 'approveCheckIn'])->middleware('throttle:10,1')->name('public.portal.check-in.approve');
    Route::post('{token}/check-ins/{checkIn}/reject', [PortalController::class, 'rejectCheckIn'])->middleware('throttle:10,1')->name('public.portal.check-in.reject');
    Route::post('{token}/estimates/{estimate}/approve', [PortalController::class, 'approveEstimate'])->middleware('throttle:10,1')->name('public.portal.estimate.approve');
    Route::post('{token}/estimates/{estimate}/reject', [PortalController::class, 'rejectEstimate'])->middleware('throttle:10,1')->name('public.portal.estimate.reject');
    Route::post('{token}/work-orders/{workOrder}/survey', [PortalController::class, 'submitSurvey'])->middleware('throttle:10,1')->name('public.work-order.survey.store');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('parties', PartyController::class);
    Route::resource('vehicles', VehicleController::class);
    Route::post('vehicles/{vehicle}/token/regenerate', [VehicleController::class, 'regenerateToken'])->name('vehicles.token.regenerate');
    Route::post('vehicles/{vehicle}/token/revoke', [VehicleController::class, 'revokeToken'])->name('vehicles.token.revoke');
    Route::resource('check-ins', CheckInController::class);
    Route::get('kanban', [KanbanController::class, 'index'])->middleware('can:ver tablero')->name('kanban.index');
    Route::get('api/kanban/data', [KanbanController::class, 'data'])->middleware('can:ver tablero')->name('api.kanban.data');
    Route::resource('estimates', EstimateController::class);
    Route::resource('work-orders', WorkOrderController::class)->except(['edit', 'update', 'create']);
    Route::post('work-orders/{workOrder}/attach-estimate', [WorkOrderController::class, 'attachEstimate'])->name('work-orders.attach-estimate');
    Route::delete('work-orders/{workOrder}/estimates/{estimate}', [WorkOrderController::class, 'detachEstimate'])->name('work-orders.detach-estimate');
    Route::post('work-orders/{workOrder}/transition', [WorkOrderController::class, 'transition'])->name('work-orders.transition');
    Route::get('work-orders/{workOrder}/quality-control', [WorkOrderController::class, 'qualityControl'])->name('work-orders.quality-control');
    Route::post('work-orders/{workOrder}/quality-control', [WorkOrderController::class, 'submitQualityControl'])->name('work-orders.quality-control.store');
    Route::post('work-orders/{workOrder}/whatsapp', [WorkOrderController::class, 'whatsapp'])->name('work-orders.whatsapp');
    Route::get('api/work-orders/{workOrder}/recipients', [WorkOrderController::class, 'recipients'])->name('api.work-orders.recipients');
    Route::post('work-orders/{workOrder}/assignments', [WorkOrderController::class, 'addAssignment'])->name('work-orders.assignments.store');
    Route::post('work-orders/{workOrder}/assignments/{assignment}/status', [WorkOrderController::class, 'updateAssignmentStatus'])->name('work-orders.assignments.status');
    Route::delete('work-orders/{workOrder}/assignments/{assignment}', [WorkOrderController::class, 'deleteAssignment'])->name('work-orders.assignments.destroy');
    Route::get('api/work-orders/search', [WorkOrderController::class, 'search'])->name('api.work-orders.search');

    Route::resource('service-vouchers', ServiceVoucherController::class);
    Route::post('service-vouchers/{service_voucher}/complete', [ServiceVoucherController::class, 'complete'])->name('service-vouchers.complete');
    Route::get('api/service-vouchers/search', [ServiceVoucherController::class, 'search'])->name('api.service-vouchers.search');

    Route::resource('provider-settlements', ProviderSettlementController::class);
    Route::post('provider-settlements/{provider_settlement}/approve', [ProviderSettlementController::class, 'approve'])->name('provider-settlements.approve');
    Route::post('provider-settlements/{provider_settlement}/pay', [ProviderSettlementController::class, 'pay'])->name('provider-settlements.pay');
    Route::delete('provider-settlements/{provider_settlement}/vouchers/{service_voucher}', [ProviderSettlementController::class, 'detachVoucher'])->name('provider-settlements.vouchers.detach');
    Route::get('api/provider-settlements/search', [ProviderSettlementController::class, 'search'])->name('api.provider-settlements.search');
    Route::get('api/provider-settlements/available-vouchers', [ProviderSettlementController::class, 'availableVouchers'])->name('api.provider-settlements.available-vouchers');

    Route::resource('form-templates', FormTemplateController::class);
    Route::post('form-templates/{formTemplate}/duplicate', [FormTemplateController::class, 'duplicate'])->name('form-templates.duplicate');
    Route::get('api/form-templates/search', [FormTemplateController::class, 'search'])->name('api.form-templates.search');
    Route::resource('checklist-items', CheckInChecklistItemController::class);
    Route::get('api/checklist-items/search', [CheckInChecklistItemController::class, 'search'])->name('api.checklist-items.search');
    Route::resource('users', UserController::class);

    Route::resource('repair-services', RepairServiceController::class);
    Route::resource('parts', PartController::class);
    Route::resource('warehouses', WarehouseController::class);
    Route::resource('stock', StockController::class)->only(['index', 'store']);

    // Kardex / movimientos y alertas de stock mínimo
    Route::get('stock/movements', [StockController::class, 'movements'])->name('stock.movements');
    Route::get('api/stock/movements', [StockController::class, 'movementsJson'])->name('api.stock.movements');
    Route::get('api/stock/alerts', [StockController::class, 'alerts'])->name('api.stock.alerts');

    // Compras (OC01)
    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::post('purchase-orders/{purchase_order}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');
    Route::post('purchase-orders/{purchase_order}/cancel', [PurchaseOrderController::class, 'cancel'])->name('purchase-orders.cancel');
    Route::get('api/purchase-orders/search', [PurchaseOrderController::class, 'search'])->name('api.purchase-orders.search');

    // Guías de inventario (NIA1 / NSA1 / NTA1)
    Route::resource('inventory-guides', InventoryGuideController::class);
    Route::get('api/inventory-guides/search', [InventoryGuideController::class, 'search'])->name('api.inventory-guides.search');

    // Pedidos de repuestos de seguro (PartOrder)
    Route::resource('part-orders', PartOrderController::class)->only(['index', 'store', 'destroy']);
    Route::post('part-orders/{part_order}/status', [PartOrderController::class, 'updateStatus'])->name('part-orders.status');
    Route::get('api/part-orders/search', [PartOrderController::class, 'search'])->name('api.part-orders.search');

    // Salida de repuestos vinculada a la OT (NSA1 motivo 10)
    Route::post('work-orders/{workOrder}/stock-exit', [WorkOrderController::class, 'stockExit'])->name('work-orders.stock-exit');

    Route::resource('service-categories', CatalogController::class);
    Route::resource('part-categories', CatalogController::class);
    Route::resource('part-brands', CatalogController::class);

    Route::get('company-settings', [CompanySettingController::class, 'edit'])->name('company-settings.edit');
    Route::put('company-settings', [CompanySettingController::class, 'update'])->name('company-settings.update');

    Route::resource('establishments', EstablishmentController::class);
    Route::post('establishments/{establishment}/copy-from-company', [EstablishmentController::class, 'copyFromCompany'])->name('establishments.copy-from-company');
    Route::post('establishments/{establishment}/regenerate-series', [EstablishmentController::class, 'regenerateSeries'])->name('establishments.regenerate-series');
    Route::get('establishments/{establishment}/series', [DocumentSeriesController::class, 'index'])->name('establishments.series.index');
    Route::post('establishments/{establishment}/series', [DocumentSeriesController::class, 'store'])->name('establishments.series.store');
    Route::put('establishments/{establishment}/series/{series}', [DocumentSeriesController::class, 'update'])->name('establishments.series.update');
    Route::delete('establishments/{establishment}/series/{series}', [DocumentSeriesController::class, 'destroy'])->name('establishments.series.destroy');
    Route::get('api/users/data', [UserController::class, 'fetchData'])->name('api.users.data');

    Route::post('check-ins/{checkIn}/approve', [CheckInController::class, 'approve'])->name('check-ins.approve');
    Route::post('check-ins/{checkIn}/reject', [CheckInController::class, 'reject'])->name('check-ins.reject');
    Route::post('check-ins/{checkIn}/send-to-client', [CheckInController::class, 'sendToClient'])->name('check-ins.send-to-client');
    Route::post('check-ins/{checkIn}/close', [CheckInController::class, 'close'])->name('check-ins.close');
    Route::get('check-ins/{checkIn}/pdf', [CheckInController::class, 'pdf'])->name('check-ins.pdf');
    Route::post('check-ins/{checkIn}/whatsapp', [CheckInController::class, 'sendWhatsApp'])->name('check-ins.whatsapp');

    Route::get('api/check-ins/search', [CheckInController::class, 'search'])->name('api.check-ins.search');
    Route::get('api/check-ins/contacts', [CheckInController::class, 'contacts'])->name('api.check-ins.contacts');
    Route::get('api/check-ins/insurance-companies', [CheckInController::class, 'insuranceCompanies'])->name('api.check-ins.insurance-companies');
    Route::delete('api/check-ins/contacts', [CheckInController::class, 'removeContact'])->name('api.check-ins.contacts.remove');
    Route::post('api/check-ins/{checkIn}/photos', [CheckInController::class, 'uploadPhoto'])->name('api.check-ins.photos.store');
    Route::delete('api/check-ins/{checkIn}/photos/{photo}', [CheckInController::class, 'destroyPhoto'])->name('api.check-ins.photos.destroy');

    Route::post('estimates/{estimate}/send-to-insurance', [EstimateController::class, 'sendToInsurance'])->name('estimates.send-to-insurance');
    Route::post('estimates/{estimate}/approve-insurance', [EstimateController::class, 'approveInsurance'])->name('estimates.approve-insurance');
    Route::post('estimates/{estimate}/reject-insurance', [EstimateController::class, 'rejectInsurance'])->name('estimates.reject-insurance');
    Route::post('estimates/{estimate}/send-to-client', [EstimateController::class, 'sendToClient'])->name('estimates.send-to-client');
    Route::post('estimates/{estimate}/approve-client', [EstimateController::class, 'approveClient'])->name('estimates.approve-client');
    Route::post('estimates/{estimate}/reject-client', [EstimateController::class, 'rejectClient'])->name('estimates.reject-client');
    Route::post('estimates/{estimate}/whatsapp', [EstimateController::class, 'sendWhatsApp'])->name('estimates.whatsapp');
    Route::post('estimates/{estimate}/start-repair', [EstimateController::class, 'startRepair'])->name('estimates.start-repair');
    Route::post('estimates/{estimate}/finalize', [EstimateController::class, 'finalize'])->name('estimates.finalize');
    Route::post('estimates/{estimate}/return-to-draft', [EstimateController::class, 'returnToDraft'])->name('estimates.return-to-draft');

    Route::get('api/estimates/search', [EstimateController::class, 'search'])->name('api.estimates.search');
    Route::post('api/estimates/calculate', [EstimateController::class, 'calculate'])->name('api.estimates.calculate');
    Route::get('api/estimates/from-check-in/{checkIn}', [EstimateController::class, 'fromCheckIn'])->name('api.estimates.from-check-in');

    Route::get('api/parties/search', [PartyController::class, 'search'])->name('api.parties.search');
    Route::get('api/parties/suppliers', [PartyController::class, 'suppliers'])->name('api.parties.suppliers');
    Route::get('api/repair-services/search', [RepairServiceController::class, 'search'])->name('api.repair-services.search');
    Route::get('api/parts/search', [PartController::class, 'search'])->name('api.parts.search');
    Route::get('api/warehouses/search', [WarehouseController::class, 'search'])->name('api.warehouses.search');
    Route::get('api/stock/search', [StockController::class, 'search'])->name('api.stock.search');
    Route::get('api/service-categories/search', [CatalogController::class, 'search'])->name('api.service-categories.search');
    Route::get('api/part-categories/search', [CatalogController::class, 'search'])->name('api.part-categories.search');
    Route::get('api/part-brands/search', [CatalogController::class, 'search'])->name('api.part-brands.search');
    Route::post('api/service-categories/quick-store', [CatalogController::class, 'quickStore'])->name('api.service-categories.quick-store');
    Route::post('api/part-categories/quick-store', [CatalogController::class, 'quickStore'])->name('api.part-categories.quick-store');
    Route::post('api/part-brands/quick-store', [CatalogController::class, 'quickStore'])->name('api.part-brands.quick-store');
    Route::post('api/parties/quick-store', [PartyController::class, 'quickStore'])->name('api.parties.quick-store');
    Route::put('api/parties/{party}/quick-update', [PartyController::class, 'quickUpdate'])->name('api.parties.quick-update');
    Route::get('api/vehicles/search', [VehicleController::class, 'search'])->name('api.vehicles.search');
    Route::get('api/vehicles/{vehicle}/recipient', [VehicleController::class, 'recipient'])->name('api.vehicles.recipient');
    Route::get('api/vehicles/{vehicle}/recipients', [VehicleController::class, 'recipients'])->name('api.vehicles.recipients');
    Route::post('api/vehicles/{vehicle}/relationships', [VehicleController::class, 'attachRelationship'])->name('api.vehicles.relationships.attach');
    Route::get('api/brands', [VehicleController::class, 'brands'])->name('api.brands');
    Route::get('api/models', [VehicleController::class, 'models'])->name('api.models');
    Route::post('api/brands/find-or-create', [VehicleController::class, 'findOrCreateBrand'])->name('api.brands.find-or-create');
    Route::post('api/models/find-or-create', [VehicleController::class, 'findOrCreateModel'])->name('api.models.find-or-create');
    Route::post('api/vehicles/quick-store', [VehicleController::class, 'quickStore'])->name('api.vehicles.quick-store');
    Route::put('api/vehicles/{vehicle}/quick-update', [VehicleController::class, 'quickUpdate'])->name('api.vehicles.quick-update');
    Route::post('api/party/search-by-document', [PartyController::class, 'searchByDocument'])->name('api.party.search-by-document');
    Route::get('api/tipo-cambio', [PartyController::class, 'tipoCambio'])->name('api.tipo-cambio');
    Route::get('api/ubigeo/resolve', [PartyController::class, 'resolveUbigeo'])->name('api.ubigeo.resolve');
    Route::get('api/ubigeo/departamentos', [PartyController::class, 'departamentos'])->name('api.ubigeo.departamentos');
    Route::get('api/ubigeo/provincias', [PartyController::class, 'provincias'])->name('api.ubigeo.provincias');
    Route::get('api/ubigeo/distritos', [PartyController::class, 'distritos'])->name('api.ubigeo.distritos');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/api/keep-alive', function () {
        return response()->json(['status' => 'ok']);
    })->name('api.keep-alive');
});

require __DIR__.'/auth.php';