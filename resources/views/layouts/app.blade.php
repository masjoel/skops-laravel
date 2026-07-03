<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ session('LOGO') ? '' : 'Superstore' }}</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Font Awesome --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --sidebar-active: #6366f1;
            --sidebar-text: #94a3b8;
            --sidebar-text-active: #f8fafc;
            --topbar-h: 64px;
            --accent: #6366f1;
            --accent-hover: #4f46e5;
            --body-bg: #f1f5f9;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --shadow: 0 1px 3px rgba(0, 0, 0, .08), 0 1px 2px rgba(0, 0, 0, .06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, .1), 0 2px 4px -1px rgba(0, 0, 0, .06);
        }

        [data-theme="dark"] {
            --body-bg: #0f172a;
            --card-bg: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border-color: #334155;
            --sidebar-bg: #020617;
            --sidebar-hover: #0f172a;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--body-bg);
            color: var(--text-main);
            margin: 0;
            transition: background .3s, color .3s;
        }

        /* ── SIDEBAR ── */
        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            z-index: 1040;
            transition: transform .3s ease;
            overflow: hidden;
        }

        #sidebar.collapsed {
            transform: translateX(calc(-1 * var(--sidebar-width)));
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 20px 20px 16px;
            border-bottom: 1px solid #1e293b;
            min-height: var(--topbar-h);
            text-decoration: none;
        }

        .sidebar-brand-icon {
            width: 36px;
            height: 36px;
            background: var(--accent);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: white;
            flex-shrink: 0;
        }

        .sidebar-brand-name {
            font-size: 15px;
            font-weight: 700;
            color: #f8fafc;
            line-height: 1.2;
        }

        .sidebar-brand-sub {
            font-size: 11px;
            color: var(--sidebar-text);
            font-weight: 400;
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 12px 0;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }

        .nav-label {
            padding: 12px 20px 4px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: #475569;
        }

        .nav-item-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 20px;
            color: var(--sidebar-text);
            text-decoration: none;
            border-radius: 0;
            font-size: 13.5px;
            font-weight: 500;
            transition: all .2s;
            position: relative;
        }

        .nav-item-link:hover {
            background: var(--sidebar-hover);
            color: var(--sidebar-text-active);
        }

        .nav-item-link.active {
            background: linear-gradient(90deg, rgba(99, 102, 241, .15), transparent);
            color: var(--sidebar-text-active);
        }

        .nav-item-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--accent);
            border-radius: 0 3px 3px 0;
        }

        .nav-item-link .nav-icon {
            width: 18px;
            text-align: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .nav-item-link .nav-badge {
            margin-left: auto;
            font-size: 10px;
            padding: 2px 7px;
            border-radius: 20px;
            background: #ef4444;
            color: white;
            font-weight: 600;
        }

        /* Submenu */
        .has-sub .sub-arrow {
            margin-left: auto;
            font-size: 11px;
            transition: transform .25s;
        }

        .has-sub.open .sub-arrow {
            transform: rotate(90deg);
        }

        .sub-menu {
            overflow: hidden;
        }

        .sub-menu .nav-item-link {
            padding-left: 48px;
            font-size: 13px;
        }

        .sidebar-footer {
            padding: 14px 20px;
            border-top: 1px solid #1e293b;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-footer-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #334155;
            background: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 13px;
            font-weight: 600;
            flex-shrink: 0;
            overflow: hidden;
        }

        .sidebar-footer-info {
            flex: 1;
            min-width: 0;
        }

        .sidebar-footer-name {
            font-size: 13px;
            font-weight: 600;
            color: #f8fafc;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-footer-role {
            font-size: 11px;
            color: #64748b;
        }

        /* ── TOPBAR ── */
        #topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--topbar-h);
            background: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            padding: 0 24px;
            z-index: 1030;
            transition: left .3s, background .3s;
            gap: 12px;
        }

        #topbar.full-width {
            left: 0;
        }

        .btn-sidebar-toggle {
            width: 36px;
            height: 36px;
            border: none;
            background: transparent;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            font-size: 18px;
            transition: background .2s, color .2s;
        }

        .btn-sidebar-toggle:hover {
            background: var(--border-color);
            color: var(--text-main);
        }

        .topbar-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-main);
        }

        .topbar-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .topbar-btn {
            width: 38px;
            height: 38px;
            border: none;
            background: transparent;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            font-size: 16px;
            transition: background .2s, color .2s;
            position: relative;
        }

        .topbar-btn:hover {
            background: var(--border-color);
            color: var(--text-main);
        }

        .topbar-btn .badge-dot {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 50%;
            border: 2px solid var(--card-bg);
        }

        /* ── MAIN CONTENT ── */
        #main-wrapper {
            margin-left: var(--sidebar-width);
            padding-top: var(--topbar-h);
            min-height: 100vh;
            transition: margin-left .3s;
        }

        #main-wrapper.full-width {
            margin-left: 0;
        }

        #main-content {
            padding: 24px;
        }

        /* ── OVERLAY ── */
        #sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .5);
            z-index: 1039;
        }

        /* ── CARDS ── */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: var(--shadow);
            transition: box-shadow .2s, background .3s;
        }

        .card:hover {
            box-shadow: var(--shadow-md);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border-color);
            font-weight: 600;
            font-size: 14px;
            padding: 14px 20px;
        }

        .card-body {
            padding: 20px;
        }

        /* ── TABLES ── */
        .table {
            color: var(--text-main);
        }

        .table thead th {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-color);
            padding: 10px 14px;
            background: transparent;
        }

        .table tbody td {
            padding: 11px 14px;
            border-color: var(--border-color);
            font-size: 13.5px;
            vertical-align: middle;
        }

        .table-hover tbody tr:hover {
            background: rgba(99, 102, 241, .04);
        }

        /* ── BADGES ── */
        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
        }

        .badge-status::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }

        .badge-success {
            background: rgba(34, 197, 94, .12);
            color: #16a34a;
        }

        .badge-success::before {
            background: #16a34a;
        }

        .badge-danger {
            background: rgba(239, 68, 68, .12);
            color: #dc2626;
        }

        .badge-danger::before {
            background: #dc2626;
        }

        .badge-warning {
            background: rgba(245, 158, 11, .12);
            color: #d97706;
        }

        .badge-warning::before {
            background: #d97706;
        }

        /* ── BUTTONS ── */
        .btn-accent {
            background: var(--accent);
            color: white;
            border: none;
        }

        .btn-accent:hover {
            background: var(--accent-hover);
            color: white;
        }

        .btn {
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 500;
        }

        /* ── FORMS ── */
        .form-control,
        .form-select {
            border-radius: 8px;
            border-color: var(--border-color);
            background: var(--card-bg);
            color: var(--text-main);
            font-size: 13.5px;
            transition: border-color .2s, box-shadow .2s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
        }

        .form-label {
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 6px;
        }

        /* ── PAGE HEADER ── */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .page-header h1 {
            font-size: 20px;
            font-weight: 700;
            margin: 0;
        }

        .breadcrumb {
            font-size: 12.5px;
            margin: 0;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            color: var(--text-muted);
        }

        .breadcrumb-item a {
            color: var(--accent);
            text-decoration: none;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 991.98px) {
            #sidebar {
                transform: translateX(calc(-1 * var(--sidebar-width)));
            }

            #sidebar.mobile-open {
                transform: translateX(0);
            }

            #topbar {
                left: 0 !important;
            }

            #main-wrapper {
                margin-left: 0 !important;
            }

            #sidebar-overlay {
                display: none;
            }

            #sidebar-overlay.show {
                display: block;
            }
        }
    </style>
    @stack('styles')
