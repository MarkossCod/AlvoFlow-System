<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sentinelas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('edicao');
            $table->string('publicador');
            $table->enum('tamanho', ['Letra Grande', 'Letra Pequena'])->default('Letra Grande');
            $table->unsignedInteger('quantidade')->default(1);
            $table->enum('status', ['Pendente', 'Entregue'])->default('Pendente');
            $table->timestamps();

            $table->index(['edicao', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sentinelas');
    }
};
