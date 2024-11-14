<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->subject('Bienvenido a ' . config('app.name'))
            ->greeting("¡Bienvenido!")
            ->line('Tu cuenta ha sido creada exitosamente.')
            ->line('Puedes solicitar un cambio de rol en cualquier momento desde tu perfil.')
            ->action('Ir al sitio', url('/'))
            ->line('¡Gracias por registrarte!');
    }
}