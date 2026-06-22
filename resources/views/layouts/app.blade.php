<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'GestiPro')</title>
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <style>
    /* ═══════════════════════════════════════════
       DESIGN SYSTEM — GestiPro Professional
    ═══════════════════════════════════════════ */
    :root {
        /* Brand */
        --primary:        #2563EB;
        --primary-h:      #1D4ED8;
        --primary-bg:     #EFF6FF;
        --primary-text:   #1D4ED8;
        --primary-border: #BFDBFE;

        /* Page */
        --bg:      #F1F5F9;
        --card:    #FFFFFF;
        --card2:   #F8FAFC;
        --border:  #E2E8F0;
        --border2: #F1F5F9;

        /* Sidebar */
        --sb:         #1E293B;
        --sb-border:  rgba(255,255,255,.07);
        --sb-text:    rgba(255,255,255,.88);
        --sb-muted:   rgba(255,255,255,.42);
        --sb-hover:   rgba(255,255,255,.06);
        --sb-active:  rgba(37,99,235,.25);

        /* Text */
        --text:   #0F172A;
        --text2:  #334155;
        --muted:  #64748B;
        --muted2: #94A3B8;

        /* Aliases pour compatibilité avec les vues existantes */
        --accent:    var(--primary);
        --accent-h:  var(--primary-h);
        --accent-bg: var(--primary-bg);
        --accent-t:  var(--primary-text);
        --acc2:      #D97706;

        /* Status */
        --c-green:  #059669; --c-green-bg: #ECFDF5; --c-green-b: #A7F3D0; --c-green-t: #065F46;
        --c-yellow: #D97706; --c-yellow-bg:#FFFBEB; --c-yellow-b:#FDE68A; --c-yellow-t:#92400E;
        --c-red:    #DC2626; --c-red-bg:  #FEF2F2; --c-red-b:  #FECACA; --c-red-t:  #991B1B;
        --c-sky:    #0284C7; --c-sky-bg:  #F0F9FF; --c-sky-b:  #BAE6FD; --c-sky-t:  #0369A1;
        --c-purple: #7C3AED; --c-purple-bg:#F5F3FF;--c-purple-b:#DDD6FE;--c-purple-t:#5B21B6;
        --c-gray:   #64748B; --c-gray-bg: #F8FAFC; --c-gray-b: #E2E8F0; --c-gray-t: #475569;

        /* Shadows */
        --shadow-xs: 0 1px 2px rgba(0,0,0,.05);
        --shadow-sm: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
        --shadow-md: 0 4px 12px rgba(0,0,0,.08);
        --shadow-lg: 0 10px 30px rgba(0,0,0,.1);
        --shadow:    var(--shadow-sm);

        /* Radius */
        --r-sm: 6px; --r-md: 8px; --r-lg: 12px; --r-xl: 16px;

        /* Sidebar width */
        --sb-w: 240px;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { font-size: 14px; }
    body {
        font-family: 'Inter', system-ui, sans-serif;
        background: var(--bg);
        color: var(--text);
        display: flex;
        min-height: 100vh;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    /* ─── Scrollbar ─── */
    ::-webkit-scrollbar { width: 5px; height: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 3px; }
    ::-webkit-scrollbar-thumb:hover { background: #94A3B8; }

    /* ════════════════════════════════════════
       SIDEBAR
    ════════════════════════════════════════ */
    .sb {
        width: var(--sb-w); min-width: var(--sb-w);
        background: var(--sb);
        display: flex; flex-direction: column;
        height: 100vh; position: sticky; top: 0;
        overflow-y: auto;
    }
    .sb-overlay { display: none; }

    /* Logo */
    .sb-logo { padding: 18px 16px 14px; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid var(--sb-border); flex-shrink: 0; }
    .sb-logo-mark { width: 32px; height: 32px; background: var(--primary); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .sb-logo-text { color: #fff; font-size: 15px; font-weight: 800; letter-spacing: -.3px; }
    .sb-logo-tag { font-size: 9px; font-weight: 700; color: rgba(255,255,255,.5); background: rgba(255,255,255,.1); padding: 1px 5px; border-radius: 4px; margin-left: 5px; vertical-align: 1px; letter-spacing: .05em; }

    /* Company switcher */
    .sb-company-wrap { padding: 10px 10px 6px; flex-shrink: 0; }
    .sb-company { padding: 8px 10px; background: rgba(255,255,255,.06); border: 1px solid var(--sb-border); border-radius: 9px; display: flex; align-items: center; gap: 9px; cursor: pointer; position: relative; transition: background .15s; }
    .sb-company:hover { background: rgba(255,255,255,.09); }
    .sb-co-av { width: 28px; height: 28px; border-radius: 6px; background: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 700; color: #fff; flex-shrink: 0; }
    .sb-co-name { color: #fff; font-size: 12px; font-weight: 600; flex: 1; min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .sb-co-role { color: var(--sb-muted); font-size: 10px; }

    .sb-dd { position: fixed; background: #263548; border: 1px solid rgba(255,255,255,.1); border-radius: 10px; z-index: 9999; overflow: hidden; box-shadow: 0 8px 24px rgba(0,0,0,.4); min-width: 200px; }
    .sb-dd-item { padding: 9px 12px; cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--sb-text); background: none; border: none; width: 100%; text-align: left; transition: background .1s; }
    .sb-dd-item:hover { background: rgba(255,255,255,.07); }
    .sb-dd-item.cur { background: rgba(37,99,235,.2); color: #93C5FD; }
    .sb-dd-sep { height: 1px; background: rgba(255,255,255,.07); margin: 4px 0; }

    /* Nav */
    .sb-nav { flex: 1; padding: 4px 8px 8px; overflow-y: auto; }
    .sb-sec { font-size: 9.5px; font-weight: 700; color: var(--sb-muted); text-transform: uppercase; letter-spacing: .1em; padding: 14px 8px 4px; }
    .nav-a { display: flex; align-items: center; gap: 8px; padding: 7px 9px; border-radius: 7px; font-size: 13px; font-weight: 500; color: var(--sb-muted); text-decoration: none; transition: all .15s; margin-bottom: 1px; }
    .nav-a:hover { background: var(--sb-hover); color: var(--sb-text); }
    .nav-a.on { background: var(--sb-active); color: #93C5FD; font-weight: 600; }
    .nav-a .nav-icon { width: 15px; height: 15px; flex-shrink: 0; }
    .nav-a .nb { margin-left: auto; min-width: 18px; height: 18px; border-radius: 9px; background: #EF4444; color: #fff; font-size: 10px; font-weight: 700; display: flex; align-items: center; justify-content: center; padding: 0 4px; }

    /* Footer */
    .sb-footer { padding: 10px 14px; border-top: 1px solid var(--sb-border); display: flex; align-items: center; gap: 9px; flex-shrink: 0; }
    .sb-av { width: 30px; height: 30px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 10px; font-weight: 700; flex-shrink: 0; }
    .sb-u-name { color: #fff; font-size: 12px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .sb-u-role { color: var(--sb-muted); font-size: 10px; }

    /* ════════════════════════════════════════
       MAIN AREA
    ════════════════════════════════════════ */
    .main { flex: 1; min-width: 0; display: flex; flex-direction: column; }

    /* Topbar — WHITE, separate from sidebar */
    .topbar { height: 60px; padding: 0 28px; background: #fff; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 20; box-shadow: var(--shadow-xs); }
    .topbar-left { display: flex; align-items: center; gap: 4px; min-width: 0; }
    .topbar-burger {
        display: none; flex-shrink: 0; width: 36px; height: 36px; margin-right: 8px;
        background: var(--card2); border: 1px solid var(--border); border-radius: 8px;
        align-items: center; justify-content: center; color: var(--text2); cursor: pointer;
    }
    .topbar-burger:hover { background: var(--primary-bg); border-color: var(--primary); color: var(--primary); }
    .topbar-title { font-size: 16px; font-weight: 700; color: var(--text); letter-spacing: -.2px; }
    .topbar-sub { font-size: 11px; color: var(--muted); margin-top: 1px; }
    .topbar-right { display: flex; align-items: center; gap: 10px; }
    .topbar-notif { width: 36px; height: 36px; background: var(--card2); border: 1px solid var(--border); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--muted); text-decoration: none; position: relative; transition: all .15s; }
    .topbar-notif:hover { background: var(--primary-bg); border-color: var(--primary); color: var(--primary); }
    .notif-dot { position: absolute; top: 7px; right: 7px; width: 6px; height: 6px; background: #EF4444; border-radius: 50%; border: 1.5px solid #fff; }

    /* Flash messages */
    .flash-zone { padding: 16px 28px 0; }

    /* Content */
    .content { flex: 1; padding: 24px 28px 48px; }

    /* ════════════════════════════════════════
       GLOBAL COMPONENT LIBRARY
    ════════════════════════════════════════ */

    /* ── Alerts ── */
    .alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px; border-radius: 10px; font-size: 13px; line-height: 1.5; margin-bottom: 0; }
    .alert svg { flex-shrink: 0; margin-top: 1px; }
    .alert-success { background: var(--c-green-bg); border: 1px solid var(--c-green-b); color: var(--c-green-t); }
    .alert-error   { background: var(--c-red-bg);   border: 1px solid var(--c-red-b);   color: var(--c-red-t); }
    .alert-warning { background: var(--c-yellow-bg); border: 1px solid var(--c-yellow-b); color: var(--c-yellow-t); }
    .alert-info    { background: var(--c-sky-bg);   border: 1px solid var(--c-sky-b);   color: var(--c-sky-t); }

    /* ── Buttons ── */
    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: var(--r-md); font-size: 13px; font-weight: 600; cursor: pointer; border: none; text-decoration: none; transition: all .15s; font-family: inherit; white-space: nowrap; line-height: 1; }
    .btn-primary { background: var(--primary); color: #fff; box-shadow: 0 1px 2px rgba(37,99,235,.2); }
    .btn-primary:hover { background: var(--primary-h); box-shadow: 0 4px 12px rgba(37,99,235,.3); }
    .btn-secondary { background: #fff; color: var(--text2); border: 1.5px solid var(--border); }
    .btn-secondary:hover { background: var(--card2); border-color: #CBD5E1; }
    .btn-success { background: var(--c-green); color: #fff; }
    .btn-success:hover { background: #047857; }
    .btn-danger { background: var(--c-red); color: #fff; }
    .btn-danger:hover { background: #B91C1C; }
    .btn-ghost { background: transparent; color: var(--muted); border: 1.5px solid var(--border); }
    .btn-ghost:hover { background: var(--card2); color: var(--text2); }
    .btn-sm { padding: 5px 11px; font-size: 12px; border-radius: 6px; }
    .btn-lg { padding: 10px 22px; font-size: 14px; border-radius: 9px; }

    /* ── Badges / Pills ── */
    .badge { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; white-space: nowrap; }
    .badge-success { background: var(--c-green-bg);  color: var(--c-green); }
    .badge-warning { background: var(--c-yellow-bg); color: var(--c-yellow); }
    .badge-danger  { background: var(--c-red-bg);    color: var(--c-red); }
    .badge-info    { background: var(--primary-bg);  color: var(--primary-text); }
    .badge-sky     { background: var(--c-sky-bg);    color: var(--c-sky); }
    .badge-purple  { background: var(--c-purple-bg); color: var(--c-purple); }
    .badge-gray    { background: var(--c-gray-bg);   color: var(--c-gray); border: 1px solid var(--c-gray-b); }

    /* Status aliases — utilisées par les vues existantes */
    .sp { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; white-space: nowrap; }
    .sp-brouillon   { background: var(--c-gray-bg);   color: var(--c-gray);   border: 1px solid var(--c-gray-b); }
    .sp-envoye,
    .sp-envoyee     { background: var(--primary-bg);  color: var(--primary-text); }
    .sp-accepte,
    .sp-livree,
    .sp-payee       { background: var(--c-green-bg);  color: var(--c-green); }
    .sp-refuse,
    .sp-annulee,
    .sp-en_retard   { background: var(--c-red-bg);    color: var(--c-red); }
    .sp-en_attente  { background: var(--c-yellow-bg); color: var(--c-yellow); }
    .sp-en_cours    { background: var(--c-sky-bg);    color: var(--c-sky); }
    .sp-devis_cree  { background: var(--c-purple-bg); color: var(--c-purple); }
    .sp-acceptee    { background: var(--c-green-bg);  color: var(--c-green); }
    .sp-refusee     { background: var(--c-red-bg);    color: var(--c-red); }

    /* ── Cards ── */
    .card { background: var(--card); border-radius: var(--r-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm); }
    .card-header { padding: 16px 20px; border-bottom: 1px solid var(--border2); display: flex; align-items: center; justify-content: space-between; }
    .card-title-sm { font-size: 14px; font-weight: 700; color: var(--text); }
    .card-body { padding: 20px; }

    /* ── Stats cards ── */
    .stat-card { background: var(--card); border-radius: var(--r-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm); padding: 18px 20px; }
    .stat-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
    .stat-label { font-size: 12px; font-weight: 600; color: var(--muted); }
    .stat-icon { width: 34px; height: 34px; border-radius: 9px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .stat-value { font-size: 26px; font-weight: 800; color: var(--text); line-height: 1; }
    .stat-sub { font-size: 11px; color: var(--muted); margin-top: 6px; }
    .stat-trend { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 700; margin-top: 8px; }
    .stat-trend-up   { background: var(--c-green-bg); color: var(--c-green); }
    .stat-trend-down { background: var(--c-red-bg);   color: var(--c-red); }
    .stat-trend-flat { background: var(--c-gray-bg);  color: var(--c-gray); border: 1px solid var(--c-gray-b); }

    /* ── Tables ── */
    .table-wrap { background: var(--card); border-radius: var(--r-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm); overflow: hidden; }
    .table { width: 100%; border-collapse: collapse; }
    .table thead th { padding: 11px 16px; font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .07em; text-align: left; background: var(--card2); border-bottom: 1px solid var(--border); }
    .table tbody td { padding: 13px 16px; font-size: 13px; color: var(--text2); border-bottom: 1px solid var(--border2); }
    .table tbody tr:last-child td { border-bottom: none; }
    .table tbody tr:hover td { background: var(--primary-bg); }
    .table-pagination { padding: 12px 16px; border-top: 1px solid var(--border2); display: flex; justify-content: center; }
    /* Aliases for old .tbl / .tc */
    .tc { background: var(--card); border-radius: var(--r-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm); overflow: hidden; }
    .tbl { width: 100%; border-collapse: collapse; }
    .tbl thead th { padding: 11px 16px; font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .07em; text-align: left; background: var(--card2); border-bottom: 1px solid var(--border); }
    .tbl tbody td, .tbl td { padding: 13px 16px; font-size: 13px; color: var(--text2); border-bottom: 1px solid var(--border2); }
    .tbl tbody tr:last-child td, .tbl tr:last-child td { border-bottom: none; }
    .tbl tbody tr:hover td, .tbl tr:hover td { background: var(--primary-bg); }

    /* ── Toolbar (search + filter) ── */
    .toolbar { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; flex-wrap: wrap; }
    .search-box { flex: 1; min-width: 200px; position: relative; }
    .search-box input, .sb-i input {
        width: 100%; padding: 9px 12px 9px 36px;
        background: var(--card); border: 1.5px solid var(--border); border-radius: 8px;
        color: var(--text); font-size: 13px; outline: none; font-family: inherit;
        transition: border-color .15s, box-shadow .15s;
    }
    .search-box input:focus, .sb-i input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
    .search-box svg, .sb-i svg { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); pointer-events: none; color: var(--muted2); }
    .sb-i { flex: 1; min-width: 200px; position: relative; }
    .filter-sel, .f-sel {
        padding: 9px 12px; background: var(--card); border: 1.5px solid var(--border);
        border-radius: 8px; color: var(--text); font-size: 13px; outline: none;
        font-family: inherit; cursor: pointer; transition: border-color .15s;
    }
    .filter-sel:focus, .f-sel:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37,99,235,.1); }

    /* ── Form controls ── */
    .form-group { margin-bottom: 16px; }
    .form-label { display: block; font-size: 12px; font-weight: 600; color: var(--text2); margin-bottom: 6px; }
    .form-control { width: 100%; padding: 9px 12px; background: var(--card); border: 1.5px solid var(--border); border-radius: 8px; color: var(--text); font-size: 13px; outline: none; font-family: inherit; transition: border-color .15s, box-shadow .15s; }
    .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
    select.form-control { cursor: pointer; }

    /* ── Stats grids ── */
    .stats-4 { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; margin-bottom: 20px; }
    .stats-5 { display: grid; grid-template-columns: repeat(5,1fr); gap: 14px; margin-bottom: 20px; }
    .stats-3 { display: grid; grid-template-columns: repeat(3,1fr); gap: 14px; margin-bottom: 20px; }
    @media(max-width:1100px) { .stats-4 { grid-template-columns: repeat(2,1fr); } .stats-5 { grid-template-columns: repeat(3,1fr); } }
    @media(max-width:700px)  { .stats-3,.stats-4,.stats-5 { grid-template-columns: repeat(2,1fr); } }
    @media(max-width:480px)  { .stats-3,.stats-4,.stats-5,.pg-stats { grid-template-columns: 1fr; } }
    /* backward-compat */
    .pg-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px,1fr)); gap: 14px; margin-bottom: 20px; }
    .ps { background: var(--card); border: 1px solid var(--border); border-radius: var(--r-lg); box-shadow: var(--shadow-sm); padding: 16px 18px; display: flex; align-items: center; gap: 12px; }
    .ps-icon { width: 34px; height: 34px; border-radius: 9px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .ps-val { font-size: 22px; font-weight: 800; color: var(--text); line-height: 1; }
    .ps-lbl { font-size: 11px; color: var(--muted); margin-top: 2px; font-weight: 500; }

    /* ── Empty state ── */
    .empty-state { padding: 60px 20px; text-align: center; }
    .empty-icon { width: 52px; height: 52px; background: var(--card2); border-radius: 14px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
    .empty-title { font-size: 15px; font-weight: 700; color: var(--text); margin-bottom: 6px; }
    .empty-text { font-size: 13px; color: var(--muted); }

    /* ── Section header ── */
    .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
    .page-title-lg { font-size: 20px; font-weight: 800; color: var(--text); letter-spacing: -.3px; }
    .page-sub { font-size: 13px; color: var(--muted); margin-top: 3px; }

    /* KPI backward-compat */
    .kpi { background: var(--card); border: 1px solid var(--border); border-radius: var(--r-lg); box-shadow: var(--shadow-sm); padding: 20px; }
    .kpi-val { font-size: 27px; font-weight: 800; color: var(--text); line-height: 1.1; margin: 8px 0 4px; }
    .kpi-lbl { font-size: 12px; color: var(--muted); font-weight: 500; }
    .kpi-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .kpi-head { display: flex; align-items: center; justify-content: space-between; }
    .ch-card { background: var(--card); border: 1px solid var(--border); border-radius: var(--r-lg); box-shadow: var(--shadow-sm); padding: 20px; }
    .bp { display: inline-flex; align-items: center; gap: 4px; padding: 3px 9px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .bp-g { background: var(--c-green-bg);  color: var(--c-green); }
    .bp-r { background: var(--c-red-bg);    color: var(--c-red); }
    .bp-a { background: var(--c-yellow-bg); color: var(--c-yellow); }
    .bp-b { background: var(--primary-bg);  color: var(--primary-text); }
    .tc-head { display: flex; align-items: center; justify-content: space-between; padding: 15px 20px; border-bottom: 1px solid var(--border2); }
    .tc-ht { font-size: 14px; font-weight: 700; color: var(--text); }
    .tc-hl { font-size: 12px; color: var(--primary-text); text-decoration: none; font-weight: 600; }
    .qb { display: flex; align-items: center; gap: 9px; padding: 11px 14px; background: var(--card2); border: 1px solid var(--border); border-radius: 10px; text-decoration: none; color: var(--text2); font-size: 13px; font-weight: 500; transition: all .15s; }
    .qb:hover { background: var(--primary-bg); border-color: var(--primary); color: var(--text); }

    /* ════════════════════════════════════════
       RESPONSIVE — tablette / mobile
    ════════════════════════════════════════ */
    @media (max-width: 968px) {
        .sb {
            position: fixed; left: 0; top: 0; height: 100vh;
            transform: translateX(-100%);
            transition: transform .25s ease;
            z-index: 1000;
            box-shadow: 0 0 40px rgba(0,0,0,.25);
        }
        .sb.open { transform: translateX(0); }
        .sb-overlay {
            display: block; position: fixed; inset: 0; background: rgba(15,23,42,.5);
            z-index: 999; opacity: 0; pointer-events: none; transition: opacity .25s ease;
        }
        .sb-overlay.show { opacity: 1; pointer-events: auto; }
        .topbar-burger { display: flex; }
        .topbar { padding: 0 16px; }
        .flash-zone { padding: 12px 16px 0; }
        .content { padding: 16px 16px 32px; }
        .page-header { gap: 12px; }
    }
    @media (min-width: 969px) {
        .topbar-burger { display: none; }
        .sb-overlay { display: none !important; }
    }

    /* Tableaux : défilement horizontal plutôt qu'écrasement sur petit écran */
    @media (max-width: 768px) {
        .table-wrap, .tc { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .table, .tbl { min-width: 640px; }
    }
    </style>
    @stack('styles')
</head>
<body x-data="{ sidebarOpen: false }">

{{-- ═══ SIDEBAR ═══ --}}
<aside class="sb" :class="{ open: sidebarOpen }">

    {{-- Logo --}}
    <div class="sb-logo">
        <div class="sb-logo-mark">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <div class="sb-logo-text">GestiPro <span class="sb-logo-tag">B2B</span></div>
    </div>

    @php
        $currentEntreprise = \App\Models\Entreprise::find(session('entreprise_id'));
        $mesEntreprises    = auth()->user()->entreprises()->withPivot('role')->get();
        $initiales         = strtoupper(substr($currentEntreprise?->nom ?? 'G', 0, 2));
        $roleActuel        = auth()->user()->getRoleInEntreprise(session('entreprise_id'));
    @endphp

    {{-- Company switcher --}}
    <div class="sb-company-wrap">
        <div class="sb-company" id="switcherBtn" onclick="toggleSwitcher()">
            <div class="sb-co-av">{{ $initiales }}</div>
            <div style="flex:1;min-width:0;">
                <div class="sb-co-name">{{ $currentEntreprise?->nom ?? 'Entreprise' }}</div>
                <div class="sb-co-role">{{ $roleActuel === 'owner' ? 'Propriétaire' : 'Employé' }}</div>
            </div>
            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="rgba(255,255,255,.4)" stroke-width="2" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/></svg>
        </div>
    </div>

    {{-- Dropdown hors sidebar (position:fixed, évite le clipping overflow) --}}
    <div class="sb-dd" id="switcherDropdown" style="display:none;">
        @foreach($mesEntreprises as $ent)
        <form method="POST" action="{{ route('entreprise.switch') }}">
            @csrf
            <input type="hidden" name="entreprise_id" value="{{ $ent->id }}">
            <button type="submit" class="sb-dd-item {{ $ent->id == session('entreprise_id') ? 'cur' : '' }}">
                <div style="width:24px;height:24px;border-radius:5px;background:{{ $ent->id == session('entreprise_id') ? 'var(--primary)' : 'rgba(255,255,255,.1)' }};display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:700;color:white;flex-shrink:0;">{{ strtoupper(substr($ent->nom,0,2)) }}</div>
                <div style="flex:1;text-align:left;">
                    <div style="font-weight:600;font-size:12px;">{{ $ent->nom }}</div>
                    <div style="font-size:10px;color:rgba(255,255,255,.4);">{{ $ent->pivot->role === 'owner' ? 'Propriétaire' : 'Employé' }}</div>
                </div>
                @if($ent->id == session('entreprise_id'))
                <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="#93C5FD" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                @endif
            </button>
        </form>
        @endforeach
        <div class="sb-dd-sep"></div>
        <a href="{{ route('entreprise.select') }}" class="sb-dd-item" style="text-decoration:none;">
            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Nouvelle entreprise
        </a>
    </div>

    {{-- Navigation --}}
    <nav class="sb-nav">
        @php
            $nbDevis     = \App\Models\Devis::where('entreprise_id',session('entreprise_id'))->whereIn('statut',['brouillon','envoye'])->count();
            $nbCommandes = \App\Models\Commande::where('entreprise_id',session('entreprise_id'))->whereIn('statut',['en_attente','en_cours'])->count();
            $nbNotifs    = \App\Models\Notification::where('user_id',auth()->id())->where('entreprise_id',session('entreprise_id'))->where('lu',false)->count();
            $nbDemandes  = \App\Models\DemandeDevis::where('entreprise_cible_id',session('entreprise_id'))->where('statut','en_attente')->count();
        @endphp

        <div class="sb-sec">Principal</div>
        <a href="{{ route('dashboard.index') }}" class="nav-a {{ request()->routeIs('dashboard.*') ? 'on' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            Dashboard
        </a>
        <a href="{{ route('devis.index') }}" class="nav-a {{ request()->routeIs('devis.*') ? 'on' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Devis
            @if($nbDevis > 0)<span class="nb">{{ $nbDevis }}</span>@endif
        </a>
        <a href="{{ route('commandes.index') }}" class="nav-a {{ request()->routeIs('commandes.*') ? 'on' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            Commandes
            @if($nbCommandes > 0)<span class="nb">{{ $nbCommandes }}</span>@endif
        </a>
        <a href="{{ route('factures.index') }}" class="nav-a {{ request()->routeIs('factures.*') ? 'on' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            Factures
        </a>

        <div class="sb-sec">Catalogue</div>
        <a href="{{ route('marketplace.index') }}" class="nav-a {{ request()->routeIs('marketplace.*') ? 'on' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Marketplace B2B
            @if($nbDemandes > 0)<span class="nb">{{ $nbDemandes }}</span>@endif
        </a>
        <a href="{{ route('produits.index') }}" class="nav-a {{ request()->routeIs('produits.*') ? 'on' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            Produits
        </a>
        <a href="{{ route('categories.index') }}" class="nav-a {{ request()->routeIs('categories.*') ? 'on' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            Catégories
        </a>
        <a href="{{ route('entreprise.equipe') }}" class="nav-a {{ request()->routeIs('entreprise.equipe') ? 'on' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Équipe
        </a>

        <div class="sb-sec">Intelligence</div>
        <a href="{{ route('ia.dashboard') }}" class="nav-a {{ request()->routeIs('ia.*') ? 'on' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H4a2 2 0 01-2-2V5a2 2 0 012-2h16a2 2 0 012 2v10a2 2 0 01-2 2h-1"/></svg>
            Prédictions IA
        </a>
        <a href="{{ route('notifications.index') }}" class="nav-a {{ request()->routeIs('notifications.*') ? 'on' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            Notifications
            @if($nbNotifs > 0)<span class="nb">{{ $nbNotifs }}</span>@endif
        </a>

        <div class="sb-sec">Compte</div>
        <a href="{{ route('entreprise.edit') }}" class="nav-a {{ request()->routeIs('entreprise.edit') ? 'on' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Paramètres
        </a>
    </nav>

    {{-- User footer --}}
    <div class="sb-footer">
        <div class="sb-av">{{ strtoupper(substr(auth()->user()->prenom ?? auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
        <div style="flex:1;min-width:0;">
            <div class="sb-u-name">{{ auth()->user()->prenom }} {{ auth()->user()->name }}</div>
            <div class="sb-u-role">{{ $roleActuel === 'owner' ? 'Propriétaire' : 'Employé' }}</div>
        </div>
        <form method="POST" action="{{ route('logout') }}" style="flex-shrink:0;">
            @csrf
            <button type="submit" title="Déconnexion" style="background:none;border:none;cursor:pointer;color:rgba(255,255,255,.35);padding:4px;border-radius:5px;transition:color .15s;" onmouseover="this.style.color='rgba(255,255,255,.7)'" onmouseout="this.style.color='rgba(255,255,255,.35)'">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            </button>
        </form>
    </div>
</aside>

<div class="sb-overlay" :class="{ show: sidebarOpen }" @click="sidebarOpen = false"></div>

{{-- ═══ MAIN ═══ --}}
<div class="main">

    {{-- Topbar --}}
    <div class="topbar">
        <div class="topbar-left">
            <button type="button" class="topbar-burger" @click="sidebarOpen = !sidebarOpen" aria-label="Ouvrir le menu">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div style="min-width:0;">
                <div class="topbar-title">@yield('page-title', 'GestiPro')</div>
                @if(View::hasSection('page-subtitle'))<div class="topbar-sub">@yield('page-subtitle')</div>@endif
            </div>
        </div>
        <div class="topbar-right">
            @yield('topbar-actions')
            <a href="{{ route('notifications.index') }}" class="topbar-notif">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                @if($nbNotifs > 0)<span class="notif-dot"></span>@endif
            </a>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success') || session('error') || session('info') || $errors->any())
    <div class="flash-zone">
        @if(session('success'))
        <div class="alert alert-success">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-error">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
        @endif
        @if(session('info'))
        <div class="alert alert-info">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('info') }}
        </div>
        @endif
        @if($errors->any())
        <div class="alert alert-error" style="flex-direction:column;align-items:flex-start;">
            <div style="font-weight:700;margin-bottom:4px;">Veuillez corriger les erreurs suivantes :</div>
            <ul style="padding-left:16px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif
    </div>
    @endif

    {{-- Page content --}}
    <div class="content">
        @yield('content')
    </div>

</div>

<script>
/* Company switcher — fixed position to avoid sidebar overflow clipping */
function toggleSwitcher() {
    const dd  = document.getElementById('switcherDropdown');
    const btn = document.getElementById('switcherBtn');
    if (dd.style.display === 'none' || !dd.style.display) {
        const r = btn.getBoundingClientRect();
        dd.style.top   = (r.bottom + 6) + 'px';
        dd.style.left  = r.left + 'px';
        dd.style.width = r.width + 'px';
        dd.style.display = 'block';
    } else {
        dd.style.display = 'none';
    }
}
document.addEventListener('click', e => {
    const btn = document.getElementById('switcherBtn');
    const dd  = document.getElementById('switcherDropdown');
    if (btn && dd && !btn.contains(e.target) && !dd.contains(e.target)) {
        dd.style.display = 'none';
    }
});
</script>
@stack('scripts')
</body>
</html>
