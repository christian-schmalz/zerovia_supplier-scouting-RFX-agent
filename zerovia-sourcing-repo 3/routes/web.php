<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — ZEROvia Sourcing & RFX Agent
|--------------------------------------------------------------------------
| Filament handles its own routing via /app panel prefix.
| This file covers the prototype viewer and any public-facing pages.
*/

Route::get('/', function () {
    return redirect('/app');
});

// Serve the HTML prototype directly for demo purposes
Route::get('/prototype', function () {
    return response()->file(public_path('supplier-agent-prototype.html'));
})->name('prototype');

// Filament panel is registered at /app via AdminPanelProvider