</head>

<body>

    {{-- Sidebar --}}
    <nav id="sidebar">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <i class="fas fa-school"></i>
            </div>
            <div>
                <div class="sidebar-brand-name">Skops</div>
                <div class="sidebar-brand-sub">Skor Perilaku Siswa</div>
            </div>
        </a>

        <div class="sidebar-nav">
            {{-- Dashboard --}}
            <a href="{{ route('dashboard') }}"
                class="nav-item-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-chart-pie"></i></span>
                Dashboard
            </a>

            {{-- Master Data --}}
            <div class="nav-label">Master Data</div>
            <a href="{{ route('master.guru.index') }}"
                class="nav-item-link {{ request()->routeIs('master.guru.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-user-friends"></i></span>
                Guru
            </a>
            <a href="{{ route('master.murid.index') }}"
                class="nav-item-link {{ request()->routeIs('master.murid.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-users"></i></span>
                Siswa
            </a>
            <a href="{{ route('master.walikelas.index') }}"
                class="nav-item-link {{ request()->routeIs('master.walikelas.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-user-plus"></i></span>
                Wali Kelas
            </a>

            <a href="{{ route('master.kelas.index') }}"
                class="nav-item-link {{ request()->routeIs('master.kelas.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-chalkboard-teacher"></i></span>
                Kelas
            </a>
            <a href="{{ route('master.jurusan.index') }}"
                class="nav-item-link {{ request()->routeIs('master.jurusan.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-sitemap"></i></span>
                Jurusan
            </a>
            <a href="{{ route('master.jenis-poin.index') }}"
                class="nav-item-link {{ request()->routeIs('master.jenis-poin.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-list-ol"></i></span>
                Jenis Poin
            </a>

            {{-- Perilaku --}}
            <div class="nav-label">Perilaku</div>
            <a href="{{ route('transaksi.kartu-kontrol.index') }}"
                class="nav-item-link {{ request()->routeIs('transaksi.kartu-kontrol.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-chart-line"></i></span>
                Kartu Kontrol
            </a>

            {{-- Laporan --}}
            <div class="nav-label">Laporan</div>
            <a href="{{ route('laporan.rekapitulasi.index') }}"
                class="nav-item-link {{ request()->routeIs('laporan.rekapitulasi.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-file"></i></span>
                Rekapitulasi Poin
            </a>

            {{-- Seting (Admin/Operator only) --}}
            @if (in_array(session('level'), ['administrator', 'operator']))
                <div class="nav-label">Seting</div>
                <a href="{{ route('seting.index') }}"
                    class="nav-item-link {{ request()->routeIs('seting.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fas fa-school"></i></span>
                    Profil
                </a>
                <a href="{{ route('seting.index') }}"
                    class="nav-item-link {{ request()->routeIs('seting.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fas fa-user"></i></span>
                    User/pengguna
                </a>
            @endif
        </div>

        {{-- Footer User --}}
        <div class="sidebar-footer">
            <div class="sidebar-footer-avatar">
                @if (session('photo'))
                    <img src="{{ asset('storage/' . session('photo')) }}" alt="avatar">
                @else
                    {{ strtoupper(substr(session('namaopr', 'U'), 0, 1)) }}
                @endif
            </div>
            <div class="sidebar-footer-info">
                <div class="sidebar-footer-name">{{ session('namaopr', 'User') }}</div>
                <div class="sidebar-footer-role">{{ ucfirst(session('level', '-')) }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="ms-auto">
                @csrf
                <button type="submit" class="topbar-btn" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </nav>

    {{-- Overlay Mobile --}}
    <div id="sidebar-overlay" onclick="toggleSidebar()"></div>

    {{-- Topbar --}}
    <header id="topbar">
        <button class="btn-sidebar-toggle" onclick="toggleSidebar()" title="Toggle Sidebar">
            <i class="fas fa-bars"></i>
        </button>
        <span class="topbar-title d-none d-md-block">@yield('title', 'Dashboard')</span>

        <div class="topbar-right">
            {{-- Stok Kritis Badge --}}
            @if (session('brgpend') > 0)
                <a href="{{ route('master.barang.index') }}" class="topbar-btn"
                    title="Stok Kritis: {{ session('brgpend') }} item">
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                    <span class="badge-dot"></span>
                </a>
            @endif

            {{-- Dark Mode Toggle --}}
            <button class="topbar-btn" id="theme-toggle" title="Toggle Dark Mode">
                <i class="fas fa-moon" id="theme-icon"></i>
            </button>
        </div>
    </header>

    {{-- Main Wrapper --}}
    <div id="main-wrapper">
        <main id="main-content">
            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3"
                    role="alert"
                    style="border-radius:10px;border:none;background:rgba(34,197,94,.12);color:#16a34a">
                    <i class="fas fa-check-circle"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"
                        style="filter:invert(1)"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3"
                    role="alert"
                    style="border-radius:10px;border:none;background:rgba(239,68,68,.12);color:#dc2626">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"
                        style="filter:invert(1)"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-3"
                    style="border-radius:10px;border:none;background:rgba(239,68,68,.12);color:#dc2626">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Terdapat kesalahan:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close ms-auto position-absolute top-0 end-0 m-2"
                        data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // ── Sidebar Toggle ──
        let sidebarOpen = window.innerWidth >= 992;

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const wrapper = document.getElementById('main-wrapper');
            const topbar = document.getElementById('topbar');
            const overlay = document.getElementById('sidebar-overlay');

            if (window.innerWidth < 992) {
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('show');
            } else {
                sidebarOpen = !sidebarOpen;
                sidebar.classList.toggle('collapsed', !sidebarOpen);
                wrapper.classList.toggle('full-width', !sidebarOpen);
                topbar.classList.toggle('full-width', !sidebarOpen);
            }
        }

        // ── Dark Mode ──
        const root = document.documentElement;
        const themeIcon = document.getElementById('theme-icon');

        function applyTheme(theme) {
            root.setAttribute('data-theme', theme);
            themeIcon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            localStorage.setItem('ss-theme', theme);
        }

        document.getElementById('theme-toggle').addEventListener('click', () => {
            applyTheme(root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
        });

        // Load saved theme
        const saved = localStorage.getItem('ss-theme');
        if (saved) applyTheme(saved);

        // ── Close overlay on resize ──
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 992) {
                document.getElementById('sidebar').classList.remove('mobile-open');
                document.getElementById('sidebar-overlay').classList.remove('show');
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
