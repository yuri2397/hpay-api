<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renvoyer le lien de vérification | {{ config('app.name') }}</title>
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
        }
        .header {
            margin-bottom: 2rem;
            text-align: center;
        }
        .title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .message {
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        .form-input {
            width: 100%;
            padding: 0.75rem;
            font-size: 1rem;
            border-radius: 4px;
            border: 1px solid #d2d6dc;
            outline: none;
            transition: border-color 0.2s;
        }
        .form-input:focus {
            border-color: #3490dc;
            box-shadow: 0 0 0 3px rgba(52, 144, 220, 0.25);
        }
        .button {
            display: inline-block;
            background-color: #3490dc;
            color: white;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
            width: 100%;
        }
        .button:hover {
            background-color: #2779bd;
        }
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #f0fff4;
            border: 1px solid #c6f6d5;
            color: #38a169;
        }
        .alert-danger {
            background-color: #fff5f5;
            border: 1px solid #feb2b2;
            color: #e53e3e;
        }
        .links {
            margin-top: 1.5rem;
            text-align: center;
        }
        .links a {
            color: #3490dc;
            text-decoration: none;
        }
        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="title">Renvoyer le lien de vérification</div>
            <p>Entrez votre adresse email pour recevoir un nouveau lien de vérification</p>
        </div>

        @if(session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('verification.send') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email" class="form-label">Adresse email</label>
                <input id="email" name="email" type="email" class="form-input" required autofocus>
                @error('email')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="button">Envoyer le lien de vérification</button>
        </form>

        <div class="links">
            <a href="{{ url('/login') }}">Retour à la connexion</a>
        </div>
    </div>
</body>
</html>
