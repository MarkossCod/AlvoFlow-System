<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

/** Dados partilhados com todas as páginas Inertia (props globais), como o utilizador autenticado. */
class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
                // Usado só para mostrar/esconder o link "Utilizadores Registados" no menu —
                // o acesso de verdade é garantido pelo middleware "markin" nas rotas.
                'isMarkin' => strtolower((string) $request->user()?->username) === 'markin',
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
