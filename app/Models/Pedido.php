<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    const ESTADOS = ['Aberto', 'Em Andamento', 'Concluído'];

    protected $fillable = [
        'codigo',
        'publicador',
        'criado_por',
        'publicacao',
        'quantidade',
        'data',
        'estado',
        'observacoes',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'date:Y-m-d',
            'quantidade' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Pedido $pedido) {
            if (empty($pedido->codigo)) {
                $ultimo = static::max('id') ?? 0;
                $pedido->codigo = 'PED-'.str_pad((string) ($ultimo + 1), 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
