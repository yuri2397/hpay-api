<!-- resources/views/emails/verify.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Vérification d'Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #3490dc;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
            background-color: #f8fafc;
        }
        .button {
            display: inline-block;
            background-color: #3490dc;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
        }
        .footer {
            padding: 20px;
            text-align: center;
            font-size: 0.8em;
            color: #718096;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Vérification de votre adresse email</h1>
        </div>
        
        <div class="content">
            <p>Bonjour {{ $name }},</p>
            
            <p>Merci de vous être inscrit sur notre plateforme. Afin de finaliser votre inscription, veuillez cliquer sur le bouton ci-dessous pour vérifier votre adresse email :</p>
            
            <p style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="button">Vérifier mon adresse email</a>
            </p>
            
            <p>Si vous n'avez pas créé de compte, aucune action n'est requise de votre part.</p>
            
            <p>Si vous ne parvenez pas à cliquer sur le bouton, vous pouvez copier et coller le lien suivant dans votre navigateur :</p>
            <p>{{ $verificationUrl }}</p>
            
            <p>Cordialement,<br>L'équipe de support</p>
        </div>
        
        <div class="footer">
            <p>Ce message a été envoyé automatiquement. Merci de ne pas y répondre.</p>
            <p>&copy; {{ date('Y') }} Votre Entreprise. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
