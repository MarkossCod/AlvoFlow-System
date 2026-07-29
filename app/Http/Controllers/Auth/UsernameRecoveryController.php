<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UsernameRecoveryNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Formulário "esqueci o nome de utilizador": envia o username por email caso exista
 * uma conta com esse endereço. A mensagem de resposta é sempre igual (existindo ou
 * não a conta) para não revelar quais emails estão registados.
 */
class UsernameRecoveryController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/ForgotUsername');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.email' => 'Introduza um email válido.',
        ]);

        $user = User::where('email', $request->email)->first();

        $user?->notify(new UsernameRecoveryNotification());

        return back()->with('status', 'Se existir uma conta com este email, o nome de utilizador foi enviado.');
    }
}
