<?php

// Rotas da aplicação (tudo atrás de login). Administração de utilizadores fica num
// sub-grupo à parte, protegido pelo middleware "markin" (ver EnsureIsMarkin).

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PainelController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\SentinelaController;
use App\Http\Controllers\UtilizadorController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
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

    // Administração de utilizadores — só a conta "markin" passa pelo middleware (403 para as demais).
    Route::middleware('markin')->group(function () {
        Route::get('/utilizadores', [UtilizadorController::class, 'index'])->name('utilizadores');
        Route::patch('/utilizadores/{user}', [UtilizadorController::class, 'update'])->name('utilizadores.update');
        Route::delete('/utilizadores/{user}', [UtilizadorController::class, 'destroy'])->name('utilizadores.destroy');
    });
});

require __DIR__.'/auth.php';
