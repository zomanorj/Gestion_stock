<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Gestion de Stock')</title>

    {{-- Google Fonts - Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Bootstrap 5.3 + icônes + Chart.js (CDN) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>

    <style>
        /* ============================================
           DESIGN SYSTEM - Gestion de Stock
           ============================================ */
        :root {
            /* Palette de couleurs */
            --color-primary: #4f46e5;
            --color-primary-light: #eef2ff;
            --color-primary-dark: #4338ca;
            --color-surface: #ffffff;
            --color-bg: #f5f6fa;
            --color-border: #e5e7eb;
            --color-text: #111827;
            --color-muted: #6b7280;
            --color-success: #10b981;
            --color-success-light: #ecfdf5;
            --color-danger: #ef4444;
            --color-danger-light: #fef2f2;
            --color-warning: #f59e0b;
            --color-warning-light: #fffbeb;

            /* Dimensions */
            --gs-sidebar-w: 240px;
            --gs-navbar-h: 64px;

            /* Typography */
            --font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-size-base: 13.5px;
        }

        * {
            font-family: var(--font-family);
        }

        body {
            min-height: 100vh;
            background-color: var(--color-bg);
            font-size: var(--font-size-base);
            color: var(--color-text);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* ============================================
           SIDEBAR
           ============================================ */
        #gs-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: var(--gs-sidebar-w);
            background-color: var(--color-surface);
            border-right: 1px solid var(--color-border);
            z-index: 1030;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        /* Logo - immédiatement en haut, zéro espace */
        .gs-sidebar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 16px;
            border-bottom: 1px solid var(--color-border);
            flex-shrink: 0;
        }

        .gs-logo-icon {
            width: 32px;
            height: 32px;
            background-color: var(--color-primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
        }

        .gs-logo-text {
            font-size: 14px;
            font-weight: 700;
            color: var(--color-text);
            letter-spacing: -0.01em;
        }

        /* Menu navigation */
        .gs-sidebar-nav {
            padding: 8px 10px;
            flex: 1;
            overflow-y: auto;
            height: 500px;
}

        .gs-nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            margin: 2px 0;
            border-radius: 8px;
            color: var(--color-muted);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 400;
            transition: all 0.15s ease;
        }

        .gs-nav-link:hover {
            background-color: #f9fafb;
            color: var(--color-text);
        }

        .gs-nav-link.active {
            background-color: var(--color-primary-light);
            color: var(--color-primary-dark);
            font-weight: 600;
        }

        .gs-nav-link i {
            font-size: 16px;
            width: 18px;
            text-align: center;
            flex-shrink: 0;
        }

        /* Badge compteur */
        .gs-nav-badge {
            margin-left: auto;
            background-color: #f3f4f6;
            color: var(--color-muted);
            font-size: 11px;
            font-weight: 500;
            padding: 2px 8px;
            border-radius: 9999px;
        }

        /* User section en bas */
        .gs-sidebar-user {
            padding: 12px 16px;
            border-top: 1px solid var(--color-border);
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .gs-user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: var(--color-primary-light);
            color: var(--color-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            flex-shrink: 0;
        }

        .gs-user-info {
            flex: 1;
            min-width: 0;
        }

        .gs-user-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--color-text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .gs-user-role {
            font-size: 11px;
            color: var(--color-muted);
        }

        /* ============================================
           NAVBAR
           ============================================ */
        .gs-navbar {
            position: fixed;
            top: 0;
            left: var(--gs-sidebar-w);
            right: 0;
            height: 61px;
            background-color: var(--color-surface);
            border-bottom: 1px solid var(--color-border);
            z-index: 1020;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
        }

        .gs-navbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .gs-navbar-logo-icon {
            width: 38px;
            height: 38px;
            background-color: var(--color-primary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
        }

        .gs-navbar-logo-text {
            font-size: 15px;
            font-weight: 700;
            color: var(--color-text);
        }

        .gs-navbar-center {
            flex: 1;
            display: flex;
            justify-content: center;
        }

        .gs-navbar-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--color-text);
            margin: 0;
        }

        .gs-navbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .gs-user-name-navbar {
            font-size: 13px;
            color: var(--color-muted);
        }

        .gs-user-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
        }

        .gs-badge-admin {
            background-color: #eef2ff;
            color: #4338ca;
        }

        .gs-badge-gestionnaire {
            background-color: #ecfdf5;
            color: #059669;
        }

        .gs-theme-toggle {
            background: none;
            border: none;
            color: var(--color-muted);
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.15s ease;
        }

        .gs-theme-toggle:hover {
            color: var(--color-text);
        }

        .gs-theme-toggle i {
            font-size: 18px;
        }

        .gs-navbar-separator {
            width: 1px;
            height: 20px;
            background-color: var(--color-border);
        }

        .gs-logout-btn {
            background: none;
            border: none;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: var(--color-muted);
            cursor: pointer;
            transition: color 0.15s ease;
            padding: 0;
        }

        .gs-logout-btn:hover {
            color: var(--color-text);
        }

        .gs-logout-btn i {
            font-size: 16px;
        }

        /* ============================================
           MAIN CONTENT
           ============================================ */
        #gs-main {
            margin-left: var(--gs-sidebar-w);
            padding-top: var(--gs-navbar-h);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        #gs-content {
            flex: 1;
            padding: 24px;
        }

        /* Page header */
        .gs-page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .gs-page-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--color-text);
            margin: 0 0 4px 0;
            letter-spacing: -0.02em;
        }

        .gs-page-subtitle {
            font-size: 13px;
            color: var(--color-muted);
            margin: 0;
        }

        /* ============================================
           CARDS
           ============================================ */
        .gs-card {
            background-color: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: 12px;
        }

        .gs-card-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--color-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .gs-card-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--color-text);
            margin: 0;
        }

        .gs-card-body {
            padding: 16px 20px;
        }

        .gs-card-footer {
            padding: 12px 20px;
            border-top: 1px solid var(--color-border);
            background-color: #f9fafb;
            border-radius: 0 0 12px 12px;
        }

        /* ============================================
           TABLES
           ============================================ */
        .gs-table-wrapper {
            overflow-x: auto;
        }

        .gs-table {
            width: 100%;
            margin-bottom: 0;
        }

        .gs-table thead th {
            background-color: #f9fafb;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--color-muted);
            padding: 12px 16px;
            border-bottom: 1px solid var(--color-border);
            white-space: nowrap;
        }

        .gs-table tbody td {
            padding: 11px 16px;
            vertical-align: middle;
            border-bottom: 1px solid #f3f4f6;
            font-size: 13.5px;
        }

        .gs-table tbody tr:hover {
            background-color: #fafafa;
        }

        .gs-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* ============================================
           ACTION BUTTONS (compact icons)
           ============================================ */
        .gs-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border: none;
            border-radius: 6px;
            background: none;
            color: var(--color-muted);
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
            padding: 0;
        }

        .gs-action-btn:hover {
            background-color: #f3f4f6;
            color: var(--color-text);
        }

        .gs-action-btn.view:hover {
            background-color: var(--color-primary-light);
            color: var(--color-primary);
        }

        .gs-action-btn.edit:hover {
            background-color: var(--color-warning-light);
            color: var(--color-warning);
        }

        .gs-action-btn.delete:hover {
            background-color: var(--color-danger-light);
            color: var(--color-danger);
        }

        .gs-action-btn i {
            font-size: 14px;
        }

        /* ============================================
           BUTTONS
           ============================================ */
        .btn-primary {
            background-color: var(--color-primary);
            border-color: var(--color-primary);
            font-weight: 500;
            padding: 8px 16px;
            font-size: 13px;
        }

        .btn-primary:hover {
            background-color: var(--color-primary-dark);
            border-color: var(--color-primary-dark);
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        /* ============================================
           BADGES
           ============================================ */
        .badge {
            font-weight: 500;
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 9999px;
        }

        .badge-pill {
            border-radius: 9999px;
        }

        .badge-primary {
            background-color: var(--color-primary-light);
            color: var(--color-primary);
        }

        .badge-success {
            background-color: var(--color-success-light);
            color: var(--color-success);
        }

        .badge-danger {
            background-color: var(--color-danger-light);
            color: var(--color-danger);
        }

        .badge-warning {
            background-color: var(--color-warning-light);
            color: var(--color-warning);
        }

        .badge-muted {
            background-color: #f3f4f6;
            color: var(--color-muted);
        }

        /* ============================================
           FORM ELEMENTS
           ============================================ */
        .form-control, .form-select {
            font-size: 13px;
            border-radius: 6px;
            border: 1px solid var(--color-border);
            padding: 7px 12px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .form-control-sm, .form-select-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        .form-label {
            font-size: 12px;
            font-weight: 500;
            color: var(--color-muted);
            margin-bottom: 4px;
        }

        /* ============================================
           FILTER CARD
           ============================================ */
        .gs-filter-card {
            background-color: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 20px;
        }

        /* ============================================
           PAGINATION
           ============================================ */
        .pagination .page-link {
            border: 1px solid var(--color-border);
            color: var(--color-muted);
            padding: 6px 12px;
            font-size: 13px;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--color-primary);
            border-color: var(--color-primary);
            color: white;
        }

        .pagination .page-link:hover {
            background-color: #f9fafb;
            color: var(--color-text);
        }

        /* ============================================
           RESPONSIVE
           ============================================ */
        @media (max-width: 991.98px) {
            #gs-sidebar {
                transform: translateX(-100%);
                transition: transform 0.25s ease;
                z-index: 1040;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            }

            #gs-sidebar.gs-mobile-open {
                transform: translateX(0);
            }

            .gs-navbar {
                left: 0;
            }

            #gs-main {
                margin-left: 0;
            }

            .gs-sidebar-toggle {
                display: flex !important;
            }
        }

        .gs-sidebar-toggle {
            display: none;
        }

        /* Progress bar */
        .gs-progress {
            height: 6px;
            background-color: #f3f4f6;
            border-radius: 3px;
            overflow: hidden;
        }

        .gs-progress-bar {
            height: 100%;
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        .gs-progress-bar-danger {
            background-color: var(--color-danger);
        }

        .gs-progress-bar-warning {
            background-color: var(--color-warning);
        }

        /* Metric card */
        .gs-metric-card {
            background-color: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: 12px;
            padding: 18px 20px;
            position: relative;
        }

        .gs-metric-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--color-muted);
            margin-bottom: 4px;
        }

        .gs-metric-value {
            font-size: 26px;
            font-weight: 700;
            color: var(--color-text);
            margin: 0;
            letter-spacing: -0.02em;
        }

        .gs-metric-subtext {
            font-size: 11px;
            color: var(--color-muted);
            margin-top: 4px;
        }

        .gs-metric-icon {
            position: absolute;
            top: 18px;
            right: 18px;
            width: 36px;
            height: 36px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .gs-metric-icon-primary {
            background-color: var(--color-primary-light);
            color: var(--color-primary);
        }

        .gs-metric-icon-success {
            background-color: var(--color-success-light);
            color: var(--color-success);
        }

        .gs-metric-icon-danger {
            background-color: var(--color-danger-light);
            color: var(--color-danger);
        }

        .gs-metric-icon-warning {
            background-color: var(--color-warning-light);
            color: var(--color-warning);
        }

        /* Avatar */
        .gs-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: var(--color-primary-light);
            color: var(--color-primary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            flex-shrink: 0;
        }

        /* Product image placeholder */
        .gs-product-img {
            width: 40px;
            height: 40px;
            border-radius: 6px;
            object-fit: cover;
        }

        .gs-product-placeholder {
            width: 40px;
            height: 40px;
            border-radius: 6px;
            background-color: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--color-muted);
        }

        /* Footer */
        .gs-footer {
            text-align: center;
            font-size: 12px;
            color: var(--color-muted);
            padding: 16px 24px;
            border-top: 1px solid var(--color-border);
            background-color: var(--color-surface);
        }
    </style>
    @stack('styles')
