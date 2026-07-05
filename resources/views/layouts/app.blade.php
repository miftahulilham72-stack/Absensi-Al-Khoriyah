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
        
        /* ===== NOTIFIKASI DROPDOWN ===== */
        #notif-dropdown {
            animation: slideDown 0.25s ease;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        #notif-dropdown::-webkit-scrollbar { width: 4px; }
        #notif-dropdown::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
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

    @stack('scripts')

    <script>
        // ===== TOGGLE SIDEBAR =====
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('open');
        }

        // ===== NOTIFICATIONS =====
        let notifCount = 0;
        let notifData = [];

        function toggleNotifications() {
            const dropdown = document.getElementById('notif-dropdown');
            const badge = document.getElementById('notif-badge');
            if (dropdown.style.display === 'block') {
                dropdown.style.display = 'none';
            } else {
                dropdown.style.display = 'block';
                if (badge) {
                    badge.style.display = 'none';
                }
            }
        }

        document.addEventListener('click', function(e) {
            const notifBtn = document.getElementById('notif-btn');
            const dropdown = document.getElementById('notif-dropdown');
            if (notifBtn && dropdown && !notifBtn.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });

        function checkNotifications() {
            fetch('/notifications/count')
                .then(res => res.json())
                .then(data => {
                    notifCount = data.count || 0;
                    notifData = data.notifications || [];

                    const badge = document.getElementById('notif-badge');
                    const list = document.getElementById('notif-list');
                    const empty = document.getElementById('notif-empty');

                    if (notifCount > 0) {
                        if (badge) {
                            badge.textContent = notifCount;
                            badge.style.display = 'block';
                        }

                        let html = '';
                        notifData.forEach(notif => {
                            const bgColor = notif.type === 'danger' ? '#fef2f2' : '#fffbeb';
                            const borderColor = notif.type === 'danger' ? '#fecaca' : '#fde68a';
                            const textColor = notif.type === 'danger' ? '#dc2626' : '#d97706';

                            html += `
                                <div style="padding:10px 16px;border-bottom:1px solid #f1f5f9;font-size:13px;color:#0f172a;cursor:pointer;transition:background 0.15s;background:${bgColor};border-left:3px solid ${borderColor};"
                                     onmouseover="this.style.background='#f8fafc'"
                                     onmouseout="this.style.background='${bgColor}'"
                                     onclick="markAsRead('${notif.id}')">
                                    <div style="font-weight:600;color:${textColor};">${notif.title}</div>
                                    <div style="font-size:12px;color:#475569;margin-top:2px;">${notif.message}</div>
                                    <div style="font-size:10px;color:#94a3b8;margin-top:4px;">${notif.time}</div>
                                </div>
                            `;
                        });

                        if (list) {
                            list.innerHTML = html;
                        }
                        if (empty) {
                            empty.style.display = 'none';
                        }
                    } else {
                        if (badge) {
                            badge.style.display = 'none';
                        }
                        if (list) {
                            list.innerHTML = '';
                        }
                        if (empty) {
                            empty.style.display = 'block';
                        }
                    }
                })
                .catch(() => {});
        }

        function markAsRead(id) {
            fetch(`/notifications/mark-read/${id}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(() => {
                checkNotifications();
            });
        }

        function markAllRead() {
            fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(() => {
                checkNotifications();
                const dropdown = document.getElementById('notif-dropdown');
                if (dropdown) {
                    dropdown.style.display = 'block';
                }
            });
        }

        // ===== SEARCH GLOBAL =====
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('global-search');
            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        document.getElementById('search-form').submit();
                    }
                });
            }

            // Cek notifikasi setiap 30 detik
            checkNotifications();
            setInterval(checkNotifications, 30000);
        });

        // ESC untuk menutup dropdown
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.getElementById('notif-dropdown').style.display = 'none';
                const sidebar = document.getElementById('sidebar');
                if (sidebar.classList.contains('open')) toggleSidebar();
            }
        });
    </script>
</body>
</html>