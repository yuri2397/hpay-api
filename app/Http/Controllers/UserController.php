<?php

namespace App\Http\Controllers;

use App\Mail\VerifyEmail;
use App\Models\User;
use App\Notifications\AfterResetPasswordNotification;
use App\Notifications\NewLoginNotification;
use App\Notifications\RequestResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Jenssegers\Agent\Agent;
use App\Models\Notification as NotificationModel;
use App\Notifications\RequestNewUserCodePinNotification;
use App\Notifications\UserPasswordUpdatedNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Création de l'utilisateur
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => null,
            ]);

            // Générer l'URL de vérification
            $verificationUrl = $this->generateVerificationUrl($user);

            // Envoyer l'email de vérification
            Mail::to($user->email)->send(new VerifyEmail($user, $verificationUrl));

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur enregistré avec succès. Veuillez vérifier votre email pour activer votre compte.',
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'inscription',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login user and create token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'device_id' => 'required|string',
            'device_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Les informations d\'identification ne correspondent pas à nos enregistrements.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Vérifier les informations d'identification
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Ces informations d\'identification ne correspondent pas à nos enregistrements.'
            ], 422);
        }

        // Vérifier si l'email est vérifié
        if (!$user->email_verified_at) {
            // Générer l'URL de vérification
            $verificationUrl = $this->generateVerificationUrl($user);

            return response()->json([
                'success' => false,
                'message' => 'Veuillez vérifier votre adresse email avant de vous connecter.',
                'requires_verification' => true,
                'verification_url' => $verificationUrl // En développement seulement, à supprimer en production
            ], 422);
        }

        // Créer un nouveau token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Récupérer les informations sur la connexion
        $loginInfo = $this->getLoginInfo($request);

        // Envoyer une notification de nouvelle connexion
        try {
            Notification::sendNow($user, new NewLoginNotification($loginInfo));
        } catch (\Exception $e) {
            // On continue même si la notification échoue
            // mais on pourrait logger l'erreur
        }

        // Retourner la réponse
        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * Check user pin code
     */
    public function checkPinCode(Request $request)
    {
        $user = $request->user();

        if (!Hash::check($request->pin_code, $user->pin_code)) {
            return response()->json([
                'success' => false,
                'message' => 'Le code PIN est incorrect.'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Le code PIN est correct.'
        ]);
    }

    /**
     * Set user pin code
     */
    public function setPinCode(Request $request)
    {
        $request->validate([
            'current_pin_code' => 'required|string|min:' . User::PIN_CODE_LENGTH . '|max:' . User::PIN_CODE_LENGTH,
            'new_pin_code' => 'required|string|min:' . User::PIN_CODE_LENGTH . '|max:' . User::PIN_CODE_LENGTH,
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_pin_code, $user->pin_code)) {
            return response()->json([
                'success' => false,
                'message' => 'Le code PIN actuel est incorrect.'
            ], 401);
        }
        $user->pin_code = Hash::make($request->new_pin_code);
        $user->save();


        return response()->json([
            'success' => true,
            'message' => 'Le code PIN a été mis à jour avec succès. Un email a été envoyé à votre adresse email pour confirmer le nouveau code PIN.'
        ]);
    }

    /**
     * Request new user pin code
     */
    public function requestNewUserPinCode(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun utilisateur trouvé avec cette adresse email.'
            ], 404);
        }

        $pinCode = random_int(10000, 99999);

        $user->pin_code = Hash::make($pinCode);
        $user->save();

        Notification::sendNow($user, new RequestNewUserCodePinNotification($pinCode, $user));

        return response()->json([
            'success' => true,
            'message' => 'Un email a été envoyé à votre adresse email pour confirmer le nouveau code PIN.'
        ]);
    }

    /**
     * Logout user (revoke token).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        // Révoquer tous les tokens de l'utilisateur
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie'
        ]);
    }

    /**
     * Get authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    // update user information
    public function updateUserInformation(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $request->user()->id,
        ]);

        $user = $request->user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return response()->json($user->refresh());
    }

    public function lastLogginSession(Request $request)
    {
        $user = $request->user();
        // last notification with type NEW_LOGIN_NOTIFICATION
        $lastLogin = $user->notifications()->where('type', NotificationModel::NEW_LOGIN_NOTIFICATION)->latest()->first();
        if ($lastLogin) {
            return response()->json([
                'browser' => $lastLogin->data['browser'],
                'device' => $lastLogin->data['device'],
                'ip_address' => $lastLogin->data['ip_address'],
                'location' => $lastLogin->data['location'],
                'login_time' => $lastLogin->data['login_time'],
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Aucune session de connexion trouvée.'
        ], 404);
    }

    public function changePasswordForCurrentUser(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/',
        ], [
            'current_password.required' => 'Le mot de passe actuel est requis.',
            'password.required' => 'Le mot de passe est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'password.regex' => 'Le mot de passe doit contenir au moins une lettre majuscule, une lettre minuscule et un chiffre et une longueur de 8 caractères.'
        ]);

        try {
            $user = $request->user();

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le mot de passe actuel est incorrect.'
                ], 401);
            }

            $user->password = Hash::make($request->password);

            Notification::sendNow($user, new UserPasswordUpdatedNotification($user));

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Le mot de passe a été mis à jour avec succès.'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la mise à jour du mot de passe.'
            ], 500);
        }
    }

    /**
     * Reset password and send link to email
     */
    public function requestResetPasswordLink(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun utilisateur trouvé avec cette adresse email.'
            ], 404);
        }
        $url = $this->generateVerificationUrl($user, 'password.reset');

        Notification::sendNow($user, new RequestResetPasswordNotification($user, $url));

        return response()->json([
            'success' => true,
            'message' => 'Un lien de réinitialisation de mot de passe a été envoyé à votre adresse email.'
        ]);
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request, NotificationService $notificationService)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/',
            'token' => 'required|string', // hash de l'url signée
            'email' => 'required|string|email|exists:users,email',
        ], [
            'password.required' => 'Le mot de passe est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'token.required' => 'Le jeton est requis.',
            'email.required' => 'L\'adresse email est requise.',
            'password.regex' => 'Le mot de passe doit contenir au moins une lettre majuscule, une lettre minuscule et un chiffre et une longueur de 8 caractères.'
        ]);


        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun utilisateur trouvé avec cette adresse email.'
            ], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        $notificationService = new NotificationService();
        $notificationService->saveNotification([
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'type' => NotificationModel::NEW_PASSWORD_NOTIFICATION,
            'message' => 'Votre mot de passe a été réinitialisé avec succès.',
            'data' => [
                'message' => 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter avec votre nouveau mot de passe.',
                'date' => now()->format('d/m/Y H:i:s'),
                'user' => $user
            ]
        ]);

        Notification::sendNow($user, new AfterResetPasswordNotification($user));

        // invalidate all tokens
        $this->invalidateVerificationUrl($user->id, $request->token);

        return view('auth.reset-password-success', [
            'success' => true,
            'message' => 'Votre mot de passe a été réinitialisé avec succès.',
            'user' => $user
        ]);
    }

    public function resetPassword(Request $request)
    {
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
                'user_not_found' => true,
                'error_message' => 'Utilisateur non trouvé.'
            ]);
        }
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Votre mot de passe a été réinitialisé avec succès.'
        ]);
    }

    /**
     * Génère une URL signée pour la vérification d'email.
     *
     * @param  \App\Models\User  $user
     * @return string
     */
    protected function generateVerificationUrl($user, $route = 'verification.verify')
    {
        $url = URL::temporarySignedRoute(
            $route,
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $user->id,
                'hash' => sha1($user->email)
            ]
        );

        return $url;
    }

    /**
     * Invalide une URL temporaire signée en modifiant le hash de l'email de l'utilisateur
     *
     * @param int $userId L'ID de l'utilisateur
     * @param string $hash Le hash original utilisé pour la vérification
     * @return bool True si l'URL a été invalidée avec succès, false sinon
     */
    protected function invalidateVerificationUrl($userId, $hash)
    {
        try {
            // Récupérer l'utilisateur par son ID
            $user = User::find($userId);

            if (!$user) {
                // L'utilisateur n'existe pas
                return false;
            }

            // Vérifier que le hash correspond à l'email de l'utilisateur
            if (sha1($user->email) !== $hash) {
                // Le hash ne correspond pas, l'URL est déjà invalide
                return false;
            }

            // Ajouter un suffixe aléatoire à l'email dans le cache
            // Cette modification n'affecte pas l'email réel de l'utilisateur,
            // mais rendra invalide toute URL utilisant le hash original
            Cache::put(
                'invalidated_verification_' . $userId,
                Carbon::now()->timestamp,
                Carbon::now()->addDays(7)
            );

            // Optionnel : enregistrer l'invalidation dans les journaux
            Log::info("URL de vérification invalidée pour l'utilisateur #$userId");

            return true;
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'invalidation de l'URL: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer les informations sur la connexion actuelle
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function getLoginInfo(Request $request)
    {
        // Récupérer l'IP du client (en tenant compte des proxys)
        $ip = $request->header('X-Forwarded-For') ?? $request->ip();
        $browser = $request->userAgent();
        $device = $request->input('device_id', 'Inconnu');
        $platform = $request->input('device_name', 'Inconnu');

        // Initialiser les variables par défaut en cas d'erreur de récupération
        $location = 'Non disponible';

        try {
            // Appeler l'API pour obtenir les informations de localisation
            $response = Http::timeout(5)->get("http://ipinfo.io/{$ip}/json");

            if ($response->successful()) {
                $ipDetails = $response->json();
                $city = $ipDetails['city'] ?? 'Inconnu';
                $region = $ipDetails['region'] ?? 'Inconnu';
                $country = $ipDetails['country'] ?? 'Inconnu';
                $location = "{$city}, {$region}, {$country}";
            }
        } catch (\Exception $e) {
            // Gérer l'erreur d'appel API (timeout, indisponibilité, etc.)
            Log::error("Erreur lors de la récupération des informations IP : " . $e->getMessage());
        }

        // Retourner les informations sous forme de tableau
        return [
            'time' => now()->format('d/m/Y H:i:s'),
            'ip' => $ip,
            'location' => $location,
            'device' => "{$device} ({$platform})",
            'browser' => $browser,
            'device_id' => $device,
            'device_name' => $platform,
        ];
    }
}
