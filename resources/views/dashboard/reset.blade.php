@extends('layouts.app')

@section('title', 'Reset Data')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-xl border border-[#E2E8F0] p-6 shadow-sm">
        <div class="flex items-center gap-3 mb-6">
            <span class="material-symbols-outlined text-[#dc2626] text-4xl">warning</span>
            <div>
                <h1 class="text-2xl font-bold text-[#dc2626]">Reset Data</h1>
                <p class="text-[#64748B] text-sm">Hapus semua data peserta dan absensi</p>
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

        <div class="bg-[#fef2f2] border border-[#fecaca] rounded-xl p-4 mb-6">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-[#dc2626]">info</span>
                <div>
                    <p class="font-semibold text-[#dc2626] text-sm">⚠️ PERINGATAN!</p>
                    <ul class="text-sm text-[#dc2626] list-disc pl-5 mt-1 space-y-1">
                        <li>Semua data peserta akan dihapus PERMANEN</li>
                        <li>Semua data absensi akan dihapus PERMANEN</li>
                        <li>Data yang dihapus TIDAK BISA DIKEMBALIKAN</li>
                        <li>Password reset: <strong>reset123</strong></li>
                    </ul>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('reset.data.process') }}">
            @csrf
            <div class="mb-4">
                <label class="text-sm font-semibold text-[#444651]">Masukkan Password Reset</label>
                <input type="password" name="password" 
                       class="w-full px-4 py-3 rounded-xl border border-[#c5c5d3] bg-white focus:ring-2 focus:ring-[#dc2626] outline-none transition-all"
                       placeholder="Masukkan password reset" required>
                <p class="text-xs text-[#64748B] mt-1">Password: <strong>reset123</strong></p>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('dashboard') }}" 
                   class="flex-1 px-4 py-3 border border-[#E2E8F0] rounded-xl font-semibold text-center hover:bg-[#f2f4f6] transition-all">
                    Batal
                </a>
                <button type="submit" 
                        class="flex-1 bg-[#dc2626] text-white py-3 rounded-xl font-semibold hover:bg-[#b91c1c] transition-all active:scale-95"
                        onclick="return confirm('⚠️ Yakin ingin menghapus SEMUA data?')">
                    🗑️ Hapus Semua Data
                </button>
            </div>
        </form>
    </div>
</div>
@endsection