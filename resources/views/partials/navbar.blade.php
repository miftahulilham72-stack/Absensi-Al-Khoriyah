<nav class="navbar">
    <div style="display:flex; align-items:center; gap:12px;">
        <button class="menu-toggle" onclick="toggleSidebar()" style="background:none;border:none;color:#fff;cursor:pointer;font-size:24px;">☰</button>
        <span class="brand">Al-Khoeriyah</span>
    </div>
    <div class="right">
        <!-- SEARCH -->
        <div style="position:relative;display:flex;align-items:center;">
            <form id="search-form" action="{{ route('peserta.index') }}" method="GET" style="display:flex;align-items:center;">
                <input type="text" name="search" id="global-search" 
                       placeholder="Cari NIS atau Nama..." 
                       style="padding:6px 12px 6px 34px;border:none;border-radius:20px;font-size:13px;outline:none;width:180px;background:rgba(255,255,255,0.12);color:#fff;transition:all 0.3s;"
                       onfocus="this.style.width='220px';this.style.background='rgba(255,255,255,0.2)';"
                       onblur="this.style.width='180px';this.style.background='rgba(255,255,255,0.12)';"
                       value="{{ request('search') }}">
                <span class="material-symbols-outlined" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);font-size:18px;color:rgba(255,255,255,0.5);">search</span>
            </form>
        </div>

        <!-- NOTIFICATIONS -->
        <div style="position:relative;">
            <button id="notif-btn" onclick="toggleNotifications()" style="background:none;border:none;color:rgba(255,255,255,0.7);cursor:pointer;padding:6px;border-radius:6px;transition:all 0.15s;position:relative;">
                <span class="material-symbols-outlined" style="font-size:20px;">notifications</span>
                <span id="notif-badge" style="position:absolute;top:0;right:0;background:#ef4444;color:#fff;font-size:9px;font-weight:700;padding:1px 5px;border-radius:20px;display:none;">0</span>
            </button>
            <!-- Dropdown Notifikasi -->
            <div id="notif-dropdown" style="display:none;position:absolute;right:0;top:40px;width:340px;background:#fff;border-radius:10px;box-shadow:0 10px 40px rgba(0,0,0,0.15);border:1px solid #e2e8f0;z-index:100;max-height:450px;overflow-y:auto;padding:8px 0;">
                <div style="padding:10px 16px;border-bottom:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-weight:600;font-size:14px;color:#0f172a;">Notifikasi</span>
                    <button onclick="markAllRead()" style="background:none;border:none;color:#3b82f6;font-size:12px;font-weight:600;cursor:pointer;padding:4px 8px;border-radius:4px;transition:background 0.15s;" 
                            onmouseover="this.style.background='#eff6ff'" 
                            onmouseout="this.style.background=''">
                        Tandai Semua Dibaca
                    </button>
                </div>
                <div id="notif-list">
                    <div style="padding:12px 16px;text-align:center;color:#94a3b8;font-size:13px;">Belum ada notifikasi</div>
                </div>
                <div id="notif-empty" style="display:none;padding:20px 16px;text-align:center;color:#94a3b8;font-size:13px;">
                    <span class="material-symbols-outlined" style="font-size:32px;display:block;margin-bottom:6px;color:#cbd5e1;">check_circle</span>
                    Semua notifikasi telah dibaca
                </div>
            </div>
        </div>

        <!-- Admin -->
        <div class="admin">
            <div class="avatar">A</div>
            <span>Admin</span>
        </div>

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
            @csrf
            <button type="submit"><span class="material-symbols-outlined" style="font-size:20px;">logout</span></button>
        </form>
    </div>
</nav>