<?php

use Illuminate\Support\Facades\Route;

// Toutes les pages sont gérées par React Router
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '^(?!api).*$');