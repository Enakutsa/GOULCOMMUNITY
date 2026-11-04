<?php

use App\Http\Controllers\Api\EtablissementController;
use Illuminate\Support\Facades\Route;

// Annuaire public (lecture seule, sans compte)
Route::get('/etablissements', [EtablissementController::class, 'index']);
Route::get('/etablissements/{slug}', [EtablissementController::class, 'show']);