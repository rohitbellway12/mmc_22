<?php

use Illuminate\Support\Facades\Route;
use Modules\CarHire\Http\Controllers\CarHireController;

// Route::group(['prefix' => 'customer', 'as' => 'customer.', 'namespace' => 'Api\V1\Customer', 'middleware' => ['auth:api']], function () {
//     Route::group(['prefix' => 'car', 'as' => 'car.'], function () {
//         Route::get('list', [\Modules\CarHire\Http\Controllers\Api\V1\CustomerCarController::class, 'index']);
//         Route::get('details/{id}', [\Modules\CarHire\Http\Controllers\Api\V1\CustomerCarController::class, 'show']);
//         Route::post('book', [\Modules\CarHire\Http\Controllers\Api\V1\CustomerCarController::class, 'store']);

//         // Chauffeur Service Specific Routes
//         Route::get('types', [\Modules\CarHire\Http\Controllers\Api\V1\CustomerCarController::class, 'getCarTypes']);
//         Route::get('chauffeur/search', [\Modules\CarHire\Http\Controllers\Api\V1\CustomerCarController::class, 'searchChauffeurCars']);
//         Route::post('chauffeur/book', [\Modules\CarHire\Http\Controllers\Api\V1\CustomerCarController::class, 'bookChauffeur']);
//     });
// });

// In your routes file (remove the api/v1 prefix)
// Route::group(['prefix' => 'customer', 'as' => 'customer.', 'middleware' => ['auth:api']], function () {
//     Route::group(['prefix' => 'car', 'as' => 'car.'], function () {
//         Route::get('list', [\Modules\CarHire\Http\Controllers\Api\V1\CustomerCarController::class, 'index']);
//         Route::get('details/{id}', [\Modules\CarHire\Http\Controllers\Api\V1\CustomerCarController::class, 'show']);
//         Route::post('book', [\Modules\CarHire\Http\Controllers\Api\V1\CustomerCarController::class, 'store']);

//         // Chauffeur Service Specific Routes
//         Route::get('types', [\Modules\CarHire\Http\Controllers\Api\V1\CustomerCarController::class, 'getCarTypes']);
//         Route::get('chauffeur/search', [\Modules\CarHire\Http\Controllers\Api\V1\CustomerCarController::class, 'searchChauffeurCars']);
//         Route::post('chauffeur/book', [\Modules\CarHire\Http\Controllers\Api\V1\CustomerCarController::class, 'bookChauffeur']);
//     });
// });