<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\UsernameRecoveryController;
use Illuminate\Support\Facades\Route;

// throttle:6,1 -> trava tentativas de login/registo/recuperação a 6 por minuto por IP,
// evitando força bruta (antes não havia nenhum limite nestas rotas).
Route::middleware(['guest', 'throttle:6,1'])->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('registo', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('registo', [RegisteredUserController::class, 'store']);

    Route::get('esqueci-senha', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('esqueci-senha', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('redefinir-senha/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('redefinir-senha', [NewPasswordController::class, 'store'])->name('password.update');

    Route::get('recuperar-utilizador', [UsernameRecoveryController::class, 'create'])->name('username.request');
    Route::post('recuperar-utilizador', [UsernameRecoveryController::class, 'store'])->name('username.send');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
