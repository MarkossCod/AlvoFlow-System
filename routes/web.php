<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PainelController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\SentinelaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', HomeController::class)->name('home');

    Route::get('/pedidos/criar', [PedidoController::class, 'criar'])->name('pedidos.criar');
    Route::post('/pedidos', [PedidoController::class, 'store'])->name('pedidos.store');
    Route::get('/pedidos/pesquisar', [PedidoController::class, 'pesquisar'])->name('pedidos.pesquisar');
    Route::get('/pedidos/visualizar', [PedidoController::class, 'visualizar'])->name('pedidos.visualizar');
    Route::patch('/pedidos/{pedido}', [PedidoController::class, 'update'])->name('pedidos.update');
    Route::delete('/pedidos/{pedido}', [PedidoController::class, 'destroy'])->name('pedidos.destroy');

    Route::get('/sentinela', [SentinelaController::class, 'index'])->name('sentinela.index');
    Route::post('/sentinela', [SentinelaController::class, 'store'])->name('sentinela.store');
    Route::patch('/sentinela/{sentinela}', [SentinelaController::class, 'update'])->name('sentinela.update');
    Route::delete('/sentinela/{sentinela}', [SentinelaController::class, 'destroy'])->name('sentinela.destroy');

    Route::get('/painel', PainelController::class)->name('painel');

    Route::get('/perfil', [PageController::class, 'perfil'])->name('perfil');
    Route::get('/sobre', [PageController::class, 'sobre'])->name('sobre');
});

require __DIR__.'/auth.php';
