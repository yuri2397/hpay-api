<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmail;

class EmailVerificationController extends Controller
{
    /**
     * Envoie un email de vérification à l'utilisateur.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendVerificationEmail(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé.'
            ], 404);
        }

        if ($user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Cet email est déjà vérifié.'
            ], 400);
        }

        $verificationUrl = $this->generateVerificationUrl($user);

        try {
            // Envoi de l'email avec la classe Mail
            Mail::to($user->email)->send(new VerifyEmail($user, $verificationUrl));

            return response()->json([
                'success' => true,
                'message' => 'Email de vérification envoyé avec succès.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'email de vérification.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vérifie l'email de l'utilisateur avec le lien reçu.
     * Affiche une page HTML avec le résultat.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request)
    {
        // Vérifier si la signature est correcte
        if (!$request->hasValidSignature()) {
            return view('auth.email-verification-page', [
                'success' => false,
                'expired' => true
            ]);
        }

        $user = User::find($request->id);

        if (!$user) {
            return view('auth.email-verification-page', [
                'success' => false,
                'error_message' => 'Utilisateur non trouvé.'
            ]);
        }

        // Si l'utilisateur a déjà vérifié son email
        if ($user->email_verified_at) {
            return view('auth.email-verification-page', [
                'success' => false,
                'already_verified' => true
            ]);
        }

        // Si le hash ne correspond pas
        if (!hash_equals(sha1($user->email), $request->hash)) {
            return view('auth.email-verification-page', [
                'success' => false,
                'error_message' => 'Lien de vérification invalide.'
            ]);
        }

        // Vérifier l'email
        $user->email_verified_at = now();
        $user->save();

        // Afficher la page de succès
        return view('auth.email-verification-page', [
            'success' => true
        ]);
    }

    /**
     * Génère une URL signée pour la vérification d'email.
     *
     * @param  \App\Models\User  $user
     * @return string
     */
    protected function generateVerificationUrl($user)
    {
        $url = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $user->id,
                'hash' => sha1($user->email)
            ]
        );

        return $url;
    }

    /**
     * Affiche un formulaire pour demander un nouveau lien de vérification.
     *
     * @return \Illuminate\Http\Response
     */
    public function showResendForm()
    {
        return view('auth.resend-verification');
    }
}
