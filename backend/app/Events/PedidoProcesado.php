<?php

namespace App\Events;

use App\Models\Pedido;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PedidoProcesado implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $pedido;

    public function __construct(Pedido $pedido)
    {
        $this->pedido = $pedido;
    }

    public function broadcastOn(): array
    {
        return [new Channel('pedidos-canal')];
    }

    public function broadcastAs(): string
    {
        return 'pedido.procesado';
    }
}