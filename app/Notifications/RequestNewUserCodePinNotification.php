<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestNewUserCodePinNotification extends Notification
{
    use Queueable;
    public $pinCode;
    public $user;
    /**
     * Create a new notification instance.
     */
    public function __construct($pinCode, $user)
    {
        $this->pinCode = $pinCode;
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
            ->subject('Nouveau code PIN pour votre compte')
            ->line('Bonjour ' . $this->user->name . ',')
            ->line('Vous avez demandé un nouveau code PIN pour votre compte.')
            ->line('Le code PIN pour votre compte est : ' . $this->pinCode)
            ->line('Merci de le modifier dès que possible.')
            ->line('Merci pour votre confiance !');
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
