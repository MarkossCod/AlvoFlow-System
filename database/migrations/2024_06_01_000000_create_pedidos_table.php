<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('publicador');
            $table->string('publicacao');
            $table->unsignedInteger('quantidade')->default(1);
            $table->date('data');
            $table->enum('estado', ['Aberto', 'Em Andamento', 'Concluído'])->default('Aberto');
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->index(['estado', 'data']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
