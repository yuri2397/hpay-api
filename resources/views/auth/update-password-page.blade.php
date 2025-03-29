<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de mot de passe | {{ config('app.name') }}</title>
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
            border: none;
            cursor: pointer;
        }

        .button:hover {
            background-color: #2779bd;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: #3490dc;
            box-shadow: 0 0 0 3px rgba(52, 144, 220, 0.25);
        }

        .is-invalid {
            border-color: #e53e3e;
        }

        .invalid-feedback {
            color: #e53e3e;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background-color: #f0fff4;
            border: 1px solid #c6f6d5;
            color: #38a169;
        }

        .alert-danger {
            background-color: #fff5f5;
            border: 1px solid #fed7d7;
            color: #e53e3e;
        }

        .alert-warning {
            background-color: #fffaf0;
            border: 1px solid #feebc8;
            color: #d69e2e;
        }

        .password-requirements {
            font-size: 0.875rem;
            color: #718096;
            margin-top: 0.5rem;
            text-align: left;
        }

        .logo {
            max-width: 150px;
            margin-bottom: 1.5rem;
        }

        .card-footer {
            margin-top: 2rem;
            font-size: 0.875rem;
            color: #718096;
        }
    </style>
</head>

<body>
    <div class="container">
        @if (isset($logo) && $logo)
            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="logo">
        @endif

        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        @if (isset($success) && $success)
            <div class="header success">
                <div class="icon">✓</div>
                <div class="title">Réinitialisation de mot de passe</div>
                <p class="message">Veuillez créer un nouveau mot de passe pour votre compte.</p>
            </div>

            {{-- form to update password --}}
            <form action="{{ route('password.update') }}" method="post">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="form-group">
                    <label for="password">Nouveau mot de passe</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        required
                        autocomplete="new-password"
                        placeholder="Nouveau mot de passe"
                    >
                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    <div class="password-requirements">
                        Le mot de passe doit contenir au moins 8 caractères, incluant des lettres majuscules, minuscules et des chiffres.
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirmation du mot de passe</label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="form-control"
                        required
                        autocomplete="new-password"
                        placeholder="Confirmation du mot de passe"
                    >
                </div>

                <button type="submit" class="button">Réinitialiser le mot de passe</button>
            </form>
        @elseif (isset($error) && $error)
            <div class="header error">
                <div class="icon">✕</div>
                <div class="title">Lien invalide</div>
                <p class="message">
                    Le lien de réinitialisation du mot de passe est invalide ou a expiré.
                    Veuillez faire une nouvelle demande de réinitialisation.
                </p>
                <a href="{{ route('password.request') }}" class="button">
                    Nouvelle demande
                </a>
            </div>
        @elseif (isset($expired) && $expired)
            <div class="header warning">
                <div class="icon">⚠</div>
                <div class="title">Lien expiré</div>
                <p class="message">
                    Le lien de réinitialisation du mot de passe a expiré.
                    Veuillez faire une nouvelle demande de réinitialisation.
                </p>
                <a href="{{ route('password.request') }}" class="button">
                    Nouvelle demande
                </a>
            </div>
        @else
            <div class="header error">
                <div class="icon">⚠</div>
                <div class="title">Une erreur est survenue</div>
                <p class="message">
                    Une erreur inattendue s'est produite. Veuillez réessayer plus tard ou
                    contacter le support technique.
                </p>
                <a href="{{ route('password.request') }}" class="button">
                    Retour
                </a>
            </div>
        @endif

        <div class="card-footer">
            &copy; {{ date('Y') }} {{ config('app.name') }} - Tous droits réservés
        </div>
    </div>
</body>

</html>
