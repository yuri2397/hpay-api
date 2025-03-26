<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * La facture concernée
     *
     * @var \App\Models\Invoice
     */
    protected $invoice;

    /**
     * Le type d'événement de facture
     *
     * @var string
     */
    protected $event;

    /**
     * Create a new notification instance.
     *
     * @param Invoice $invoice
     * @param string $event
     * @return void
     */
    public function __construct(Invoice $invoice, string $event = 'created')
    {
        $this->invoice = $invoice;
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
            return $notifiable->notificationChannels('invoice');
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
            case 'created':
                $mail->subject('Nouvelle facture disponible')
                    ->line('Une nouvelle facture n°' . $this->invoice->invoice_number . ' d\'un montant de ' . number_format($this->invoice->amount, 2) . ' ' . $this->invoice->currency . ' est disponible.')
                    ->line('Référence: ' . $this->invoice->reference);
                break;

            case 'paid':
                $mail->subject('Facture payée')
                    ->line('Votre facture n°' . $this->invoice->invoice_number . ' d\'un montant de ' . number_format($this->invoice->amount, 2) . ' ' . $this->invoice->currency . ' a été payée.');
                break;

            case 'overdue':
                $mail->subject('Facture en retard de paiement')
                    ->line('Votre facture n°' . $this->invoice->invoice_number . ' d\'un montant de ' . number_format($this->invoice->amount, 2) . ' ' . $this->invoice->currency . ' est en retard de paiement.')
                    ->line('Veuillez procéder au règlement dès que possible.');
                break;

            case 'cancelled':
                $mail->subject('Facture annulée')
                    ->line('Votre facture n°' . $this->invoice->invoice_number . ' d\'un montant de ' . number_format($this->invoice->amount, 2) . ' ' . $this->invoice->currency . ' a été annulée.');
                break;

            default:
                $mail->subject('Mise à jour de votre facture')
                    ->line('Il y a eu une mise à jour concernant votre facture n°' . $this->invoice->invoice_number . '.')
                    ->line('Statut actuel: ' . ucfirst($this->invoice->status));
        }

        // Ajouter un lien vers les détails de la facture
        $mail->action('Voir la facture', url('/invoices/' . $this->invoice->id));

        // Ajouter un lien pour payer si la facture n'est pas payée
        if ($this->invoice->status !== 'paid' && $this->invoice->status !== 'cancelled') {
            $mail->line('');
            $mail->line('Vous pouvez procéder au paiement en cliquant sur le lien ci-dessous:');
            $mail->action('Payer maintenant', url('/invoices/' . $this->invoice->id . '/pay'));
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
        $messages = [
            'created' => 'Nouvelle facture n°' . $this->invoice->invoice_number . ' d\'un montant de ' . number_format($this->invoice->amount, 2) . ' ' . $this->invoice->currency . ' disponible.',
            'paid' => 'Votre facture n°' . $this->invoice->invoice_number . ' a été payée.',
            'overdue' => 'Votre facture n°' . $this->invoice->invoice_number . ' est en retard de paiement.',
            'cancelled' => 'Votre facture n°' . $this->invoice->invoice_number . ' a été annulée.',
            'default' => 'Mise à jour de votre facture n°' . $this->invoice->invoice_number . '.'
        ];

        $message = $messages[$this->event] ?? $messages['default'];

        return [
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'amount' => $this->invoice->amount,
            'currency' => $this->invoice->currency,
            'status' => $this->invoice->status,
            'reference' => $this->invoice->reference,
            'event' => $this->event,
            'message' => $message,
            'read' => false,
            'timestamp' => now()->toIso8601String()
        ];
    }
}