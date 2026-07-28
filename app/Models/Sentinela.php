<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sentinela extends Model
{
    use HasFactory;

    const TAMANHOS = ['Letra Grande', 'Letra Pequena'];
    const STATUS = ['Pendente', 'Entregue'];

    protected $table = 'sentinelas';

    protected $fillable = [
        'codigo',
        'edicao',
        'publicador',
        'tamanho',
        'quantidade',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'quantidade' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Sentinela $sentinela) {
            if (empty($sentinela->codigo)) {
                $ultimo = static::max('id') ?? 0;
                $sentinela->codigo = 'SENT-'.str_pad((string) ($ultimo + 1), 3, '0', STR_PAD_LEFT);
            }
        });
    }
}
