@extends('layouts.app')

@section('title', 'Manajemen Sesi')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-[#00236f]">Manajemen Sesi Acara</h1>
            <p class="text-[#64748B] text-sm">Tambahkan, edit nama, atau ubah batas waktu toleransi absen</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Tambah Sesi -->
        <div class="bg-white rounded-xl border border-[#E2E8F0] p-6 shadow-sm">
            <h3 class="font-semibold text-[#00236f] mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined">add_task</span> Buat Sesi Baru
            </h3>

            @if(session('success'))
                <div class="bg-[#10B981]/10 text-[#10B981] p-3 rounded-lg text-sm mb-4">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('sesi.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="text-sm font-semibold text-[#444651]">Nama Sesi</label>
                    <input type="text" name="nama_sesi" class="w-full px-4 py-2.5 rounded-lg border border-[#c5c5d3] focus:ring-2 focus:ring-[#00236f] outline-none text-sm" placeholder="Contoh: Pembukaan Matsama" required>
                </div>
                
                <!-- ===== JAM MULAI (BARU!) ===== -->
                <div>
                    <label class="text-sm font-semibold text-[#444651]">Jam Mulai Sesi</label>
                    <input type="time" name="jam_mulai" class="w-full px-4 py-2.5 rounded-lg border border-[#c5c5d3] focus:ring-2 focus:ring-[#00236f] outline-none text-sm" required>
                    <p class="text-xs text-[#64748B] mt-1">Waktu sesi dimulai</p>
                </div>
                
                <!-- ===== BATAS WAKTU ===== -->
                <div>
                    <label class="text-sm font-semibold text-[#444651]">Batas Toleransi Absen</label>
                    <input type="time" name="batas_waktu" class="w-full px-4 py-2.5 rounded-lg border border-[#c5c5d3] focus:ring-2 focus:ring-[#00236f] outline-none text-sm" required>
                    <p class="text-xs text-[#64748B] mt-1">+3 menit toleransi setelah batas waktu</p>
                </div>
                
                <div>
                    <label class="text-sm font-semibold text-[#444651]">Peruntukan</label>
                    <select name="peruntukan" class="w-full px-4 py-2.5 rounded-lg border border-[#c5c5d3] focus:ring-2 focus:ring-[#00236f] outline-none text-sm">
                        <option value="Semua">Semua</option>
                        <option value="MTs">MTs</option>
                        <option value="MA">MA</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-[#a53936] text-white py-3 rounded-lg font-semibold text-sm hover:bg-[#852221] transition-all">
                    SIMPAN SESI
                </button>
            </form>
        </div>

        <!-- Daftar Sesi -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-[#E2E8F0] shadow-sm overflow-hidden">
            <div class="px-6 py-3 border-b border-[#E2E8F0] flex items-center justify-between">
                <h3 class="font-semibold text-[#00236f] flex items-center gap-2">
                    <span class="material-symbols-outlined">list_alt</span> Daftar Sesi
                </h3>
                <span class="text-sm text-[#64748B]">Total: {{ isset($sesi) ? $sesi->count() : 0 }} sesi</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#00236f] text-white">
                            <th class="px-4 py-3 text-xs font-semibold">Nama Sesi</th>
                            <th class="px-4 py-3 text-xs font-semibold">Jam Mulai</th>
                            <th class="px-4 py-3 text-xs font-semibold">Batas Waktu</th>
                            <th class="px-4 py-3 text-xs font-semibold">Peruntukan</th>
                            <th class="px-4 py-3 text-xs font-semibold">Status</th>
                            <th class="px-4 py-3 text-xs font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E2E8F0]">
                        @forelse($sesi ?? [] as $s)
                        <tr class="hover:bg-[#f2f4f6] transition-colors">
                            <td class="px-4 py-3 text-sm font-medium">{{ $s->nama_sesi }}</td>
                            <td class="px-4 py-3 font-mono text-sm">{{ $s->jam_mulai ?? '-' }}</td>
                            <td class="px-4 py-3 font-mono text-sm">{{ $s->batas_waktu }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $s->peruntukan == 'Semua' ? 'bg-[#00236f]/10 text-[#00236f]' : ($s->peruntukan == 'MA' ? 'bg-[#a53936]/10 text-[#a53936]' : 'bg-[#F59E0B]/10 text-[#F59E0B]') }}">
                                    {{ $s->peruntukan }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $s->is_active ? 'bg-[#10B981]/10 text-[#10B981]' : 'bg-[#e6e8ea] text-[#64748B]' }}">
                                    {{ $s->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-1">
                                    <button onclick="toggleSesi({{ $s->id }})" class="p-1.5 rounded {{ $s->is_active ? 'bg-[#10B981]/20 text-[#10B981]' : 'bg-[#e6e8ea] text-[#64748B] hover:bg-[#00236f]/20 hover:text-[#00236f]' }} transition-all">
                                        <span class="material-symbols-outlined text-sm">{{ $s->is_active ? 'pause' : 'play_arrow' }}</span>
                                    </button>
                                    <button onclick="editSesi({{ $s->id }})" class="p-1.5 rounded text-[#00236f] hover:bg-[#00236f]/10 transition-all">
                                        <span class="material-symbols-outlined text-sm">edit</span>
                                    </button>
                                    <button onclick="hapusSesi({{ $s->id }})" class="p-1.5 rounded text-[#EF4444] hover:bg-[#EF4444]/10 transition-all">
                                        <span class="material-symbols-outlined text-sm">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-[#64748B]">
                                <span class="material-symbols-outlined text-4xl block mb-2 text-[#c5c5d3]">event</span>
                                Belum ada sesi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleSesi(id) {
        fetch(`/sesi/${id}/toggle-active`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(res => res.json()).then(data => { if(data.success) location.reload(); });
    }
    function hapusSesi(id) {
        if(confirm('Yakin ingin menghapus sesi ini?')) {
            fetch(`/sesi/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(res => res.json()).then(data => { if(data.success) location.reload(); });
        }
    }
    function editSesi(id) {
        alert('Fitur edit sesi akan segera hadir');
    }
</script>
@endpush
@endsection