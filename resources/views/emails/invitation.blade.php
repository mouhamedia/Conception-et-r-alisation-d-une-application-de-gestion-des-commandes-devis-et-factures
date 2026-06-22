<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Invitation GestiPro</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8fafc; margin: 0; padding: 40px 0; }
        .container { max-width: 560px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
        .header { background: #1E3A8A; padding: 30px 40px; text-align: center; }
        .header h1 { color: white; font-size: 24px; margin: 0; }
        .body { padding: 32px 40px; }
        .body p { color: #374151; line-height: 1.7; margin: 0 0 16px; }
        .btn { display: inline-block; padding: 14px 32px; background: #1E3A8A; color: white !important; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 15px; }
        .btn-wrap { text-align: center; margin: 28px 0; }
        .footer { background: #f8fafc; padding: 20px 40px; text-align: center; font-size: 12px; color: #9ca3af; }
        .entreprise-name { color: #1E3A8A; font-weight: bold; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>GestiPro</h1>
    </div>
    <div class="body">
        <p>Bonjour,</p>
        <p>
            Vous avez été invité(e) à rejoindre l'entreprise
            <span class="entreprise-name">{{ $invitation->entreprise->nom }}</span>
            sur GestiPro en tant que <strong>{{ $invitation->role === 'owner' ? 'Propriétaire' : 'Employé(e)' }}</strong>.
        </p>
        <p>
            GestiPro est une application de gestion commerciale qui vous permet de gérer
            vos devis, commandes et factures en toute simplicité.
        </p>
        <div class="btn-wrap">
            <a href="{{ $lien }}" class="btn">Accepter l'invitation</a>
        </div>
        <p>
            Ce lien est valable jusqu'au {{ $invitation->expires_at->format('d/m/Y à H:i') }}.
            Après cette date, vous devrez demander une nouvelle invitation.
        </p>
        <p>Si vous n'êtes pas concerné(e) par cette invitation, ignorez simplement cet email.</p>
    </div>
    <div class="footer">
        GestiPro · Application de gestion commerciale B2B<br>
        <a href="{{ url('/') }}" style="color:#1E3A8A;">{{ url('/') }}</a>
    </div>
</div>
</body>
</html>
