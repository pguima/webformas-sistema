<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['api_token'])->group(function () {
    Route::get('/leads', [\App\Http\Controllers\Api\LeadController::class, 'index'])->middleware('api_token:leads.read');
    Route::post('/leads', [\App\Http\Controllers\Api\LeadController::class, 'store'])->middleware('api_token:leads.write');
    Route::put('/leads/{lead}', [\App\Http\Controllers\Api\LeadController::class, 'update'])->middleware('api_token:leads.write');
    Route::delete('/leads/{lead}', [\App\Http\Controllers\Api\LeadController::class, 'destroy'])->middleware('api_token:leads.write');
});
