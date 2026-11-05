<?php

use App\Http\Controllers\Api\AbonnementController;
use App\Http\Controllers\Api\ClubController;
use App\Http\Controllers\Api\EtablissementController;
use App\Http\Controllers\Api\FilActualiteController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PropositionEtablissementController;
use App\Http\Controllers\Api\ProspectController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Authentification
Route::prefix('auth')->group(function () {
    Route::post('/inscription', [AuthController::class, 'inscription'])->middleware('throttle:10,1');
    Route::post('/envoyer-code', [AuthController::class, 'envoyerCode'])->middleware('throttle:5,1');
    Route::post('/verifier-code', [AuthController::class, 'verifierCode'])->middleware('throttle:10,1');
    Route::get('/dev-code', [AuthController::class, 'codeDev']);
});

// Annuaire public (lecture seule, sans compte)
Route::get('/etablissements', [EtablissementController::class, 'index']);
Route::get('/etablissements/{slug}', [EtablissementController::class, 'show']);

Route::get('/clubs', [ClubController::class, 'index']);
Route::get('/clubs/{slug}', [ClubController::class, 'show']);

// Formulaire d'intérêt (public)
Route::post('/prospects', [ProspectController::class, 'store'])->middleware('throttle:5,1');
Route::post('/prospects/desabonnement/{token}', [ProspectController::class, 'desabonner'])->middleware('throttle:10,1');

// Routes réservées aux utilisateurs connectés
Route::middleware('auth:sanctum')->group(function () {
    // Abonnements
    Route::get('/abonnements', [AbonnementController::class, 'index']);
    Route::post('/abonnements', [AbonnementController::class, 'store']);
    Route::delete('/abonnements/{type}/{id}', [AbonnementController::class, 'destroy'])
        ->whereIn('type', ['etablissement', 'club'])
        ->whereNumber('id');

    // Fil d'actualité et lecture
    Route::get('/fil', [FilActualiteController::class, 'index']);
    Route::post('/publications/{id}/lu', [FilActualiteController::class, 'marquerLu'])->whereNumber('id');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/compteur', [NotificationController::class, 'compteur']);
    Route::post('/notifications/tout-lu', [NotificationController::class, 'toutLu']);
    Route::post('/notifications/{id}/lue', [NotificationController::class, 'marquerLue'])->whereNumber('id');

    // Création de pages
    Route::post('/clubs', [ClubController::class, 'store'])->middleware('throttle:10,1');
    Route::post('/etablissements/propositions', [PropositionEtablissementController::class, 'store'])->middleware('throttle:5,1');
});