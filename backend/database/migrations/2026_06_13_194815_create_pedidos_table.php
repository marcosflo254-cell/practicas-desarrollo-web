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
            $table->string('cliente'); // Registra a Marcos Flores Vera
            $table->decimal('total', 10, 2);
            $table->string('estado')->default('pendiente');
            $table->string('email_enviado_at')->nullable(); // Columna para Polling de Vue
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};