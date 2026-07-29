<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Regista a última página visitada e o instante da última atividade de cada utilizador
 * autenticado. Usado apenas para a conta "markin" ver o que os outros utilizadores estão
 * a fazer no sistema (ecrã Utilizadores Registados) — não é um log de auditoria completo.
 *
 * Atualiza direto via query builder (não Eloquent) para não disparar eventos/observers
 * nem tocar o "updated_at" do model por causa de um simples "ping" de atividade.
 */
class TrackUserActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            DB::table('users')->where('id', $request->user()->id)->update([
                'last_seen_at' => now(),
                'last_url' => '/'.ltrim($request->path(), '/'),
            ]);
        }

        return $next($request);
    }
}
