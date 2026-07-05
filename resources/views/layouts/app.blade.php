<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Al-Khoeriyah Attendance')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            color: #0f172a;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        
        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed;
            top: 60px;
            left: 0;
            bottom: 0;
            width: 240px;
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            z-index: 30;
            padding: 16px 12px;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 14px;
            margin-bottom: 14px;
            border-bottom: 1px solid #e2e8f0;
        }
        .sidebar-logo .icon {
            width: 36px;
            height: 36px;
            background: #1e293b;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .sidebar-logo .icon span { color: #fff; font-size: 20px; }
        .sidebar-logo .text h3 { font-size: 13px; font-weight: 700; color: #1e293b; }
        .sidebar-logo .text p { font-size: 9px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.3px; }
        
        .menu-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 6px;
            color: #475569;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.15s;
            width: 100%;
            border: none;
            background: transparent;
            cursor: pointer;
        }
        .menu-item:hover { background: #f1f5f9; }
        .menu-item.active { background: #1e293b; color: #ffffff; }
        .menu-item .icon { font-size: 18px; flex-shrink: 0; }
        .menu-item.badge-admin { border-left: 3px solid #dc2626; color: #dc2626; }
        .menu-item.badge-admin.active { background: #dc2626; color: #fff; }
        .badge-label {
            font-size: 8px;
            background: #dc2626;
            color: #fff;
            padding: 1px 8px;
            border-radius: 20px;
            margin-left: auto;
            text-transform: uppercase;
            font-weight: 700;
        }
        .menu-item.active .badge-label { background: rgba(255,255,255,0.25); }
        
        .sidebar-footer {
            margin-top: auto;
            padding-top: 14px;
            border-top: 1px solid #e2e8f0;
        }
        .sidebar-footer .version {
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            padding-top: 6px;
        }
        
        /* ===== NAVBAR ===== */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: #1e293b;
            z-index: 40;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .navbar .brand { color: #fff; font-size: 16px; font-weight: 700; }
        .navbar .right { display: flex; align-items: center; gap: 8px; }
        .navbar .right .admin {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #fff;
            font-size: 13px;
            border-left: 1px solid rgba(255,255,255,0.15);
            padding-left: 12px;
        }
        .navbar .right .admin .avatar {
            width: 30px;
            height: 30px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
        }
        .navbar .right button {
            background: none;
            border: none;
            color: rgba(255,255,255,0.7);
            cursor: pointer;
            padding: 6px;
            border-radius: 6px;
            transition: all 0.15s;
        }
        .navbar .right button:hover { background: rgba(255,255,255,0.1); color: #fff; }
        .menu-toggle { display: none; }
        
        /* ===== MAIN ===== */
        .main-content {
            margin-left: 240px;
            padding: 76px 20px 20px 20px;
            min-height: 100vh;
        }
        
        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
                z-index: 50;
                transition: transform 0.3s ease;
                box-shadow: 4px 0 20px rgba(0,0,0,0.1);
            }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay {
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.3);
                z-index: 45;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.3s ease;
            }
            .sidebar-overlay.open { opacity: 1; pointer-events: all; }
            .main-content { margin-left: 0 !important; }
            .menu-toggle { display: block !important; }
        }
    </style>
</head>
<body>

    <!-- Overlay -->
    <div id="sidebar-overlay" class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <div class="icon"><span class="material-symbols-outlined">school</span></div>
            <div class="text">
                <h3>Al-Khoeriyah</h3>
                <p>SISTEM ABSENSI</p>
            </div>
        </div>
        <nav style="flex:1; display:flex; flex-direction:column; gap:2px;">
            <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="material-symbols-outlined icon">dashboard</span> Dashboard
            </a>
            <a href="{{ route('peserta.index') }}" class="menu-item {{ request()->routeIs('peserta.*') ? 'active' : '' }}">
                <span class="material-symbols-outlined icon">group</span> Manajemen Siswa
            </a>
            <a href="{{ route('sesi.index') }}" class="menu-item {{ request()->routeIs('sesi.*') ? 'active' : '' }}">
                <span class="material-symbols-outlined icon">event_repeat</span> Manajemen Sesi
            </a>
            <a href="{{ route('absensi.log') }}" class="menu-item {{ request()->routeIs('absensi.log') ? 'active' : '' }}">
                <span class="material-symbols-outlined icon">list_alt</span> Riwayat Absensi
            </a>
            <a href="{{ route('absensi.form') }}" class="menu-item {{ request()->routeIs('absensi.form') ? 'active' : '' }}">
                <span class="material-symbols-outlined icon">edit_note</span> Form Absensi
            </a>
            <a href="{{ route('absensi.manual') }}" class="menu-item badge-admin {{ request()->routeIs('absensi.manual*') ? 'active' : '' }}">
                <span class="material-symbols-outlined icon">edit_note</span> Absensi Manual
                <span class="badge-label">ADMIN</span>
            </a>
            
            <!-- ===== LOGOUT DI SIDEBAR ===== -->
            <form method="POST" action="{{ route('logout') }}" style="margin-top:8px;border-top:1px solid #e2e8f0;padding-top:8px;">
                @csrf
                <button type="submit" class="menu-item" style="color:#dc2626;width:100%;text-align:left;">
                    <span class="material-symbols-outlined icon" style="font-size:18px;">logout</span> Keluar
                </button>
            </form>
        </nav>
        <div class="sidebar-footer">
            <a href="#" class="menu-item" style="color:#94a3b8;">
                <span class="material-symbols-outlined icon" style="font-size:16px;">help</span> Bantuan
            </a>
            <div class="version">v1.0.0 | Al-Khoeriyah © 2026</div>
        </div>
    </aside>

    <!-- Navbar -->
    @include('partials.navbar')

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        @yield('content')
    </div>

    <!-- ========================================================== -->
    <!-- 🚪 FLOATING LOGOUT BUTTON UNTUK HP (DI SINI!)              -->
    <!-- ========================================================== -->
    @if(Auth::check())
    <div class="md:hidden fixed bottom-6 right-6 z-50">
        <form method="POST" action="{{ route('logout') }}" id="floating-logout-form">
            @csrf
            <button type="submit" 
                    class="bg-[#dc2626] text-white w-14 h-14 rounded-full shadow-lg flex items-center justify-center hover:bg-[#b91c1c] transition-all active:scale-95"
                    style="box-shadow: 0 4px 20px rgba(220,38,38,0.4);"
                    id="floating-logout-btn">
                <span class="material-symbols-outlined" style="font-size:28px;">logout</span>
            </button>
        </form>
    </div>
    @endif

    @stack('scripts')

    <script>
        // ===== TOGGLE SIDEBAR =====
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('open');
        }

        // ===== KONFIRMASI LOGOUT =====
        document.addEventListener('DOMContentLoaded', function() {
            // Semua form logout
            const logoutForms = document.querySelectorAll('form[action*="logout"]');
            
            logoutForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!confirm('Yakin ingin keluar dari sistem?')) {
                        e.preventDefault();
                    }
                });
            });

            // Floating logout button
            const floatingBtn = document.getElementById('floating-logout-btn');
            if (floatingBtn) {
                floatingBtn.addEventListener('click', function(e) {
                    // Confirm sudah di-handle oleh form submit
                });
            }
        });

        // ESC untuk tutup sidebar
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const sidebar = document.getElementById('sidebar');
                if (sidebar.classList.contains('open')) toggleSidebar();
            }
        });

        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebar-overlay');
                sidebar.classList.remove('open');
                overlay.classList.remove('open');
            }
        });
    </script>
</body>
</html>