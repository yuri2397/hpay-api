<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur de réinitialisation | {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f8fafc;
            color: #1a202c;
        }

        .container {
            max-width: 600px;
            width: 100%;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            background-color: white;
            text-align: center;
        }

        .header {
            margin-bottom: 2rem;
        }

        .title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .error {
            color: #e53e3e;
        }

        .warning {
            color: #d69e2e;
        }

        .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .message {
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .button {
            display: inline-block;
            background-color: #3490dc;
            color: white;
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            transition: background-color 0.2s;
        }

        .button:hover {
            background-color: #2779bd;
        }

        .card-footer {
            margin-top: 2rem;
            font-size: 0.875rem;
            color: #718096;
        }

        .logo {
            max-width: 150px;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        @if (isset($logo) && $logo)
            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="logo">
        @endif

        @if(isset($expired) && $expired)
            <div class="header warning">
                <div class="icon">⚠</div>
                <div class="title">Lien expiré</div>
            </div>
            <div class="message">
                <p>Le lien de réinitialisation de mot de passe a expiré.</p>
                <p>Pour des raisons de sécurité, les liens de réinitialisation sont valables 60 minutes.</p>
                <p>Veuillez faire une nouvelle demande de réinitialisation.</p>
            </div>
            <a href="{{ route('password.request') }}" class="button">Nouvelle demande</a>

        @elseif(isset($user_not_found) && $user_not_found)
            <div class="header error">
                <div class="icon">✗</div>
                <div class="title">Utilisateur introuvable</div>
            </div>
            <div class="message">
                <p>Nous n'avons pas pu trouver de compte associé à ce lien de réinitialisation.</p>
                <p>{{ isset($error_message) ? $error_message : 'Veuillez vérifier votre adresse email ou créer un nouveau compte.' }}</p>
            </div>
            <div style="display: flex; justify-content: center; gap: 1rem;">
                <a href="{{ route('password.request') }}" class="button">Réessayer</a>
                <a href="{{ route('register') }}" class="button" style="background-color: #48bb78;">Créer un compte</a>
            </div>

        @else
            <div class="header error">
                <div class="icon">✗</div>
                <div class="title">Lien invalide</div>
            </div>
            <div class="message">
                <p>Le lien de réinitialisation de mot de passe est invalide ou a été manipulé.</p>
                <p>Veuillez vérifier l'URL ou faire une nouvelle demande de réinitialisation.</p>
            </div>
            <a href="{{ route('password.request') }}" class="button">Nouvelle demande</a>
        @endif

        <div class="card-footer">
            &copy; {{ date('Y') }} {{ config('app.name') }} - Tous droits réservés
        </div>
    </div>
</body>
</html>