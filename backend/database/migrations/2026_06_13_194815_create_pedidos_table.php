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
            // Guardamos el nombre del cliente (Marcos Flores Vera para tu evaluación)
            $table->string('cliente'); 
            // Total económico de la compra
            $table->decimal('total', 10, 2); 
            // Campo crucial para la Práctica 11: Guardará el momento exacto en que el Job mande el correo
            $table->timestamp('email_enviado_at')->nullable(); 
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};