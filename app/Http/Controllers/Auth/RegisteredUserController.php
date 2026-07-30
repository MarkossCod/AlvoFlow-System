<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

/** Registo de conta nova: só pede username + password (sem nome nem email). */
class RegisteredUserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:users,username'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'username.required' => 'Escolha um nome de utilizador.',
            'username.alpha_dash' => 'Utilize apenas letras, números, traços e underscores.',
            'username.unique' => 'Este nome de utilizador já está em uso.',
            'password.confirmed' => 'As palavras-passe não coincidem.',
        ]);

        $user = User::create([
            'name' => $request->username,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        // Não faz login automático: manda para o ecrã de Entrar para o utilizador iniciar
        // sessão com a conta que acabou de criar (fluxo registo -> login, não registo -> app).
        return redirect()->route('login')->with('success', 'Conta criada com sucesso! Inicie sessão para continuar.');
    }
}
