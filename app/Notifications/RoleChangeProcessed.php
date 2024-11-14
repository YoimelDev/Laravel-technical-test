<?php

namespace App\Notifications;

use App\Models\RoleChangeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RoleChangeProcessed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly RoleChangeRequest $roleChangeRequest)
    {}

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(): MailMessage
    {
        $status = $this->roleChangeRequest->status->value;
        $role = $this->roleChangeRequest->requested_role;

        return (new MailMessage)
            ->subject("Solicitud de cambio de rol {$status}")
            ->greeting("Hola {$this->roleChangeRequest->user->name},")
            ->line("Tu solicitud para el rol de {$role} ha sido {$status}.")
            ->when($this->roleChangeRequest->admin_notes, function (MailMessage $message) {
                return $message->line("Notas del administrador: {$this->roleChangeRequest->admin_notes}");
            })
            ->line('Gracias por usar nuestra aplicación!');
    }
}
