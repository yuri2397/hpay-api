<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Le sujet de la notification
     *
     * @var string
     */
    protected $subject;

    /**
     * Le message de la notification
     *
     * @var string
     */
    protected $message;

    /**
     * Données supplémentaires
     *
     * @var array
     */
    protected $data;

    /**
     * Priorité de la notification
     *
     * @var string
     */
    protected $priority;

    /**
     * Create a new notification instance.
     *
     * @param string $subject
     * @param string $message
     * @param array $data
     * @param string $priority normal|high|low
     * @return void
     */
    public function __construct($subject, $message, array $data = [], $priority = 'normal')
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->data = $data;
        $this->priority = $priority;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // On vérifie si le modèle notifiable a une méthode pour déterminer les canaux
        if (method_exists($notifiable, 'notificationChannels')) {
            return $notifiable->notificationChannels('system');
        }

        // Par défaut, on envoie sur le canal "database" et "mail"
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject($this->subject)
            ->line($this->message);

        // Ajouter un bouton d'action si URL spécifiée dans les données
        if (isset($this->data['action_url']) && isset($this->data['action_text'])) {
            $mail->action($this->data['action_text'], $this->data['action_url']);
        }

        // Ajouter des lignes supplémentaires si spécifiées
        if (isset($this->data['additional_lines']) && is_array($this->data['additional_lines'])) {
            foreach ($this->data['additional_lines'] as $line) {
                $mail->line($line);
            }
        }

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'subject' => $this->subject,
            'message' => $this->message,
            'data' => $this->data,
            'priority' => $this->priority,
            'read' => false,
            'timestamp' => now()->toIso8601String()
        ];
    }

    /**
     * Get the database representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return $this->toArray($notifiable);
    }
}