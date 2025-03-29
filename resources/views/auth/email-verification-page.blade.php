<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification du compte | {{ config('app.name') }}</title>
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
    </style>
</head>
<body>
    <div class="container">
        @if(isset($success) && $success)
            <div class="header success">
                <div class="icon">✓</div>
                <div class="title">Compte vérifié avec succès !</div>
            </div>
            <div class="message">
                <p>Votre adresse email a été vérifiée et votre compte est maintenant actif.</p>
                <p>Vous pouvez maintenant vous connecter à votre compte et profiter de tous nos services.</p>
            </div>
            <a href="{{ env('FRONTEND_URL') }}" class="button">Se connecter</a>
        @elseif(isset($already_verified) && $already_verified)
            <div class="header warning">
                <div class="icon">ℹ</div>
                <div class="title">Email déjà vérifié</div>
            </div>
            <div class="message">
                <p>Votre adresse email a déjà été vérifiée.</p>
                <p>Vous pouvez vous connecter à votre compte et profiter de tous nos services.</p>
            </div>
            <a href="{{ env('FRONTEND_URL') }}" class="button">Se connecter</a>
        @elseif(isset($expired) && $expired)
            <div class="header error">
                <div class="icon">⚠</div>
                <div class="title">Lien expiré</div>
            </div>
            <div class="message">
                <p>Le lien de vérification a expiré.</p>
                <p>Veuillez demander un nouveau lien de vérification pour activer votre compte.</p>
            </div>
            <a href="{{ env('FRONTEND_URL') }}/resend-verification" class="button">Demander un nouveau lien</a>
        @else
            <div class="header error">
                <div class="icon">✗</div>
                <div class="title">Erreur de vérification</div>
            </div>
            <div class="message">
                <p>Une erreur s'est produite lors de la vérification de votre adresse email.</p>
                <p>{{ isset($error_message) ? $error_message : 'Veuillez réessayer ou contacter notre support technique.' }}</p>
            </div>
            <a href="{{ env('FRONTEND_URL') }}/contact" class="button">Contacter le support</a>
        @endif
    </div>
</body>
</html>
