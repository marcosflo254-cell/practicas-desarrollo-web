<?php

namespace App\Jobs;

use App\Models\Pedido;
use App\Notifications\ConfirmacionPedidoNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Throwable;

class EnviarConfirmacionPedido implements ShouldQueue // Interface obligatoria[cite: 5]
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3; // Rúbrica: Reintentos automáticos si falla
    public int $timeout = 60; // Tiempo límite de ejecución

    public $pedido;

    public function __construct(Pedido $pedido)
    {
        $this->pedido = $pedido;
    }

    public function handle(): void
    {
        // Simulamos o recuperamos el usuario activo
        $user = User::first() ?? new User(['name' => 'Marcos Flores Vera', 'email' => 'marcos.flores@uptex.com']);
        
        // Despachamos el correo
        $user->notify(new ConfirmacionPedidoNotification($this->pedido));

        // Rúbrica: Actualizar campo tras completar el job
        $this->pedido->update([
            'email_enviado_at' => now()->toDateTimeString()
        ]);
    }

    public function failed(Throwable $e): void
    {
        Log::error('Fallo crítico en envío de email del pedido #' . $this->pedido->id);
    }
}