</head>
<body>

    {{-- SIDEBAR ============================================ --}}
    <aside id="gs-sidebar">
        {{-- Logo - immédiatement en haut --}}
        <div class="gs-sidebar-logo">
            <div class="gs-logo-icon">
                <i class="bi bi-box-seam" style="font-size: 16px;"></i>
            </div>
            <span class="gs-logo-text">Gestion de Stock</span>
        </div>

        {{-- Navigation --}}
        <nav class="gs-sidebar-nav">
            <a class="gs-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            <a class="gs-nav-link {{ request()->routeIs('produits.*') ? 'active' : '' }}" href="{{ route('produits.index') }}">
                <i class="bi bi-box-seam"></i>
                <span>Produits</span>
                @if(isset($totalProduits) && $totalProduits !== null)
                    <span class="gs-nav-badge">{{ $totalProduits }}</span>
                @endif
            </a>
            <a class="gs-nav-link {{ request()->routeIs('mouvements.*') ? 'active' : '' }}" href="{{ route('mouvements.index') }}">
                <i class="bi bi-arrow-left-right"></i>
                <span>Mouvements</span>
            </a>
            @role('admin')
                <a class="gs-nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                    <i class="bi bi-tags"></i>
                    <span>Catégories</span>
                    @if(isset($totalCategories) && $totalCategories !== null)
                        <span class="gs-nav-badge">{{ $totalCategories }}</span>
                    @endif
                </a>
                <a class="gs-nav-link {{ request()->routeIs('fournisseurs.*') ? 'active' : '' }}" href="{{ route('fournisseurs.index') }}">
                    <i class="bi bi-truck"></i>
                    <span>Fournisseurs</span>
                    @if(isset($totalFournisseurs) && $totalFournisseurs !== null)
                        <span class="gs-nav-badge">{{ $totalFournisseurs }}</span>
                    @endif
                </a>
            @endrole
        </nav>

        {{-- User section --}}
        <div class="gs-sidebar-user">
            <div class="gs-user-avatar">
                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
            </div>
            <div class="gs-user-info">
                <div class="gs-user-name">{{ Auth::user()->name }}</div>
                <div class="gs-user-role">
                    @if(auth()->user()->hasRole('admin'))
                        Administrateur
                    @elseif(auth()->user()->hasRole('gestionnaire'))
                        Gestionnaire
                    @endif
                </div>
            </div>
        </div>
    </aside>

    {{-- NAVBAR ============================================ --}}
    <nav class="gs-navbar">
        <div class="gs-navbar-left">
            <button class="gs-sidebar-toggle btn btn-link text-muted p-0" id="gsSidebarToggle" aria-label="Ouvrir le menu">
                <i class="bi bi-list fs-4"></i>
            </button>
            <div class="gs-navbar-logo-icon d-lg-none">
                <i class="bi bi-box-seam" style="font-size: 18px;"></i>
            </div>
            <span class="gs-navbar-logo-text d-lg-none">Gestion de Stock</span>
        </div>
        <div class="gs-navbar-center">
            <h1 class="gs-navbar-title">@yield('page-title', 'Dashboard')</h1>
        </div>
        <div class="gs-navbar-right">
            <span class="gs-user-name-navbar d-none d-sm-inline">{{ Auth::user()->name }}</span>
            @if(auth()->user()->hasRole('admin'))
                <span class="gs-user-badge gs-badge-admin">admin</span>
            @elseif(auth()->user()->hasRole('gestionnaire'))
                <span class="gs-user-badge gs-badge-gestionnaire">gestionnaire</span>
            @endif
            <button class="gs-theme-toggle" id="gsThemeToggle" aria-label="Basculer le mode sombre/clair">
                <i class="bi bi-moon" id="gsThemeIcon"></i>
            </button>
            <div class="gs-navbar-separator d-none d-sm-block"></div>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="gs-logout-btn">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="d-none d-sm-inline">Déconnexion</span>
                </button>
            </form>
        </div>
    </nav>

    {{-- MAIN CONTENT ============================================ --}}
    <div id="gs-main">
        <div id="gs-content">
            @include('components.flash')
            @yield('content')
        </div>
        <footer class="gs-footer">
            Gestion de Stock © {{ date('Y') }} — Tous droits réservés
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
    <script>
        // Sidebar toggle
        document.getElementById('gsSidebarToggle')?.addEventListener('click', function () {
            document.getElementById('gs-sidebar').classList.toggle('gs-mobile-open');
        });

        // Dark/Light mode toggle
        (function() {
            const themeToggle = document.getElementById('gsThemeToggle');
            const themeIcon = document.getElementById('gsThemeIcon');
            const root = document.documentElement;

            // Check for saved theme preference or default to 'light'
            const currentTheme = localStorage.getItem('gs-theme') || 'light';
            root.setAttribute('data-theme', currentTheme);
            updateThemeIcon(currentTheme);

            // Apply theme colors
            if (currentTheme === 'dark') {
                applyDarkTheme();
            }

            themeToggle?.addEventListener('click', function() {
                const existingTheme = root.getAttribute('data-theme');
                const newTheme = existingTheme === 'dark' ? 'light' : 'dark';

                root.setAttribute('data-theme', newTheme);
                localStorage.setItem('gs-theme', newTheme);
                updateThemeIcon(newTheme);

                if (newTheme === 'dark') {
                    applyDarkTheme();
                } else {
                    removeDarkTheme();
                }
            });

            function updateThemeIcon(theme) {
                if (themeIcon) {
                    themeIcon.className = theme === 'dark' ? 'bi bi-sun' : 'bi bi-moon';
                }
            }

            function applyDarkTheme() {
                root.style.setProperty('--color-surface', '#1f2937');
                root.style.setProperty('--color-bg', '#111827');
                root.style.setProperty('--color-border', '#374151');
                root.style.setProperty('--color-text', '#f9fafb');
                root.style.setProperty('--color-muted', '#9ca3af');
            }

            function removeDarkTheme() {
                root.style.setProperty('--color-surface', '#ffffff');
                root.style.setProperty('--color-bg', '#f5f6fa');
                root.style.setProperty('--color-border', '#e5e7eb');
                root.style.setProperty('--color-text', '#111827');
                root.style.setProperty('--color-muted', '#6b7280');
            }
        })();
    </script>
    @stack('scripts')
</body>
</html>