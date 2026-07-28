<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class UtilizadorController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Utilizadores', [
            'utilizadores' => User::orderBy('id')->get(['id', 'username', 'email', 'created_at']),
        ]);
    }
}
