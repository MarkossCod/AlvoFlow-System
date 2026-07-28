<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        $hoje = now()->toDateString();

        return Inertia::render('Home', [
            'stats' => [
                'total' => Pedido::count(),
                'abertos' => Pedido::where('estado', 'Aberto')->count(),
                'concluidos' => Pedido::where('estado', 'Concluído')->count(),
                'hoje' => Pedido::whereDate('data', $hoje)->count(),
            ],
            'recentes' => Pedido::latest('data')->latest('id')->take(6)->get(),
        ]);
    }
}
