<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UsernameRecoveryNotification extends Notification
{
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Recuperação do nome de utilizador — AlvoFlow')
            ->greeting('Olá, '.$notifiable->name.'!')
            ->line('Recebemos um pedido para recuperar o seu nome de utilizador.')
            ->line('O seu nome de utilizador é: '.$notifiable->username)
            ->line('Se não foi você quem fez este pedido, pode ignorar este email.');
    }
}
