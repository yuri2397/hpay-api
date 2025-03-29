<?php

namespace App\Notifications;

use App\Models\Notification as NotificationModel;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewLoginNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Les informations de connexion
     *
     * @var array
     */
    protected $loginInfo;
    private $notificationService;

    /**
     * Create a new notification instance.
     *
     * @param array $loginInfo
     * @return void
     */
    public function __construct(array $loginInfo)
    {
        $this->loginInfo = $loginInfo;
        $this->notificationService = new NotificationService();
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
            return $notifiable->notificationChannels('login');
        }

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
            ->subject('Nouvelle connexion détectée')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Nous avons détecté une nouvelle connexion à votre compte.')
            ->line('Détails de la connexion:')
            ->line('Date et heure: ' . $this->loginInfo['time'])
            ->line('Adresse IP: ' . $this->loginInfo['ip'])
            ->line('Appareil: ' . $this->loginInfo['device_name'])
            ->line('Navigateur: ' . $this->loginInfo['browser']);

        if (isset($this->loginInfo['location'])) {
            $mail->line('Localisation approximative: ' . $this->loginInfo['location']);
        }

        if (isset($this->loginInfo['device'])) {
            $mail->line('Appareil: ' . $this->loginInfo['device']);
        }

        if (isset($this->loginInfo['browser'])) {
            $mail->line('Navigateur: ' . $this->loginInfo['browser']);
        }

        $mail->line('')
            ->line('Si c\'était vous, vous pouvez ignorer cet email.')
            ->line('Si vous ne reconnaissez pas cette activité, veuillez sécuriser votre compte immédiatement en changeant votre mot de passe.')
            ->action('Changer mon mot de passe', url('/password/reset'));

        return $mail;
    }

    public function toDatabase($notifiable)
    {
        $this->notificationService->sendNotification($notifiable, 'new_login', 'Nouvelle connexion à votre compte détectée', $this->toArray($notifiable));
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
            'type' => NotificationModel::NEW_LOGIN_NOTIFICATION,
            'message' => 'Nouvelle connexion à votre compte détectée',
            'login_time' => $this->loginInfo['time'],
            'ip_address' => $this->loginInfo['ip'],
            'location' => $this->loginInfo['location'] ?? null,
            'device' => $this->loginInfo['device'] ?? null,
            'browser' => $this->loginInfo['browser'] ?? null,
            'read' => false,
            'timestamp' => now()->toIso8601String()
        ];
    }
}
