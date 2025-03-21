<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailVerificationController;
// Routes Web pour la vérification d'email (pages HTML)
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->name('verification.verify');

Route::get('/resend-verification', [EmailVerificationController::class, 'showResendForm'])
    ->name('verification.resend');

Route::post('/email/verification-notification', [EmailVerificationController::class, 'sendVerificationEmail'])
    ->name('verification.send');