@extends('layouts.app')

@section('title', 'Tambah Siswa')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl border border-[#E2E8F0] p-6 shadow-sm">
        <div class="flex items-center gap-3 mb-6">
            <span class="material-symbols-outlined text-[#00236f] text-3xl">person_add</span>
            <div>
                <h1 class="text-2xl font-bold text-[#00236f]">Tambah Siswa</h1>
                <p class="text-[#64748B] text-sm">Tambahkan data peserta baru ke sistem</p>
            </div>
        </div>

        <form id="create-form" method="POST" action="{{ route('peserta.store') }}">
            @csrf

            <div class="space-y-4">
                <!-- NIS -->
                <div>
                    <label class="text-sm font-semibold text-[#444651]">NIS</label>
                    <input type="text" name="nis" value="{{ old('nis') }}" 
                           class="w-full px-4 py-3 rounded-xl border border-[#c5c5d3] bg-white focus:ring-2 focus:ring-[#00236f] outline-none transition-all" 
                           placeholder="Masukkan NIS" required>
                    @error('nis')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label class="text-sm font-semibold text-[#444651]">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" 
                           class="w-full px-4 py-3 rounded-xl border border-[#c5c5d3] bg-white focus:ring-2 focus:ring-[#00236f] outline-none transition-all" 
                           placeholder="Masukkan nama lengkap" required>
                    @error('nama_lengkap')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Lembaga -->
                <div>
                    <label class="text-sm font-semibold text-[#444651]">Lembaga</label>
                    <select name="lembaga" 
                            class="w-full px-4 py-3 rounded-xl border border-[#c5c5d3] bg-white focus:ring-2 focus:ring-[#00236f] outline-none transition-all" 
                            required>
                        <option value="MTs" {{ old('lembaga') == 'MTs' ? 'selected' : '' }}>MTs</option>
                        <option value="MA" {{ old('lembaga') == 'MA' ? 'selected' : '' }}>MA</option>
                    </select>
                    @error('lembaga')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Gugus -->
                <div>
                    <label class="text-sm font-semibold text-[#444651]">Gugus/Kelompok</label>
                    <input type="text" name="gugus" value="{{ old('gugus') }}" 
                           class="w-full px-4 py-3 rounded-xl border border-[#c5c5d3] bg-white focus:ring-2 focus:ring-[#00236f] outline-none transition-all" 
                           placeholder="Contoh: Kelompok Al-Fatih">
                    @error('gugus')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-4">
                    <a href="{{ route('peserta.index') }}" 
                       class="flex-1 px-4 py-3 border border-[#E2E8F0] rounded-xl font-semibold text-center hover:bg-[#f2f4f6] transition-all">
                        Batal
                    </a>
                    <button type="submit" 
                            class="flex-1 bg-[#a53936] text-white py-3 rounded-xl font-semibold hover:bg-[#852221] transition-all active:scale-95">
                        💾 Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('create-form').addEventListener('submit', function(e) {
        const btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '⏳ Menyimpan...';
    });
</script>
@endpush
@endsection