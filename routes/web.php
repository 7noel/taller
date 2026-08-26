<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\CompanySettingController;
use App\Http\Controllers\DocumentSeriesController;
use App\Http\Controllers\EstablishmentController;
use App\Http\Controllers\EstimateController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RepairServiceController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\WarehouseController;
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

    Route::post('{token}/check-ins/{checkIn}/approve', [PortalController::class, 'approveCheckIn'])->middleware('throttle:10,1')->name('public.portal.check-in.approve');
    Route::post('{token}/check-ins/{checkIn}/reject', [PortalController::class, 'rejectCheckIn'])->middleware('throttle:10,1')->name('public.portal.check-in.reject');
    Route::post('{token}/estimates/{estimate}/approve', [PortalController::class, 'approveEstimate'])->middleware('throttle:10,1')->name('public.portal.estimate.approve');
    Route::post('{token}/estimates/{estimate}/reject', [PortalController::class, 'rejectEstimate'])->middleware('throttle:10,1')->name('public.portal.estimate.reject');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('parties', PartyController::class);
    Route::resource('vehicles', VehicleController::class);
    Route::post('vehicles/{vehicle}/token/regenerate', [VehicleController::class, 'regenerateToken'])->name('vehicles.token.regenerate');
    Route::post('vehicles/{vehicle}/token/revoke', [VehicleController::class, 'revokeToken'])->name('vehicles.token.revoke');
    Route::resource('check-ins', CheckInController::class);
    Route::resource('estimates', EstimateController::class);
    Route::resource('users', UserController::class);

    Route::resource('repair-services', RepairServiceController::class);
    Route::resource('parts', PartController::class);
    Route::resource('warehouses', WarehouseController::class);
    Route::resource('stock', StockController::class)->only(['index', 'store']);

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