<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\ClientController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| These routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group.
|
*/

// Routes publiques (sans authentification)
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

// Routes de vérification d'email
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->name('verification.verify');
Route::post('/email/verification-notification', [EmailVerificationController::class, 'sendVerificationEmail'])
    ->name('verification.send');

// Routes protégées (avec authentification)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/me', [UserController::class, 'me']);
});


/*
|--------------------------------------------------------------------------
| Routes API pour ClientController
|--------------------------------------------------------------------------
*/

// Routes nécessitant une authentification
Route::middleware('auth:sanctum')->group(function () {

    // Routes pour la gestion des clients
    Route::prefix('clients')->group(function () {
        Route::get('/', [ClientController::class, 'index'])->name('clients.index');
        Route::post('/', [ClientController::class, 'store'])->name('clients.store');
        Route::get('/{id}', [ClientController::class, 'show'])->name('clients.show');
        Route::put('/{id}', [ClientController::class, 'update'])->name('clients.update');
        Route::delete('/{id}', [ClientController::class, 'destroy'])->name('clients.destroy');

        // Routes supplémentaires pour filtrer par type
        Route::get('/types/carriers', [ClientController::class, 'getCarriers'])->name('clients.carriers');
        Route::get('/types/customers', [ClientController::class, 'getCustomers'])->name('clients.customers');
    });

    // Routes pour le profil client de l'utilisateur connecté
    Route::prefix('profile')->group(function () {
        Route::get('/', [ClientController::class, 'getUserProfile'])->name('profile.get');
        Route::post('/', [ClientController::class, 'updateUserProfile'])->name('profile.update');
        Route::get('/status', [ClientController::class, 'checkProfileStatus'])->name('profile.status');
    });
});