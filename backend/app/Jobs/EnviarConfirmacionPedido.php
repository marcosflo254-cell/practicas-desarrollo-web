<?php

namespace App\Jobs;

use App\Models\Pedido;
use App\Events\PedidoProcesado; // 🚀 Importamos el evento
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class EnviarConfirmacionPedido implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3; 
    public int $timeout = 60;
    protected $pedido;

    public function __construct(Pedido $pedido)
    {
        $this->pedido = $pedido;
    }

    public function handle(): void
    {
        $user = User::first() ?? new User(['name' => 'Marcos Flores Vera', 'email' => 'marcos@uptex.com']);
        
        $this->pedido->update([
            'email_enviado_at' => now()->toDateTimeString()
        ]);

        event(new PedidoProcesado($this->pedido));
    }
}