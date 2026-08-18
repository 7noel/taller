<?php

use App\Http\Controllers\PartyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('parties', PartyController::class);
    Route::resource('vehicles', VehicleController::class);

    Route::get('api/parties/search', [PartyController::class, 'search'])->name('api.parties.search');
    Route::post('api/parties/quick-store', [PartyController::class, 'quickStore'])->name('api.parties.quick-store');
    Route::get('api/vehicles/search', [VehicleController::class, 'search'])->name('api.vehicles.search');
    Route::get('api/ubigeo/provincias', [PartyController::class, 'provincias'])->name('api.ubigeo.provincias');
    Route::get('api/ubigeo/distritos', [PartyController::class, 'distritos'])->name('api.ubigeo.distritos');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';