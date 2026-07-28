<?php

namespace Database\Seeders;

use App\Models\Pedido;
use App\Models\Sentinela;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'publicador@congregacao.pt'],
            ['name' => 'Publicador', 'username' => 'publicador', 'password' => bcrypt('password')]
        );

        $nomes = ['Ana Ferreira', 'João Silva', 'Maria Costa', 'Pedro Santos', 'Rita Almeida', 'Carlos Pinto'];
        $publicacoes = ['A Sentinela', 'Despertai!', 'Bíblia — Tradução do Novo Mundo', 'Ame as Pessoas', 'Ensina', 'Cartão de Visita'];
        $estados = Pedido::ESTADOS;

        if (Pedido::count() === 0) {
            foreach (range(0, 11) as $i) {
                Pedido::create([
                    'publicador' => $nomes[$i % count($nomes)],
                    'publicacao' => $publicacoes[$i % count($publicacoes)],
                    'quantidade' => ($i % 5) + 1,
                    'data' => now()->subDays(12 - $i)->toDateString(),
                    'estado' => $estados[$i % count($estados)],
                ]);
            }
        }

        $edicoes = ['Sentinela — Estudo, Janeiro 2026', 'Sentinela — Pública, Fevereiro 2026', 'Sentinela — Estudo, Março 2026'];
        $tamanhos = Sentinela::TAMANHOS;

        if (Sentinela::count() === 0) {
            foreach ($nomes as $i => $nome) {
                Sentinela::create([
                    'edicao' => $edicoes[$i % count($edicoes)],
                    'publicador' => $nome,
                    'tamanho' => $tamanhos[$i % 2],
                    'quantidade' => ($i % 4) + 1,
                    'status' => $i % 3 === 0 ? 'Entregue' : 'Pendente',
                ]);
            }
        }
    }
}
