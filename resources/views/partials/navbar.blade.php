<nav class="bg-[#00236f] text-white h-16 fixed top-0 left-0 right-0 z-50 shadow-md flex items-center justify-between px-4 md:px-6">
    <div class="flex items-center gap-3">
        <button onclick="toggleSidebar()" class="menu-toggle-btn md:hidden text-white hover:bg-white/10 p-2 rounded-lg transition-all">
            <span class="material-symbols-outlined text-2xl">menu</span>
        </button>

        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-white/10 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-xl">school</span>
            </div>
            <span class="font-bold text-lg hidden sm:block">Al-Khoeriyah Bantarsari</span>
        </div>
    </div>

    <div class="flex items-center gap-2">
        <div class="hidden lg:flex items-center bg-white/10 rounded-lg px-3 py-1.5 relative">
            <form id="search-form" action="{{ route('peserta.index') }}" method="GET" class="flex items-center">
                <span class="material-symbols-outlined text-sm text-white/60 mr-2">search</span>
                <input type="text" name="search" id="global-search" placeholder="Cari NIS atau Nama..." value="{{ request('search') }}" class="bg-transparent border-none outline-none text-white placeholder:text-white/40 text-sm w-32 xl:w-48">
            </form>
        </div>

        <div class="relative">
            <button id="notif-btn" onclick="toggleNotifications()" class="text-white/80 hover:text-white hover:bg-white/10 p-2 rounded-full transition-all relative">
                <span class="material-symbols-outlined">notifications</span>
                <span id="notif-badge" class="absolute top-1.5 right-1.5 min-w-[14px] h-[14px] px-1 bg-red-500 rounded-full text-[9px] font-bold hidden items-center justify-center">0</span>
            </button>
            <div id="notif-dropdown" class="hidden absolute right-0 top-10 w-80 bg-white border border-slate-200 rounded-xl shadow-xl z-[100] max-h-96 overflow-y-auto">
                <div class="px-4 py-3 border-b border-slate-200 font-semibold text-sm text-slate-800">Notifikasi</div>
                <div id="notif-list">
                    <div class="px-4 py-3 text-sm text-slate-500 text-center">Belum ada notifikasi</div>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 border-l border-white/20 pl-3">
            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold flex-shrink-0">
                A
            </div>
            <span class="text-sm font-medium hidden sm:block">Admin</span>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="text-white/80 hover:text-white hover:bg-white/10 p-2 rounded-full transition-all">
                <span class="material-symbols-outlined">logout</span>
            </button>
        </form>
    </div>
</nav>