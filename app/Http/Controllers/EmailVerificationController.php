<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

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

        // Vous pouvez envoyer l'email ici via une notification
        // $user->notify(new VerifyEmailNotification($verificationUrl));

        // Pour le moment, retournons simplement l'URL (à remplacer par un vrai envoi d'email)
        return response()->json([
            'success' => true,
            'message' => 'Email de vérification envoyé.',
            'verification_url' => $verificationUrl // À supprimer en production
        ]);
    }

    /**
     * Vérifie l'email de l'utilisateur avec le lien reçu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request)
    {
        $user = User::find($request->id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé.'
            ], 404);
        }

        // Si l'utilisateur a déjà vérifié son email
        if ($user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Cet email est déjà vérifié.'
            ], 400);
        }

        // Si le hash ne correspond pas
        if (!hash_equals(sha1($user->email), $request->hash)) {
            return response()->json([
                'success' => false,
                'message' => 'Lien de vérification invalide.'
            ], 400);
        }

        // Si la signature est incorrecte
        if (!$request->hasValidSignature()) {
            return response()->json([
                'success' => false,
                'message' => 'Lien de vérification expiré ou invalide.'
            ], 400);
        }

        // Vérifier l'email
        $user->email_verified_at = now();
        $user->save();

        // Créer un token pour l'authentification automatique après vérification
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Email vérifié avec succès.',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
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
}
