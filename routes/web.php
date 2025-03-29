<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\UserController;

// Routes Web pour la vérification d'email (pages HTML)
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->name('verification.verify');

Route::get('/resend-verification', [EmailVerificationController::class, 'showResendForm'])
    ->name('verification.resend');

Route::post('/email/verification-notification', [EmailVerificationController::class, 'sendVerificationEmail'])
    ->name('verification.send');

Route::get('/password/reset/{id}/{hash}', [EmailVerificationController::class, 'resetPassword'])
    ->name('password.reset');

Route::view('/password/update/{id}', 'auth.update-password-page')
    ->name('password.update.view');

Route::post('/password/update', [UserController::class, 'updatePassword'])
    ->name('password.update');
