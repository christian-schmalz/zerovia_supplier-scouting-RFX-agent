<?php

use App\Http\Controllers\SourcingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — ZEROvia Sourcing & RFX Agent
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // NOGA taxonomy search
    Route::get('/noga/search', [SourcingController::class, 'nogaSearch']);

    // Supplier search
    Route::post('/sourcing/search', [SourcingController::class, 'search']);

    // RFQ generation and dispatch
    Route::post('/sourcing/rfq/generate', [SourcingController::class, 'generateRfq']);
    Route::post('/sourcing/rfq/{rfq}/send', [SourcingController::class, 'sendRfq']);

});

// Open-tracking pixel (no auth required — called from email client)
Route::get('/rfq/track/{rfq}/{recipient}', [SourcingController::class, 'trackOpen'])
    ->name('rfq.track');
