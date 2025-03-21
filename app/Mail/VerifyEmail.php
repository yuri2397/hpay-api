<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * L'utilisateur auquel l'email est envoyé.
     *
     * @var \App\Models\User
     */
    public $user;

    /**
     * L'URL de vérification.
     *
     * @var string
     */
    public $verificationUrl;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\User  $user
     * @param  string  $verificationUrl
     * @return void
     */
    public function __construct(User $user, $verificationUrl)
    {
        $this->user = $user;
        $this->verificationUrl = $verificationUrl;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Veuillez vérifier votre adresse email')
            ->view('emails.verify-email')
            ->with([
                'name' => $this->user->name,
                'verificationUrl' => $this->verificationUrl
            ]);
    }
}
