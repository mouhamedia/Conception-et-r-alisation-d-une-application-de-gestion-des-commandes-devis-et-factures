<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GestiPro — Gestion commerciale B2B intelligente</title>
    <meta name="description" content="Automatisez devis, commandes, factures et paiements avec l'IA intégrée.">
    <style>
    /* ─── RESET ─────────────────────────── */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }
    body { font-family: system-ui, -apple-system, 'Segoe UI', sans-serif; background: #fff; color: #1f2937; line-height: 1.6; -webkit-font-smoothing: antialiased; }
    a { text-decoration: none; }
    svg { display: block; }

    /* ─── LAYOUT ────────────────────────── */
    .container    { max-width: 1100px; margin: 0 auto; padding: 0 24px; }
    .container-sm { max-width: 760px; margin: 0 auto; padding: 0 24px; }

    @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    /* ─── NAVBAR ────────────────────────── */
    .navbar {
        position: fixed; top: 0; left: 0; right: 0; z-index: 100;
        background: rgba(255,255,255,.95);
        border-bottom: 1px solid #e5e7eb;
    }
    .navbar-inner { max-width: 1100px; margin: 0 auto; padding: 0 24px; height: 60px; display: flex; align-items: center; justify-content: space-between; }
    .brand { display: flex; align-items: center; gap: 10px; }
    .brand-icon { width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg,#1E3A8A,#2563EB); display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 15px; color: #fff; }
    .brand-name { font-size: 18px; font-weight: 800; color: #111; letter-spacing: -.4px; }
    .brand-badge { font-size: 10px; font-weight: 700; color: #2563EB; background: #eff6ff; padding: 2px 7px; border-radius: 5px; }
    .nav-links { display: flex; align-items: center; gap: 28px; }
    .nav-links a { font-size: 14px; font-weight: 500; color: #4b5563; }
    .nav-links a:hover { color: #1E3A8A; }
    .nav-actions { display: flex; align-items: center; gap: 8px; }
    .btn-link { font-size: 14px; font-weight: 600; color: #374151; padding: 8px 14px; border-radius: 8px; }
    .btn-link:hover { background: #f3f4f6; }
    .btn-cta { font-size: 14px; font-weight: 700; color: #fff; padding: 9px 20px; border-radius: 10px; background: linear-gradient(135deg,#1E3A8A,#2563EB); display: inline-flex; align-items: center; gap: 6px; }
    .btn-cta:hover { opacity: .9; }

    /* ─── HERO ──────────────────────────── */
    .hero { background: linear-gradient(150deg, #0f172a 0%, #1e3a8a 55%, #1d4ed8 100%); padding: 120px 0 80px; position: relative; overflow: hidden; }
    .hero::before { content: ''; position: absolute; inset: 0; background-image: linear-gradient(rgba(255,255,255,.04) 1px,transparent 1px), linear-gradient(90deg,rgba(255,255,255,.04) 1px,transparent 1px); background-size: 50px 50px; pointer-events: none; }
    .hero-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; position: relative; }

    .hero-badge { display: inline-flex; align-items: center; gap: 7px; background: rgba(59,130,246,.18); border: 1px solid rgba(59,130,246,.3); padding: 6px 14px; border-radius: 999px; margin-bottom: 22px; animation: fadeUp .5s ease both; }
    .hero-badge-dot { width: 7px; height: 7px; border-radius: 50%; background: #60a5fa; }
    .hero-badge span { font-size: 12px; font-weight: 600; color: #93c5fd; }

    .hero-title { font-size: clamp(34px, 4.5vw, 54px); font-weight: 900; color: #fff; line-height: 1.1; letter-spacing: -1.5px; margin-bottom: 20px; animation: fadeUp .5s ease .1s both; }
    .hero-title .gt { background: linear-gradient(135deg,#60a5fa,#bae6fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
    .hero-desc { font-size: 17px; color: rgba(186,230,253,.78); line-height: 1.7; max-width: 460px; margin-bottom: 32px; animation: fadeUp .5s ease .18s both; }
    .hero-ctas { display: flex; gap: 14px; flex-wrap: wrap; animation: fadeUp .5s ease .26s both; }
    .btn-white { font-size: 15px; font-weight: 800; color: #1E3A8A; padding: 14px 32px; border-radius: 14px; background: #fff; box-shadow: 0 4px 20px rgba(0,0,0,.15); display: inline-block; }
    .btn-white:hover { box-shadow: 0 8px 28px rgba(0,0,0,.2); transform: translateY(-2px); transition: all .2s; }
    .btn-outline { font-size: 15px; font-weight: 600; color: #fff; padding: 14px 32px; border-radius: 14px; border: 1.5px solid rgba(255,255,255,.25); display: inline-block; }
    .btn-outline:hover { background: rgba(255,255,255,.1); transition: background .2s; }

    .hero-social { display: flex; align-items: center; gap: 12px; margin-top: 28px; animation: fadeUp .5s ease .34s both; }
    .avatars { display: flex; }
    .av { width: 32px; height: 32px; border-radius: 50%; border: 2px solid rgba(255,255,255,.2); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: #fff; margin-left: -7px; }
    .av:first-child { margin-left: 0; }
    .hero-social p { font-size: 13px; color: rgba(186,230,253,.7); }
    .hero-social strong { color: #fff; }

    /* ─── MOCKUP ────────────────────────── */
    .mockup { background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.15); border-radius: 20px; padding: 20px; animation: fadeUp .5s ease .2s both; }
    .m-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
    .m-dots { display: flex; gap: 5px; }
    .m-dot { width: 9px; height: 9px; border-radius: 50%; }
    .m-title { font-size: 12px; font-weight: 700; color: #fff; }
    .m-date  { font-size: 10px; color: rgba(255,255,255,.4); }
    .m-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 10px; }
    .m-card { background: rgba(255,255,255,.09); border: 1px solid rgba(255,255,255,.1); border-radius: 12px; padding: 10px; }
    .m-card .lbl { font-size: 9px; color: rgba(255,255,255,.4); margin-bottom: 3px; }
    .m-card .val { font-size: 17px; font-weight: 900; color: #fff; }
    .m-card .tag { display: inline-block; font-size: 9px; font-weight: 700; padding: 2px 6px; border-radius: 5px; margin-top: 3px; }
    .m-chart { background: rgba(255,255,255,.09); border: 1px solid rgba(255,255,255,.1); border-radius: 12px; padding: 10px; }
    .m-chart .lbl { font-size: 9px; color: rgba(255,255,255,.4); margin-bottom: 10px; }
    .bars { display: flex; align-items: flex-end; gap: 5px; height: 52px; }
    .bar { flex: 1; border-radius: 3px 3px 0 0; background: linear-gradient(180deg,#3b82f6,#1e3a8a); }
    .bar-lbls { display: flex; justify-content: space-between; margin-top: 5px; }
    .bar-lbls span { font-size: 8px; color: rgba(255,255,255,.25); }

    .m-badge { position: absolute; bottom: -14px; right: -16px; background: #22c55e; color: #fff; font-size: 11px; font-weight: 700; padding: 7px 13px; border-radius: 999px; box-shadow: 0 4px 14px rgba(34,197,94,.4); white-space: nowrap; }

    /* ─── STATS ─────────────────────────── */
    .stats { padding: 52px 0; border-top: 1px solid #f3f4f6; border-bottom: 1px solid #f3f4f6; }
    .stats-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 20px; }
    .stat { text-align: center; padding: 24px 16px; border-radius: 16px; background: linear-gradient(135deg,#fff,#f0f7ff); border: 1px solid rgba(59,130,246,.1); }
    .stat .num { font-size: 38px; font-weight: 900; color: #1E3A8A; line-height: 1; margin-bottom: 6px; }
    .stat .lbl { font-size: 14px; font-weight: 700; color: #1f2937; }
    .stat .sub { font-size: 12px; color: #9ca3af; margin-top: 3px; }

    /* ─── SECTION HEADER ────────────────── */
    .s-tag { display: inline-block; font-size: 12px; font-weight: 700; color: #2563EB; background: #eff6ff; padding: 5px 14px; border-radius: 999px; margin-bottom: 14px; }
    .s-title { font-size: clamp(26px,3.5vw,36px); font-weight: 900; color: #111; margin-bottom: 12px; letter-spacing: -.4px; }
    .s-desc { font-size: 16px; color: #6b7280; max-width: 540px; margin: 0 auto; }

    /* ─── FEATURES ──────────────────────── */
    .features { padding: 80px 0; background: #f9fafb; }
    .f-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 20px; }
    .f-card { background: #fff; border-radius: 18px; padding: 24px; border: 1px solid #f0f0f0; position: relative; }
    .f-card:hover { box-shadow: 0 12px 36px rgba(30,58,138,.08); transform: translateY(-4px); transition: all .2s; }
    .f-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 14px; }
    .f-title { font-size: 15px; font-weight: 700; color: #111; margin-bottom: 7px; }
    .f-desc { font-size: 13px; color: #6b7280; line-height: 1.65; }
    .f-badge { position: absolute; top: 14px; right: 14px; font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 999px; }

    /* ─── STEPS ─────────────────────────── */
    .steps { padding: 80px 0; }
    .steps-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 20px; }
    .step { text-align: center; }
    .step-n { width: 52px; height: 52px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 17px; font-weight: 900; color: #fff; margin: 0 auto 14px; }
    .step-t { font-size: 14px; font-weight: 700; color: #111; margin-bottom: 6px; }
    .step-d { font-size: 13px; color: #6b7280; line-height: 1.6; }

    /* ─── TESTIMONIALS ──────────────────── */
    .testi { padding: 72px 0; background: #f9fafb; }
    .t-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 18px; }
    .t-card { background: #fff; border: 1px solid rgba(30,58,138,.08); border-radius: 18px; padding: 22px; }
    .stars { display: flex; gap: 3px; margin-bottom: 12px; color: #f59e0b; font-size: 14px; }
    .t-text { font-size: 13px; color: #374151; line-height: 1.75; font-style: italic; margin-bottom: 16px; }
    .t-author { display: flex; align-items: center; gap: 10px; }
    .t-av { width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg,#1E3A8A,#2563EB); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: #fff; flex-shrink: 0; }
    .t-name { font-size: 13px; font-weight: 700; color: #111; }
    .t-role { font-size: 11px; color: #9ca3af; }

    /* ─── CTA ───────────────────────────── */
    .cta { padding: 88px 0; background: linear-gradient(135deg,#0f172a 0%,#1e3a8a 50%,#1d4ed8 100%); text-align: center; position: relative; overflow: hidden; }
    .cta::before { content: ''; position: absolute; inset: 0; background-image: linear-gradient(rgba(255,255,255,.03) 1px,transparent 1px), linear-gradient(90deg,rgba(255,255,255,.03) 1px,transparent 1px); background-size: 50px 50px; }
    .cta .inner { position: relative; }
    .cta-badge { display: inline-block; font-size: 12px; font-weight: 600; color: #93c5fd; background: rgba(59,130,246,.18); border: 1px solid rgba(59,130,246,.3); padding: 6px 16px; border-radius: 999px; margin-bottom: 20px; }
    .cta-title { font-size: clamp(28px,4vw,44px); font-weight: 900; color: #fff; line-height: 1.15; letter-spacing: -1px; margin-bottom: 16px; }
    .cta-desc { font-size: 16px; color: rgba(186,230,253,.75); max-width: 500px; margin: 0 auto 36px; }
    .cta-btns { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
    .cta-note { margin-top: 18px; font-size: 12px; color: rgba(147,197,253,.5); }

    /* ─── FOOTER ────────────────────────── */
    .footer { background: #030712; color: #9ca3af; padding: 52px 0 24px; }
    .f-grid { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 40px; margin-bottom: 36px; }
    .f-logo { display: flex; align-items: center; gap: 9px; margin-bottom: 12px; }
    .f-logo-mark { width: 30px; height: 30px; border-radius: 9px; background: linear-gradient(135deg,#1E3A8A,#2563EB); display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 900; color: #fff; }
    .f-logo span { font-size: 16px; font-weight: 900; color: #fff; }
    .f-desc { font-size: 13px; line-height: 1.7; max-width: 260px; }
    .f-status { display: flex; align-items: center; gap: 7px; margin-top: 14px; font-size: 11px; font-weight: 500; color: #4ade80; }
    .green-dot { width: 7px; height: 7px; border-radius: 50%; background: #4ade80; }
    .f-col-title { font-size: 12px; font-weight: 700; color: #fff; margin-bottom: 14px; }
    .f-col ul { list-style: none; }
    .f-col li { margin-bottom: 9px; }
    .f-col a { font-size: 13px; color: #9ca3af; }
    .f-col a:hover { color: #fff; }
    .tech-row { display: flex; align-items: center; gap: 7px; font-size: 13px; margin-bottom: 9px; }
    .tech-dot { width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0; }
    .f-bottom { border-top: 1px solid #111827; padding-top: 22px; display: flex; align-items: center; justify-content: space-between; font-size: 12px; }

    /* ─── RESPONSIVE ────────────────────── */
    @media (max-width: 900px) {
        .hero-grid { grid-template-columns: 1fr; }
        .mockup-wrap { display: none; }
        .f-grid { grid-template-columns: 1fr; }
        .f-bottom { flex-direction: column; gap: 6px; text-align: center; }
        .nav-links { display: none; }
        .stats-grid { grid-template-columns: 1fr 1fr; }
        .f-grid-features { grid-template-columns: 1fr; }
        .t-grid { grid-template-columns: 1fr; }
        .steps-grid { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 620px) {
        .stats-grid { grid-template-columns: 1fr 1fr; }
        .f-grid { grid-template-columns: 1fr; }
        .hero-ctas, .cta-btns { flex-direction: column; align-items: center; }
    }
    </style>
</head>
<body>

{{-- NAVBAR --}}
<nav class="navbar">
    <div class="navbar-inner">
        <div class="brand">
            <div class="brand-icon">G</div>
            <span class="brand-name">GestiPro</span>
            <span class="brand-badge">B2B</span>
        </div>
        <div class="nav-links">
            <a href="#features">Fonctionnalités</a>
            <a href="#steps">Comment ça marche</a>
            <a href="#stats">Résultats</a>
        </div>
        <div class="nav-actions">
            <a href="{{ route('login') }}" class="btn-link">Connexion</a>
            <a href="{{ route('register') }}" class="btn-cta">
                Essai gratuit
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
        </div>
    </div>
</nav>

{{-- HERO --}}
<section class="hero">
    <div class="container">
        <div class="hero-grid">

            <div>
                <div class="hero-badge">
                    <div class="hero-badge-dot"></div>
                    <span>IA Prédictive intégrée · FastAPI</span>
                </div>

                <h1 class="hero-title">
                    Gérez tout votre<br>
                    <span class="gt">cycle commercial</span><br>
                    en un seul endroit
                </h1>

                <p class="hero-desc">
                    Devis, commandes, factures et paiements — automatisés, connectés
                    et pilotés par l'intelligence artificielle. Zéro saisie manuelle.
                </p>

                <div class="hero-ctas">
                    <a href="{{ route('register') }}" class="btn-white">Créer mon compte — Gratuit</a>
                    <a href="{{ route('login') }}" class="btn-outline">Déjà inscrit ?</a>
                </div>

                <div class="hero-social">
                    <div class="avatars">
                        @foreach([['#1E3A8A','A'],['#2563EB','M'],['#0ea5e9','S'],['#0284c7','D']] as [$c,$l])
                        <div class="av" style="background:{{ $c }}">{{ $l }}</div>
                        @endforeach
                    </div>
                    <p><strong>+120 entreprises</strong> utilisent GestiPro au Sénégal</p>
                </div>
            </div>

            <div class="mockup-wrap">
                <div style="position:relative;max-width:400px;margin-left:auto;">
                    <div class="mockup">
                        <div class="m-header">
                            <div>
                                <div class="m-title">Tableau de bord</div>
                                <div class="m-date">Juillet 2026</div>
                            </div>
                            <div class="m-dots">
                                <div class="m-dot" style="background:rgba(248,113,113,.7)"></div>
                                <div class="m-dot" style="background:rgba(251,191,36,.7)"></div>
                                <div class="m-dot" style="background:rgba(74,222,128,.7)"></div>
                            </div>
                        </div>
                        <div class="m-grid">
                            @foreach([
                                ['CA ce mois','4 820 000 F','+18%','color:#22c55e;background:#22c55e22'],
                                ['Factures','47','12 envoyées','color:#3b82f6;background:#3b82f622'],
                                ['Devis en cours','8','2 urgents','color:#f59e0b;background:#f59e0b22'],
                                ['Alertes stock','3','à réappro.','color:#ef4444;background:#ef444422'],
                            ] as [$lbl,$val,$tag,$style])
                            <div class="m-card">
                                <div class="lbl">{{ $lbl }}</div>
                                <div class="val">{{ $val }}</div>
                                <div class="tag" style="{{ $style }}">{{ $tag }}</div>
                            </div>
                            @endforeach
                        </div>
                        <div class="m-chart">
                            <div class="lbl">Ventes — 6 derniers mois</div>
                            <div class="bars">
                                @foreach([40,65,45,82,55,96] as $h)
                                <div class="bar" style="height:{{ $h }}%"></div>
                                @endforeach
                            </div>
                            <div class="bar-lbls">
                                @foreach(['Jan','Fév','Mar','Avr','Mai','Jun'] as $m)
                                <span>{{ $m }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="m-badge">✓ Facture #FC-2026-047 générée</div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- STATS --}}
<section id="stats" class="stats">
    <div class="container">
        <div class="stats-grid">
            @foreach([
                ['98%','Satisfaction client','Basée sur 120+ entreprises'],
                ['3x','Plus rapide','Que la gestion manuelle'],
                ['0 F','Pour commencer','Pas de carte requise'],
                ['18%','TVA automatique','Calcul en temps réel'],
            ] as [$n,$l,$s])
            <div class="stat">
                <div class="num">{{ $n }}</div>
                <div class="lbl">{{ $l }}</div>
                <div class="sub">{{ $s }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- FEATURES --}}
<section id="features" class="features">
    <div class="container">
        <div style="text-align:center;margin-bottom:48px;">
            <div class="s-tag">Fonctionnalités</div>
            <h2 class="s-title">Tout ce dont votre entreprise a besoin</h2>
            <p class="s-desc">Une plateforme complète pour automatiser votre gestion commerciale B2B.</p>
        </div>
        <div class="f-grid f-grid-features">
            @foreach([
                ['#eff6ff','#2563EB','M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z','Devis automatisés','Créez des devis professionnels en quelques clics. Numérotation auto, TVA 18%, PDF instantané.','Populaire','#eff6ff','#2563EB'],
                ['#f0fdf4','#16a34a','M13 10V3L4 14h7v7l9-11h-7z','Chaîne automatique','Devis accepté → commande → facture. Zéro saisie manuelle, zéro erreur humaine.',null,null,null],
                ['#f5f3ff','#7c3aed','M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z','IA Prédictive','Prédictions de ventes, recommandations produits et analyse KPI via FastAPI.','Nouveau','#f5f3ff','#7c3aed'],
                ['#fff7ed','#ea580c','M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z','Multi-entreprises','Gérez plusieurs entreprises depuis un seul compte. Rôles et permissions distincts.',null,null,null],
                ['#ecfeff','#0891b2','M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z','Factures & Paiements','Suivi des paiements, relances automatiques, génération PDF comptable.',null,null,null],
                ['#fef2f2','#dc2626','M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4','Gestion des stocks','Alertes stock minimum, décrémentation automatique à la validation des commandes.',null,null,null],
            ] as [$bg,$c,$path,$title,$desc,$bdgBg,$bdgC])
            <div class="f-card">
                @if($desc !== null && $bdgBg)
                @php $badge = ($c === '#2563EB') ? 'Populaire' : 'Nouveau'; @endphp
                <span class="f-badge" style="background:{{ $bdgBg }};color:{{ $bdgC }}">{{ $badge }}</span>
                @endif
                <div class="f-icon" style="background:{{ $bg }}">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="{{ $c }}" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}"/></svg>
                </div>
                <h3 class="f-title">{{ $title }}</h3>
                <p class="f-desc">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- STEPS --}}
<section id="steps" class="steps">
    <div class="container">
        <div style="text-align:center;margin-bottom:48px;">
            <div class="s-tag">Processus</div>
            <h2 class="s-title">Du devis à la facture en 4 étapes</h2>
            <p class="s-desc">Votre cycle commercial automatisé, traçable et sans erreur humaine.</p>
        </div>
        <div class="steps-grid">
            @foreach([
                ['01','#1E3A8A','Créez le devis','Renseignez client, produits et quantités. PDF professionnel généré instantanément.'],
                ['02','#4338ca','Client accepte','Le client valide. Vous confirmez d\'un clic depuis votre tableau de bord.'],
                ['03','#7c3aed','Commande auto','Commande créée automatiquement, stock décrémenté en temps réel.'],
                ['04','#9333ea','Facture & Paiement','Facture générée et envoyée. Suivi des paiements automatisé.'],
            ] as [$n,$bg,$t,$d])
            <div class="step">
                <div class="step-n" style="background:linear-gradient(135deg,{{ $bg }},{{ $bg }}cc);box-shadow:0 6px 16px {{ $bg }}44">{{ $n }}</div>
                <h3 class="step-t">{{ $t }}</h3>
                <p class="step-d">{{ $d }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- TESTIMONIALS --}}
<section class="testi">
    <div class="container">
        <div style="text-align:center;margin-bottom:40px;">
            <div class="s-tag">Témoignages</div>
            <h2 class="s-title">Ils font confiance à GestiPro</h2>
        </div>
        <div class="t-grid">
            @foreach([
                ['Amadou D.','Directeur Commercial, Dakar','GestiPro a divisé par 3 le temps consacré à nos devis. La génération automatique des factures est un gain énorme.'],
                ['Mariama S.','Gérante PME, Saint-Louis','L\'IA prédit mes ventes avec une précision remarquable. Je planifie mes stocks bien à l\'avance.'],
                ['Samba N.','Responsable Admin, Thiès','Interface intuitive, support réactif. On a mis en place GestiPro en une journée.'],
            ] as [$name,$role,$text])
            <div class="t-card">
                <div class="stars">★★★★★</div>
                <p class="t-text">"{{ $text }}"</p>
                <div class="t-author">
                    <div class="t-av">{{ substr($name,0,1) }}</div>
                    <div>
                        <div class="t-name">{{ $name }}</div>
                        <div class="t-role">{{ $role }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="cta">
    <div class="container-sm inner">
        <div class="cta-badge">Commencez en moins de 2 minutes</div>
        <h2 class="cta-title">
            Prêt à transformer votre<br>
            <span style="background:linear-gradient(135deg,#60a5fa,#bae6fd);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text">gestion commerciale ?</span>
        </h2>
        <p class="cta-desc">Rejoignez plus de 120 entreprises sénégalaises qui automatisent leur cycle commercial avec GestiPro.</p>
        <div class="cta-btns">
            <a href="{{ route('register') }}" class="btn-white" style="font-size:15px;font-weight:800;color:#1E3A8A;padding:14px 32px;border-radius:14px;background:#fff;box-shadow:0 4px 20px rgba(0,0,0,.15);">Créer mon compte — C'est gratuit</a>
            <a href="{{ route('login') }}" style="font-size:15px;font-weight:600;color:#fff;padding:14px 32px;border-radius:14px;border:1.5px solid rgba(255,255,255,.25);">Se connecter</a>
        </div>
        <p class="cta-note">Aucun engagement · Données hébergées au Sénégal · Support en français</p>
    </div>
</section>

{{-- FOOTER --}}
<footer class="footer">
    <div class="container">
        <div class="f-grid">
            <div>
                <div class="f-logo">
                    <div class="f-logo-mark">G</div>
                    <span>GestiPro</span>
                </div>
                <p class="f-desc">Application B2B de gestion commerciale intelligente pour les entrepreneurs et PME sénégalaises.</p>
                <div class="f-status"><div class="green-dot"></div> Tous les systèmes opérationnels</div>
            </div>
            <div class="f-col">
                <div class="f-col-title">Produit</div>
                <ul>
                    <li><a href="#features">Fonctionnalités</a></li>
                    <li><a href="#steps">Comment ça marche</a></li>
                    <li><a href="{{ route('register') }}">Créer un compte</a></li>
                    <li><a href="{{ route('login') }}">Se connecter</a></li>
                </ul>
            </div>
            <div class="f-col">
                <div class="f-col-title">Technologies</div>
                <div class="tech-row"><div class="tech-dot" style="background:#60a5fa"></div> Laravel {{ app()->version() }}</div>
                <div class="tech-row"><div class="tech-dot" style="background:#a78bfa"></div> FastAPI · IA Prédictive</div>
                <div class="tech-row"><div class="tech-dot" style="background:#34d399"></div> MySQL</div>
                <div class="tech-row"><div class="tech-dot" style="background:#fb923c"></div> PDF Generation</div>
            </div>
        </div>
        <div class="f-bottom">
            <p>© {{ date('Y') }} GestiPro · Gestion commerciale B2B · Sénégal</p>
            <p>Fait avec ❤️ pour les entrepreneurs sénégalais</p>
        </div>
    </div>
</footer>

</body>
</html>
