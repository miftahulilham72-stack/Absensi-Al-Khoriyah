<nav class="navbar" style="position:fixed;top:0;left:0;right:0;height:60px;background:#1e293b;z-index:40;display:flex;align-items:center;justify-content:space-between;padding:0 12px;box-shadow:0 1px 3px rgba(0,0,0,0.1);">
    <!-- KIRI -->
    <div style="display:flex; align-items:center; gap:8px;">
        <button class="menu-toggle" onclick="toggleSidebar()" style="background:none;border:none;color:#fff;cursor:pointer;font-size:24px;padding:6px 8px;display:block;">
            ☰
        </button>
        <span class="brand" style="font-size:15px;font-weight:700;color:#fff;display:flex;align-items:center;gap:6px;">
            <span class="hidden sm:inline">Al-Khoeriyah</span>
            <span class="sm:hidden">Absensi</span>
        </span>
    </div>

    <!-- KANAN -->
    <div style="display:flex; align-items:center; gap:2px;">
        <!-- Search Icon (Mobile) -->
        <button class="lg:hidden text-white/80 hover:text-white hover:bg-white/10 p-2 rounded-full transition-all" style="min-width:40px;min-height:40px;">
            <span class="material-symbols-outlined" style="font-size:22px;">search</span>
        </button>

        <!-- Notifications -->
        <button class="text-white/80 hover:text-white hover:bg-white/10 p-2 rounded-full transition-all relative" style="min-width:40px;min-height:40px;">
            <span class="material-symbols-outlined" style="font-size:22px;">notifications</span>
        </button>

        <!-- Admin -->
        <div class="admin hidden sm:flex items-center gap-2 border-l border-white/20 pl-3">
            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold">A</div>
            <span class="text-sm font-medium hidden md:block">Admin</span>
        </div>

        <!-- ===== LOGOUT - DIPERBAIKI ===== -->
        <form method="POST" action="{{ route('logout') }}" class="inline" id="logout-form">
            @csrf
            <button type="submit" 
                    class="text-white/80 hover:text-white hover:bg-white/10 rounded-full transition-all active:bg-white/20"
                    style="min-width:44px;min-height:44px;display:flex;align-items:center;justify-content:center;padding:0 8px;"
                    id="logout-btn">
                <span class="material-symbols-outlined" style="font-size:24px;">logout</span>
            </button>
        </form>
    </div>
</nav>

<!-- Floating Logout untuk HP -->
@if(Auth::check())
<div class="md:hidden fixed bottom-6 right-6 z-50">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" 
                class="bg-[#dc2626] text-white w-14 h-14 rounded-full shadow-lg flex items-center justify-center hover:bg-[#b91c1c] transition-all active:scale-95"
                style="box-shadow: 0 4px 20px rgba(220,38,38,0.4);">
            <span class="material-symbols-outlined" style="font-size:28px;">logout</span>
        </button>
    </form>
</div>
@endif