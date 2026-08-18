<?php

use App\Http\Controllers\ClientController;
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
    Route::resource('clients', ClientController::class);
    Route::resource('vehicles', VehicleController::class);

    Route::get('api/clients/search', [ClientController::class, 'search'])->name('api.clients.search');
    Route::get('api/vehicles/search', [VehicleController::class, 'search'])->name('api.vehicles.search');
    Route::get('api/ubigeo/provincias', [ClientController::class, 'provincias'])->name('api.ubigeo.provincias');
    Route::get('api/ubigeo/distritos', [ClientController::class, 'distritos'])->name('api.ubigeo.distritos');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
