@extends('layouts.app')

@section('title', 'Detail Siswa')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl border border-[#E2E8F0] p-6 shadow-sm">
        <div class="flex items-center gap-3 mb-6">
            <span class="material-symbols-outlined text-[#00236f] text-3xl">person</span>
            <div>
                <h1 class="text-2xl font-bold text-[#00236f]">Detail Siswa</h1>
                <p class="text-[#64748B] text-sm">Informasi lengkap data peserta</p>
            </div>
        </div>

        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-[#f8fafc] p-4 rounded-xl">
                    <p class="text-xs text-[#64748B] font-semibold">NIS</p>
                    <p class="text-lg font-mono text-[#00236f] font-bold">{{ $peserta->nis }}</p>
                </div>
                <div class="bg-[#f8fafc] p-4 rounded-xl">
                    <p class="text-xs text-[#64748B] font-semibold">Lembaga</p>
                    <p class="text-lg font-bold">
                        <span class="px-3 py-1 rounded-full text-sm font-bold {{ $peserta->lembaga == 'MA' ? 'bg-[#00236f]/10 text-[#00236f]' : 'bg-[#a53936]/10 text-[#a53936]' }}">
                            {{ $peserta->lembaga }}
                        </span>
                    </p>
                </div>
            </div>

            <div class="bg-[#f8fafc] p-4 rounded-xl">
                <p class="text-xs text-[#64748B] font-semibold">Nama Lengkap</p>
                <p class="text-lg font-bold text-[#0f172a]">{{ $peserta->nama_lengkap }}</p>
            </div>

            <div class="bg-[#f8fafc] p-4 rounded-xl">
                <p class="text-xs text-[#64748B] font-semibold">Gugus/Kelompok</p>
                <p class="text-lg">{{ $peserta->gugus ?? '-' }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="bg-[#f8fafc] p-4 rounded-xl">
                    <p class="text-xs text-[#64748B] font-semibold">Dibuat</p>
                    <p class="text-sm">{{ $peserta->created_at ? $peserta->created_at->format('d/m/Y H:i') : '-' }}</p>
                </div>
                <div class="bg-[#f8fafc] p-4 rounded-xl">
                    <p class="text-xs text-[#64748B] font-semibold">Diperbarui</p>
                    <p class="text-sm">{{ $peserta->updated_at ? $peserta->updated_at->format('d/m/Y H:i') : '-' }}</p>
                </div>
            </div>
        </div>

        <div class="flex gap-3 pt-6 border-t border-[#E2E8F0] mt-4">
            <a href="{{ route('peserta.index') }}" 
               class="flex-1 px-4 py-3 border border-[#E2E8F0] rounded-xl font-semibold text-center hover:bg-[#f2f4f6] transition-all">
                ← Kembali
            </a>
            <a href="{{ route('peserta.edit', $peserta->id) }}" 
               class="flex-1 bg-[#00236f] text-white py-3 rounded-xl font-semibold text-center hover:bg-[#00236f]/90 transition-all">
                ✏️ Edit Data
            </a>
        </div>
    </div>
</div>
@endsection