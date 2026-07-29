<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Sentinela;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

/** Dashboard (rota "/painel"): estatísticas de pedidos, gráficos e monitoramento geral. */
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

        $totalPedidos = array_sum($porEstado->toArray());
        $concluidos = $porEstado['Concluído'] ?? 0;

        // Última atividade da sessão autenticada (tabela "sessions", driver database).
        $sessao = DB::table('sessions')->where('id', session()->getId())->first();

        return Inertia::render('Painel', [
            'porEstado' => $porEstado,
            'porDia' => $porDia,
            'pedidos' => Pedido::orderBy('data')->get(),
            'monitoramento' => [
                'publicadores' => User::count(),
                'sentinelaPendentes' => Sentinela::where('status', 'Pendente')->count(),
                'taxaConclusao' => $totalPedidos > 0 ? round(($concluidos / $totalPedidos) * 100) : 0,
                'ultimaAtualizacao' => Pedido::max('updated_at') ?? Sentinela::max('updated_at'),
                'contaCriadaEm' => Auth::user()->created_at,
                'sessaoDesde' => $sessao ? \Illuminate\Support\Carbon::createFromTimestamp($sessao->last_activity) : null,
                'nomeUtilizador' => Auth::user()->username,
            ],
        ]);
    }
}
