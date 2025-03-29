<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CmaCgmInvoiceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ShippingCompanyController;
use App\Http\Controllers\InvoiceController;
/*
|--------------------------------------------------------------------------
| Routes API et Web pour l'authentification
|--------------------------------------------------------------------------
*/

// Routes publiques (sans authentification)
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/forgot-password', [UserController::class, 'forgotPassword']);

// Route pour renvoyer l'email de vérification via API
Route::post('/email/verification-notification', [EmailVerificationController::class, 'sendVerificationEmail'])
    ->name('api.verification.send');

Route::post('/request-reset-password', [UserController::class, 'requestResetPasswordLink']);
Route::post('/reset-password', [UserController::class, 'resetPassword']);

// Routes protégées par l'authentification
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/me', [UserController::class, 'me']);
    Route::post('/change-password', [UserController::class, 'changePasswordForCurrentUser']);
    Route::post('/update-user-information', [UserController::class, 'updateUserInformation']);
    Route::get('/last-login-session', [UserController::class, 'lastLogginSession']);

    // Routes pour les notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount']);
        Route::post('/mark-as-read/{id}', [NotificationController::class, 'markAsRead']);
        Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
        Route::post('/send', [NotificationController::class, 'sendNotification'])->middleware('can:send-notifications');
    });

    // Routes pour les compagnies maritimes
    Route::prefix('shipping-companies')->group(function () {
        Route::get('/', [ShippingCompanyController::class, 'index']);
    });
});

// Routes pour l'API CMA CGM
Route::middleware('auth:sanctum')->prefix('cmacgm')->group(function () {
    // Récupérer une facture par son numéro
    Route::get('invoices/{invoiceNo}', [CmaCgmInvoiceController::class, 'getInvoice']);
    Route::get('invoices/{transportDocumentReference}/shipment', [CmaCgmInvoiceController::class, 'getShipmentInvoices']);
    // Télécharger le PDF d'une facture
    Route::get('invoices/{id}/pdf', [CmaCgmInvoiceController::class, 'downloadInvoicePdf']);
});

// Routes pour le dashboard
Route::middleware('auth:sanctum')->prefix('dashboard')->group(function () {
    Route::get('/invoices-statistics', [DashboardController::class, 'invoicesStatistics']);
    Route::get('/last-ten-invoices', [DashboardController::class, 'lastTenInvoices']);
});

// Routes pour les factures
Route::middleware('auth:sanctum')->prefix('invoices')->group(function () {
    Route::get('/', [InvoiceController::class, 'index']);
    Route::get('/{invoice}', [InvoiceController::class, 'show']);

    Route::get('/search/{invoiceNumber}', [InvoiceController::class, 'searchInvoiceByNumber']);
    Route::get('/{invoice}/download', [InvoiceController::class, 'downloadInvoice']);
});
