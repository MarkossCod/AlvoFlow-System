<?php

namespace App\Http\Controllers;

use App\Models\Sentinela;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/** Gestão das edições da Sentinela (estudo/artigo) usadas pela congregação. */
class SentinelaController extends Controller
{
    public function index(Request $request): Response
    {
        $edicao = $request->string('edicao', 'todos')->toString();
        $status = $request->string('status', 'todos')->toString();

        $query = Sentinela::query();
        if ($edicao !== 'todos') {
            $query->where('edicao', $edicao);
        }
        if ($status !== 'todos') {
            $query->where('status', $status);
        }

        return Inertia::render('Sentinela/Index', [
            'lista' => $query->orderBy('edicao')->orderBy('publicador')->get(),
            'edicoes' => Sentinela::query()->distinct()->orderBy('edicao')->pluck('edicao'),
            'edicao' => $edicao,
            'status' => $status,
            'totais' => [
                'total' => Sentinela::sum('quantidade'),
                'entregues' => Sentinela::where('status', 'Entregue')->count(),
                'pendentes' => Sentinela::where('status', 'Pendente')->count(),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'edicao' => ['required', 'string', 'max:255'],
            'publicador' => ['required', 'string', 'max:255'],
            'tamanho' => ['required', 'in:Letra Grande,Letra Pequena'],
            'quantidade' => ['required', 'integer', 'min:1'],
        ]);

        Sentinela::create($data + ['status' => 'Pendente']);

        return back()->with('success', 'Pedido de Sentinela adicionado.');
    }

    public function update(Request $request, Sentinela $sentinela): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:Pendente,Entregue'],
        ]);

        $sentinela->update($data);

        return back()->with('success', 'Estado atualizado.');
    }

    public function destroy(Sentinela $sentinela): RedirectResponse
    {
        $sentinela->delete();

        return back()->with('success', 'Registo removido.');
    }
}
