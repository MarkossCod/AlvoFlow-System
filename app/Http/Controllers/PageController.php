<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

/** Páginas estáticas/informativas (Sobre o Sistema, Perfil) sem lógica própria. */
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
