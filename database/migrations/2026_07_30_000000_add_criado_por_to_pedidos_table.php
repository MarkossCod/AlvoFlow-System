<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            // Nome de utilizador de quem criou o pedido — string simples (como "publicador"),
            // não uma foreign key: mais simples, e sobrevive mesmo que a conta seja apagada.
            // Nullable porque os pedidos já existentes não têm essa informação.
            $table->string('criado_por')->nullable()->after('publicador');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn('criado_por');
        });
    }
};
