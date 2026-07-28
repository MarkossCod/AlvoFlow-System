<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PedidoController extends Controller
{
    public function criar(): Response
    {
        return Inertia::render('Pedidos/Criar', [
            'estados' => Pedido::ESTADOS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'publicador' => ['required', 'string', 'max:255'],
            'publicacao' => ['required', 'string', 'max:255'],
            'quantidade' => ['required', 'integer', 'min:1'],
            'data' => ['required', 'date'],
            'observacoes' => ['nullable', 'string'],
        ]);

        Pedido::create($data + ['estado' => 'Aberto']);

        return redirect()->route('home')->with('success', 'Pedido criado com sucesso.');
    }

    public function pesquisar(Request $request): Response
    {
        return Inertia::render('Pedidos/Pesquisar', [
            'resultados' => $this->buscar($request->string('q')->toString(), $request->string('tipo', 'todos')->toString()),
            'q' => $request->string('q')->toString(),
            'tipo' => $request->string('tipo', 'todos')->toString(),
        ]);
    }

    private function buscar(string $q, string $tipo)
    {
        if ($q === '') {
            return [];
        }

        $query = Pedido::query();

        return (match ($tipo) {
            'publicador' => $query->where('publicador', 'like', "%{$q}%"),
            'publicacao' => $query->where('publicacao', 'like', "%{$q}%"),
            'data' => $query->where('data', 'like', "%{$q}%"),
            'estado' => $query->where('estado', 'like', "%{$q}%"),
            'id', 'codigo' => $query->where('codigo', 'like', "%{$q}%"),
            default => $query->where(function ($qb) use ($q) {
                $qb->where('publicador', 'like', "%{$q}%")
                    ->orWhere('publicacao', 'like', "%{$q}%")
                    ->orWhere('data', 'like', "%{$q}%")
                    ->orWhere('estado', 'like', "%{$q}%")
                    ->orWhere('codigo', 'like', "%{$q}%");
            }),
        })->latest('data')->get();
    }

    public function visualizar(Request $request): Response
    {
        $estado = $request->string('estado', 'todos')->toString();

        $query = Pedido::query();
        if ($estado !== 'todos') {
            $query->where('estado', $estado);
        }

        return Inertia::render('Pedidos/Visualizar', [
            'pedidos' => $query->orderBy('data')->get(),
            'estado' => $estado,
        ]);
    }

    public function update(Request $request, Pedido $pedido): RedirectResponse
    {
        $data = $request->validate([
            'quantidade' => ['sometimes', 'integer', 'min:1'],
            'observacoes' => ['sometimes', 'nullable', 'string'],
            'estado' => ['sometimes', 'in:'.implode(',', Pedido::ESTADOS)],
        ]);

        $pedido->update($data);

        return back()->with('success', 'Pedido atualizado com sucesso.');
    }

    public function destroy(Pedido $pedido): RedirectResponse
    {
        $pedido->delete();

        return back()->with('success', 'Pedido removido.');
    }
}
