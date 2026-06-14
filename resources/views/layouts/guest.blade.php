<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'GestiPro')</title>
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet"/>
    <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { height: 100%; }
    body {
        font-family: 'Inter', system-ui, sans-serif;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px 16px;
        background: #f0f4fa;
        position: relative;
        overflow-x: hidden;
        -webkit-font-smoothing: antialiased;
    }

    /* Fond décoratif */
    body::before {
        content: '';
        position: fixed; inset: 0;
        background:
            radial-gradient(ellipse 800px 600px at 20% 20%, rgba(30,58,138,.08) 0%, transparent 70%),
            radial-gradient(ellipse 600px 500px at 80% 80%, rgba(37,99,235,.06) 0%, transparent 70%);
        pointer-events: none;
    }
    body::after {
        content: '';
        position: fixed; inset: 0;
        background-image:
            linear-gradient(rgba(30,58,138,.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(30,58,138,.03) 1px, transparent 1px);
        background-size: 40px 40px;
        pointer-events: none;
    }

    /* Wrapper centré */
    .guest-wrap {
        width: 100%; max-width: 420px;
        position: relative; z-index: 1;
    }

    /* Logo */
    .guest-logo {
        display: flex; align-items: center; justify-content: center;
        gap: 10px; margin-bottom: 32px; text-decoration: none;
    }
    .guest-logo-icon {
        width: 44px; height: 44px; border-radius: 14px;
        background: linear-gradient(135deg, #1E3A8A, #2563EB);
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; font-weight: 900; color: #fff;
        box-shadow: 0 8px 24px rgba(30,58,138,.35);
    }
    .guest-logo-name {
        font-size: 24px; font-weight: 900; color: #111;
        letter-spacing: -.5px;
    }
    .guest-logo-name span {
        font-size: 11px; font-weight: 700; color: #2563EB;
        background: #eff6ff; padding: 2px 7px; border-radius: 6px;
        margin-left: 6px; vertical-align: middle;
    }

    /* Card principale */
    .guest-card {
        background: #fff;
        border-radius: 24px;
        border: 1px solid rgba(30,58,138,.08);
        box-shadow: 0 20px 60px rgba(30,58,138,.1), 0 4px 16px rgba(0,0,0,.04);
        padding: 36px 32px;
    }

    /* Titres */
    .card-title {
        font-size: 22px; font-weight: 800; color: #111;
        margin-bottom: 6px; letter-spacing: -.3px;
    }
    .card-subtitle {
        font-size: 14px; color: #6b7280; margin-bottom: 28px;
    }

    /* Alerte erreur */
    .alert-error {
        background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px;
        padding: 12px 16px; margin-bottom: 20px;
        font-size: 13px; color: #dc2626; line-height: 1.5;
    }
    .alert-success {
        background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px;
        padding: 12px 16px; margin-bottom: 20px;
        font-size: 13px; color: #16a34a; line-height: 1.5;
    }

    /* Formulaire */
    .form-group { margin-bottom: 18px; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 18px; }
    label {
        display: block; font-size: 13px; font-weight: 600;
        color: #374151; margin-bottom: 6px;
    }
    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="tel"] {
        width: 100%; padding: 11px 14px;
        border: 1.5px solid #e5e7eb; border-radius: 10px;
        font-size: 14px; font-family: inherit; color: #111;
        background: #fafafa;
        outline: none; transition: border-color .2s, box-shadow .2s, background .2s;
        -webkit-appearance: none;
    }
    input:focus {
        border-color: #1E3A8A;
        box-shadow: 0 0 0 3px rgba(30,58,138,.12);
        background: #fff;
    }
    input::placeholder { color: #9ca3af; }

    /* Checkbox remember */
    .remember-row {
        display: flex; align-items: center; gap: 8px; margin-bottom: 22px;
    }
    input[type="checkbox"] {
        width: 16px; height: 16px; cursor: pointer;
        accent-color: #1E3A8A;
    }
    .remember-row label { margin-bottom: 0; font-weight: 400; color: #6b7280; cursor: pointer; }

    /* Bouton submit */
    .btn-submit {
        width: 100%; padding: 13px;
        background: linear-gradient(135deg, #1E3A8A, #2563EB);
        color: #fff; font-size: 15px; font-weight: 700;
        border: none; border-radius: 12px; cursor: pointer;
        font-family: inherit;
        box-shadow: 0 4px 16px rgba(37,99,235,.35);
        transition: transform .2s, box-shadow .2s;
        letter-spacing: .1px;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(37,99,235,.4);
    }
    .btn-submit:active { transform: translateY(0); }

    /* Lien bas de card */
    .card-footer-link {
        text-align: center; font-size: 13px; color: #6b7280;
        margin-top: 22px;
    }
    .card-footer-link a {
        color: #1E3A8A; font-weight: 700; text-decoration: none;
        border-bottom: 1.5px solid rgba(30,58,138,.2);
        transition: border-color .2s;
    }
    .card-footer-link a:hover { border-color: #1E3A8A; }

    /* Footer bas page */
    .page-footer {
        text-align: center; font-size: 12px; color: #9ca3af;
        margin-top: 20px;
    }

    /* Separateur décoratif */
    .divider {
        display: flex; align-items: center; gap: 12px;
        margin: 20px 0; color: #d1d5db; font-size: 12px;
    }
    .divider::before, .divider::after {
        content: ''; flex: 1; height: 1px; background: #e5e7eb;
    }

    @media (max-width: 480px) {
        .guest-card { padding: 28px 20px; border-radius: 20px; }
        .form-grid { grid-template-columns: 1fr; }
    }
    </style>
</head>
<body>
<div class="guest-wrap">

    {{-- Logo --}}
    <a href="{{ route('home') }}" class="guest-logo">
        <div class="guest-logo-icon">G</div>
        <div class="guest-logo-name">
            GestiPro<span>B2B</span>
        </div>
    </a>

    {{-- Contenu (login ou register) --}}
    <div class="guest-card">
        @yield('content')
    </div>

    {{-- Footer --}}
    <p class="page-footer">© {{ date('Y') }} GestiPro · Application B2B · Sénégal</p>

</div>
</body>
</html>
