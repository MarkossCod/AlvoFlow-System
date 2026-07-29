<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Conta de utilizador do AlvoFlow. Login é feito por "username" (não por email — o
 * email é opcional, usado só para recuperação de senha/utilizador). Sem sistema de
 * roles: o único utilizador com acesso de administração é identificado pelo
 * username "markin" (ver middleware EnsureIsMarkin).
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
