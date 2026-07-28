<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Inertia\Inertia;
use Inertia\Response;

class PainelController extends Controller
{
    public function __invoke(): Response
    {
        $porEstado = Pedido::selectRaw('estado, count(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $porDia = Pedido::selectRaw('data, count(*) as total')
            ->where('data', '>=', now()->subDays(13)->toDateString())
            ->groupBy('data')
            ->orderBy('data')
            ->pluck('total', 'data');

        return Inertia::render('Painel', [
            'porEstado' => $porEstado,
            'porDia' => $porDia,
            'pedidos' => Pedido::orderBy('data')->get(),
        ]);
    }
}
