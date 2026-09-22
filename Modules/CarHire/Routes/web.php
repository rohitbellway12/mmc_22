<?php

use Illuminate\Support\Facades\Route;
use Modules\CarHire\Http\Controllers\Web\Admin\CarFeatureController;
use Modules\CarHire\Http\Controllers\Web\Admin\CarYearController;
use Modules\CarHire\Http\Controllers\Web\Admin\CarAjaxController;
use Modules\CarHire\Http\Controllers\Web\Admin\CarBrandController;
use Modules\CarHire\Http\Controllers\Web\Admin\CarFuleTypeController;
use Modules\CarHire\Http\Controllers\Web\Admin\CarTypeController;
use Modules\CarHire\Http\Controllers\Web\Admin\CarHireController;
use Modules\CarHire\Http\Controllers\Web\Admin\CarModelController;
use Modules\CarHire\Http\Controllers\Web\Admin\CarTransmissionController;
use Modules\CarHire\Http\Controllers\Web\Admin\YearController;

// Route::middleware(['web'])
//     ->group(function () {
//         Route::get('carhire', [CarHireController::class, 'index'])->name('carhire.index');
//     });

use Modules\CarHire\Http\Controllers\Web\Provider\ProviderCarController;
use Modules\CarHire\Http\Controllers\Web\Provider\ProviderCarBookingController;
use Modules\CarHire\Http\Controllers\Web\Admin\AdminCarBookingController;

Route::group(['prefix' => 'provider', 'as' => 'provider.', 'middleware' => ['provider']], function () {
    Route::group(['prefix' => 'car', 'as' => 'car.'], function () {
        Route::get('index', [ProviderCarController::class, 'index'])->name('index');
        Route::get('create', [ProviderCarController::class, 'create'])->name('create');
        Route::get('create/car-hire', [ProviderCarController::class, 'createCarHire'])->name('create-car-hire');
        Route::get('create/chauffeur', [ProviderCarController::class, 'createChauffeur'])->name('create-chauffeur');
        Route::post('store', [ProviderCarController::class, 'store'])->name('store');
        Route::get('edit/{id}', [ProviderCarController::class, 'edit'])->name('edit');
        Route::put('update/{id}', [ProviderCarController::class, 'update'])->name('update');
        Route::delete('delete/{id}', [ProviderCarController::class, 'destroy'])->name('destroy');

        // Car Booking Management
        Route::group(['prefix' => 'booking', 'as' => 'booking.'], function () {
            Route::get('list', [ProviderCarBookingController::class, 'index'])->name('list');
            Route::get('details/{id}', [ProviderCarBookingController::class, 'details'])->name('details');
            Route::post('status-update/{id}', [ProviderCarBookingController::class, 'statusUpdate'])->name('status-update');
        });
    });
});

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['admin']], function () {
    Route::group(['prefix' => 'car', 'as' => 'car.'], function () {
        Route::group(['prefix' => 'booking', 'as' => 'booking.'], function () {
            Route::get('list', [AdminCarBookingController::class, 'index'])->name('list');
            Route::get('details/{id}', [AdminCarBookingController::class, 'details'])->name('details');
            Route::post('status-update/{id}', [AdminCarBookingController::class, 'statusUpdate'])->name('status-update');
        });
    });
});
