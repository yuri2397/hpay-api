<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\ClientController;

/*
|--------------------------------------------------------------------------
| Routes API et Web pour l'authentification
|--------------------------------------------------------------------------
*/

// Routes publiques (sans authentification)
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

// Route pour renvoyer l'email de vérification via API
Route::post('/email/verification-notification', [EmailVerificationController::class, 'sendVerificationEmail'])
    ->name('api.verification.send');

// Routes protégées par l'authentification
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/me', [UserController::class, 'me']);

    // Routes pour le profil client
    Route::prefix('profile')->group(function () {
        Route::get('/', [ClientController::class, 'getUserProfile']);
        Route::post('/', [ClientController::class, 'updateUserProfile']);
        Route::get('/status', [ClientController::class, 'checkProfileStatus']);
    });
});