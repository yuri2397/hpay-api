<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserPasswordUpdatedNotification extends Notification
{
    use Queueable;

    public $user;

    /**
     * Create a new notification instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Mot de passe mis à jour')
            ->line('Bonjour ' . $this->user->name)
            ->line('Le mot de passe de votre compte a été mis à jour avec succès.')
            ->line('Vous pouvez maintenant vous connecter avec votre nouveau mot de passe.')
            ->line('Merci de vous connecter avec votre nouveau mot de passe.')
            ->line('Si vous avez des questions, veuillez contacter le support.')
            ->line('Merci de nous contacter si vous avez des questions ou des suggestions.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
