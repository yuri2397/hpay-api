<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe mis à jour | {{ config('app.name') }}</title>
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

        .success {
            color: #38a169;
        }

        .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .message {
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .user-info {
            background-color: #f7fafc;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: left;
        }

        .user-info p {
            margin: 0.5rem 0;
        }

        .user-info strong {
            font-weight: 600;
            color: #4a5568;
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

        .button-secondary {
            background-color: #a0aec0;
        }

        .button-secondary:hover {
            background-color: #718096;
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

        .buttons-container {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            border-left: 4px solid;
        }

        .alert-success {
            background-color: #f0fff4;
            border-color: #38a169;
            color: #276749;
        }
    </style>
</head>
<body>
    <div class="container">
        @if (isset($logo) && $logo)
            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="logo">
        @endif

        <div class="header success">
            <div class="icon">✓</div>
            <div class="title">Mot de passe mis à jour avec succès</div>
        </div>

        @if (isset($message) && $message)
            <div class="alert alert-success">
                {{ $message }}
            </div>
        @endif

        <div class="message">
            <p>Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter à votre compte avec votre nouveau mot de passe.</p>
        </div>

        @if (isset($user) && $user)
            <div class="user-info">
                <p><strong>Nom d'utilisateur :</strong> {{ $user->name }}</p>
                <p><strong>Email :</strong> {{ $user->email }}</p>
                <p><strong>Compte créé le :</strong> {{ $user->created_at->format('d/m/Y') }}</p>
            </div>
        @endif

        {{-- <div class="buttons-container">
            <a href="{{ route('login') }}" class="button">Se connecter maintenant</a>
            <a href="{{ route('home') }}" class="button button-secondary">Retour à l'accueil</a>
        </div> --}}

        <div class="card-footer">
            &copy; {{ date('Y') }} {{ config('app.name') }} - Tous droits réservés
        </div>
    </div>
</body>
</html>