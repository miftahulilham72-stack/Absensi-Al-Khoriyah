<div class="flex flex-col h-full p-4">
    <!-- Logo -->
    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-[#E2E8F0]">
        <div class="w-10 h-10 bg-[#00236f] rounded-xl flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-white">school</span>
        </div>
        <div>
            <h2 class="font-bold text-[#00236f] text-sm">Al-Khoeriyah</h2>
            <p class="text-[10px] text-[#64748B] uppercase tracking-wider">SISTEM ABSENSI</p>
        </div>
    </div>

    <!-- Menu -->
    <nav class="flex-1 space-y-1">
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

        <!-- ===== Pemisah Menu Admin ===== -->
        <div class="border-t border-[#E2E8F0] my-2"></div>
        <p class="text-[9px] text-[#94a3b8] uppercase tracking-wider px-3 py-1 font-semibold">Admin</p>

        <!-- ===== RESET DATA ===== -->
        <a href="{{ route('reset.data') }}" 
           class="menu-item {{ request()->routeIs('reset.data*') ? 'active' : '' }}" 
           style="color:#dc2626; border-left: 3px solid #dc2626;">
            <span class="material-symbols-outlined icon" style="color:#dc2626;">delete_forever</span> 
            Reset Data
            <span class="badge-label" style="background:#dc2626;">ADMIN</span>
        </a>

        <!-- ===== RESET PASSWORD ===== -->
        <a href="{{ route('reset.password') }}" 
           class="menu-item {{ request()->routeIs('reset.password*') ? 'active' : '' }}" 
           style="color:#00236f; border-left: 3px solid #00236f;">
            <span class="material-symbols-outlined icon" style="color:#00236f;">lock_reset</span> 
            Reset Password
            <span class="badge-label" style="background:#00236f;">ADMIN</span>
        </a>
    </nav>

    <!-- Footer -->
    <div class="mt-auto pt-4 border-t border-[#E2E8F0]">
        <!-- ===== LOGOUT DI SIDEBAR ===== -->
        <form method="POST" action="{{ route('logout') }}" class="mb-2">
            @csrf
            <button type="submit" class="menu-item w-full text-left" style="color:#dc2626; border-left: 3px solid #dc2626;">
                <span class="material-symbols-outlined icon" style="color:#dc2626;">logout</span> 
                Keluar
            </button>
        </form>

        <a href="#" class="menu-item text-[#64748B] hover:bg-[#e6e8ea]">
            <span class="material-symbols-outlined icon text-sm">help</span> Bantuan
        </a>
        <div class="px-4 py-2 text-[10px] text-[#64748B] text-center">
            v1.0.0 | Al-Khoeriyah © 2026
        </div>
    </div>
</div>

<style>
    /* ===== MENU ITEM STYLES ===== */
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
        transition: all 0.15s ease;
        width: 100%;
        border: none;
        background: transparent;
        cursor: pointer;
    }
    .menu-item:hover {
        background: #f1f5f9;
    }
    .menu-item.active {
        background: #1e293b;
        color: #ffffff;
    }
    .menu-item .icon {
        font-size: 18px;
        flex-shrink: 0;
    }
    .menu-item.badge-admin {
        border-left: 3px solid #dc2626;
        color: #dc2626;
    }
    .menu-item.badge-admin.active {
        background: #dc2626;
        color: #ffffff;
    }
    .badge-label {
        font-size: 8px;
        background: #dc2626;
        color: #ffffff;
        padding: 1px 8px;
        border-radius: 20px;
        margin-left: auto;
        text-transform: uppercase;
        font-weight: 700;
    }
    .menu-item.active .badge-label {
        background: rgba(255,255,255,0.25);
    }

    /* ===== SCROLLBAR ===== */
    .sidebar::-webkit-scrollbar {
        width: 4px;
    }
    .sidebar::-webkit-scrollbar-thumb {
        background: #c5c5d3;
        border-radius: 10px;
    }
</style>