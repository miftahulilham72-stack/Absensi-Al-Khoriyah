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

        <!-- ===== TAMBAHKAN INI: RESET DATA ===== -->
        <a href="{{ route('reset.data') }}" 
           class="menu-item {{ request()->routeIs('reset.data*') ? 'active' : '' }}" 
           style="color:#dc2626; border-left: 3px solid #dc2626; margin-top: 8px; border-top: 1px solid #e2e8f0; padding-top: 12px;">
            <span class="material-symbols-outlined icon" style="color:#dc2626;">delete_forever</span> 
            Reset Data
            <span class="badge-label" style="background:#dc2626;">ADMIN</span>
        </a>
    </nav>

    <!-- Footer -->
    <div class="mt-auto pt-4 border-t border-[#E2E8F0]">
        <a href="#" class="menu-item text-[#64748B] hover:bg-[#e6e8ea]">
            <span class="material-symbols-outlined icon text-sm">help</span> Bantuan
        </a>
        <div class="px-4 py-2 text-[10px] text-[#64748B] text-center">
            v1.0.0 | Al-Khoeriyah © 2026
        </div>
    </div>
</div>