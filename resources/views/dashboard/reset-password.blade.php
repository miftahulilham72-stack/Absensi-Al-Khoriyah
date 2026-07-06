@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-xl border border-[#E2E8F0] p-6 shadow-sm">
        <div class="flex items-center gap-3 mb-6">
            <span class="material-symbols-outlined text-[#00236f] text-4xl">lock_reset</span>
            <div>
                <h1 class="text-2xl font-bold text-[#00236f]">Reset Password Admin</h1>
                <p class="text-[#64748B] text-sm">Ubah password akun admin</p>
            </div>
        </div>

        @if(session('error'))
            <div class="bg-[#ffdad6] text-[#93000a] p-3 rounded-lg mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="bg-[#10B981]/10 text-[#10B981] p-3 rounded-lg mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('reset.password.process') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-semibold text-[#444651]">Password Lama</label>
                    <input type="password" name="old_password" 
                           class="w-full px-4 py-3 rounded-xl border border-[#c5c5d3] bg-white focus:ring-2 focus:ring-[#00236f] outline-none transition-all"
                           placeholder="Masukkan password lama" required>
                </div>
                <div>
                    <label class="text-sm font-semibold text-[#444651]">Password Baru</label>
                    <input type="password" name="new_password" 
                           class="w-full px-4 py-3 rounded-xl border border-[#c5c5d3] bg-white focus:ring-2 focus:ring-[#00236f] outline-none transition-all"
                           placeholder="Masukkan password baru (min 4 karakter)" required>
                </div>
                <div>
                    <label class="text-sm font-semibold text-[#444651]">Konfirmasi Password Baru</label>
                    <input type="password" name="new_password_confirmation" 
                           class="w-full px-4 py-3 rounded-xl border border-[#c5c5d3] bg-white focus:ring-2 focus:ring-[#00236f] outline-none transition-all"
                           placeholder="Konfirmasi password baru" required>
                </div>
                <div class="flex gap-3 pt-2">
                    <a href="{{ route('dashboard') }}" 
                       class="flex-1 px-4 py-3 border border-[#E2E8F0] rounded-xl font-semibold text-center hover:bg-[#f2f4f6] transition-all">
                        Batal
                    </a>
                    <button type="submit" 
                            class="flex-1 bg-[#00236f] text-white py-3 rounded-xl font-semibold hover:bg-[#00236f]/90 transition-all active:scale-95">
                        💾 Simpan Password
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection