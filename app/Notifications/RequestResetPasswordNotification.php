<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestResetPasswordNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $url;

    /**
     * Create a new notification instance.
     */
    public function __construct($user, $url)
    {
        $this->user = $user;
        $this->url = $url;
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
            ->subject('Réinitialisation de mot de passe')
            ->line('Bonjour ' . $this->user->name . ',')
            ->line('Vous avez demandé une réinitialisation de mot de passe.')
            ->line('Pour réinitialiser votre mot de passe, veuillez cliquer sur le lien suivant:')
            ->action('Réinitialiser le mot de passe', $this->url)
            ->line('Ce lien expirera dans 60 minutes.')
            ->line('Si vous n\'avez pas demandé de réinitialisation de mot de passe, veuillez ignorer cet email.')
            ->line('Merci pour votre confiance.');
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
