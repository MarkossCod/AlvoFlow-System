<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Administração de utilizadores: listar, editar e excluir contas, e ver a última
 * atividade de cada uma (last_seen_at/last_url, preenchidos pelo middleware
 * TrackUserActivity). Só acessível pela conta "markin" — ver middleware EnsureIsMarkin,
 * aplicado nas rotas em routes/web.php.
 */
class UtilizadorController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Utilizadores/Index', [
            'utilizadores' => User::orderBy('id')->get([
                'id', 'username', 'email', 'created_at', 'last_seen_at', 'last_url',
            ]),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:users,username,'.$user->id],
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ], [
            'username.alpha_dash' => 'Utilize apenas letras, números, traços e underscores.',
            'username.unique' => 'Este nome de utilizador já está em uso.',
            'email.email' => 'Introduza um email válido.',
            'email.unique' => 'Já existe uma conta com este email.',
            'password.confirmed' => 'As palavras-passe não coincidem.',
        ]);

        $user->username = $data['username'];
        $user->name = $data['username'];
        $user->email = $data['email'] ?: null;

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return back()->with('success', 'Utilizador atualizado com sucesso.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->id === Auth::id(), 403, 'Não é possível excluir a própria conta por aqui.');

        $user->delete();

        return back()->with('success', 'Utilizador excluído.');
    }
}
