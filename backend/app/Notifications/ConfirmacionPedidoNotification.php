<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConfirmacionPedidoNotification extends Notification
{
    use Queueable;

    public $pedido;

    public function __construct($pedido)
    {
        $this->pedido = $pedido;
    }

    public function via(object $notifiable): array
    {
        return ['mail']; // Canal de entrega por correo
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Confirmación de tu pedido #' . $this->pedido->id)
            ->greeting('¡Hola, ' . $notifiable->name . '!')
            ->line('Recibimos tu pedido correctamente en la plataforma unificada.')
            ->line('Alumno Evaluado: Marcos Flores Vera')
            ->line('Total a pagar: $' . number_format($this->pedido->total, 2))
            ->action('Ver mi pedido', url('/'))
            ->line('Gracias por tu compra.');
    }
}