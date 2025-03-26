<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Le paiement concerné
     *
     * @var \App\Models\Payment
     */
    protected $payment;

    /**
     * Le type d'événement de paiement
     *
     * @var string
     */
    protected $event;

    /**
     * Create a new notification instance.
     *
     * @param Payment $payment
     * @param string $event
     * @return void
     */
    public function __construct(Payment $payment, string $event = 'processed')
    {
        $this->payment = $payment;
        $this->event = $event;
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
            return $notifiable->notificationChannels('payment');
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
        $mail = new MailMessage;

        switch ($this->event) {
            case 'processed':
                $mail->subject('Paiement traité avec succès')
                    ->line('Votre paiement de ' . number_format($this->payment->amount, 2) . ' ' . $this->payment->currency . ' a été traité avec succès.')
                    ->line('Numéro de transaction: ' . $this->payment->transaction_id)
                    ->line('Date du paiement: ' . $this->payment->payment_date->format('d/m/Y H:i'));
                break;

            case 'failed':
                $mail->subject('Échec du traitement de paiement')
                    ->line('Nous n\'avons pas pu traiter votre paiement de ' . number_format($this->payment->amount, 2) . ' ' . $this->payment->currency . '.')
                    ->line('Veuillez vérifier vos informations de paiement et réessayer.');
                break;

            case 'refunded':
                $mail->subject('Remboursement effectué')
                    ->line('Votre paiement de ' . number_format($this->payment->amount, 2) . ' ' . $this->payment->currency . ' a été remboursé.')
                    ->line('Le remboursement sera visible sur votre compte dans les prochains jours.');
                break;

            default:
                $mail->subject('Mise à jour de votre paiement')
                    ->line('Il y a eu une mise à jour concernant votre paiement de ' . number_format($this->payment->amount, 2) . ' ' . $this->payment->currency . '.')
                    ->line('Statut actuel: ' . ucfirst($this->payment->status));
        }

        // Ajouter un lien vers les détails du paiement
        $mail->action('Voir les détails', url('/payments/' . $this->payment->id));

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
        $messages = [
            'processed' => 'Votre paiement de ' . number_format($this->payment->amount, 2) . ' ' . $this->payment->currency . ' a été traité avec succès.',
            'failed' => 'Échec du traitement de votre paiement de ' . number_format($this->payment->amount, 2) . ' ' . $this->payment->currency . '.',
            'refunded' => 'Votre paiement de ' . number_format($this->payment->amount, 2) . ' ' . $this->payment->currency . ' a été remboursé.',
            'default' => 'Mise à jour de votre paiement de ' . number_format($this->payment->amount, 2) . ' ' . $this->payment->currency . '.'
        ];

        $message = $messages[$this->event] ?? $messages['default'];

        return [
            'payment_id' => $this->payment->id,
            'amount' => $this->payment->amount,
            'currency' => $this->payment->currency,
            'status' => $this->payment->status,
            'transaction_id' => $this->payment->transaction_id,
            'event' => $this->event,
            'message' => $message,
            'read' => false,
            'timestamp' => now()->toIso8601String()
        ];
    }
}