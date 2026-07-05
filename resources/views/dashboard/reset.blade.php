@extends('layouts.app')

@section('title', 'Reset Data')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-[#E2E8F0] p-8">
        <!-- Header -->
        <div class="text-center mb-6">
            <div class="w-20 h-20 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-4xl text-red-600">warning</span>
            </div>
            <h1 class="text-2xl font-bold text-[#00236f]">Reset Data Sistem</h1>
            <p class="text-[#64748B] text-sm mt-1">Hapus semua data peserta dan absensi</p>
        </div>

        <!-- Alert -->
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-red-600">dangerous</span>
                <div>
                    <p class="font-bold text-red-700 text-sm">⚠️ PERINGATAN!</p>
                    <ul class="text-sm text-red-600 list-disc pl-5 mt-1 space-y-1">
                        <li>Semua data <strong>peserta</strong> akan dihapus permanen</li>
                        <li>Semua data <strong>absensi</strong> akan dihapus permanen</li>
                        <li>Tindakan ini <strong>TIDAK BISA DIURUNGKAN!</strong></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('reset.data.process') }}" class="space-y-4">
            @csrf

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded-xl text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div>
                <label class="block text-sm font-semibold text-[#444651] mb-1">
                    🔒 Masukkan Password untuk Konfirmasi
                </label>
                <input type="password" name="password" 
                       class="w-full px-4 py-3 rounded-xl border border-[#c5c5d3] focus:ring-2 focus:ring-red-500 outline-none transition-all"
                       placeholder="Masukkan password reset" required>
                <p class="text-xs text-[#64748B] mt-1">Password default: <strong>reset123</strong></p>
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('dashboard') }}" 
                   class="flex-1 px-4 py-3 border border-[#E2E8F0] rounded-xl text-center font-semibold text-[#64748B] hover:bg-[#f2f4f6] transition-all">
                    ❌ Batal
                </a>
                <button type="submit" 
                        class="flex-1 bg-red-600 text-white py-3 rounded-xl font-semibold hover:bg-red-700 transition-all active:scale-95">
                    🗑️ Hapus Semua Data
                </button>
            </div>
        </form>
    </div>

    <!-- Informasi Tambahan -->
    <div class="mt-4 text-center">
        <p class="text-xs text-[#94a3b8]">
            Password dapat diubah di file <strong>DashboardController.php</strong>
        </p>
    </div>
</div>
@endsection