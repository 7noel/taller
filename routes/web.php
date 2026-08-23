<?php

use App\Http\Controllers\CheckInController;
use App\Http\Controllers\PartyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
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

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('parties', PartyController::class);
    Route::resource('vehicles', VehicleController::class);
    Route::resource('check-ins', CheckInController::class);
    Route::resource('users', UserController::class);
    Route::get('api/users/data', [UserController::class, 'fetchData'])->name('api.users.data');

    Route::post('check-ins/{checkIn}/approve', [CheckInController::class, 'approve'])->name('check-ins.approve');
    Route::post('check-ins/{checkIn}/reject', [CheckInController::class, 'reject'])->name('check-ins.reject');
    Route::post('check-ins/{checkIn}/send-to-client', [CheckInController::class, 'sendToClient'])->name('check-ins.send-to-client');

    Route::get('api/check-ins/search', [CheckInController::class, 'search'])->name('api.check-ins.search');
    Route::get('api/check-ins/contacts', [CheckInController::class, 'contacts'])->name('api.check-ins.contacts');
    Route::get('api/check-ins/insurance-companies', [CheckInController::class, 'insuranceCompanies'])->name('api.check-ins.insurance-companies');
    Route::delete('api/check-ins/contacts', [CheckInController::class, 'removeContact'])->name('api.check-ins.contacts.remove');
    Route::post('api/check-ins/{checkIn}/photos', [CheckInController::class, 'uploadPhoto'])->name('api.check-ins.photos.store');
    Route::delete('api/check-ins/{checkIn}/photos/{photo}', [CheckInController::class, 'destroyPhoto'])->name('api.check-ins.photos.destroy');

    Route::get('api/parties/search', [PartyController::class, 'search'])->name('api.parties.search');
    Route::post('api/parties/quick-store', [PartyController::class, 'quickStore'])->name('api.parties.quick-store');
    Route::put('api/parties/{party}/quick-update', [PartyController::class, 'quickUpdate'])->name('api.parties.quick-update');
    Route::get('api/vehicles/search', [VehicleController::class, 'search'])->name('api.vehicles.search');
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