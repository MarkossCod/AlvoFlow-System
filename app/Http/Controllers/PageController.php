<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function perfil(): Response
    {
        return Inertia::render('Perfil');
    }

    public function sobre(): Response
    {
        return Inertia::render('Sobre');
    }
}
