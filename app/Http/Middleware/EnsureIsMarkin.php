<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restringe rotas de administração (lista/edição/exclusão de utilizadores) a uma única
 * conta, identificada pelo nome de utilizador "markin" (comparação sem diferenciar maiúsculas).
 *
 * Não há sistema de papéis/permissões no AlvoFlow — esta é uma verificação simples e
 * suficiente para o caso de uso atual (um único responsável pelo sistema).
 */
class EnsureIsMarkin
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(strtolower((string) $request->user()?->username) === 'markin', 403);

        return $next($request);
    }
}
