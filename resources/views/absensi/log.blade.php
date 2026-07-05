@extends('layouts.app')

@section('title', 'Riwayat Absensi')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-[#00236f]">Riwayat Kehadiran</h1>
            <p class="text-[#64748B] text-sm">Log seluruh data absensi siswa</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('absensi.export.excel', request()->query()) }}" class="bg-[#10B981] text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-[#059669] transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">table_rows</span> Export Excel
            </a>
            <a href="{{ route('absensi.export.pdf', request()->query()) }}" class="bg-[#EF4444] text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-[#DC2626] transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">picture_as_pdf</span> Export PDF
            </a>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl border border-[#E2E8F0] p-4 mb-6">
        <form method="GET" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[180px]">
                <label class="text-xs font-semibold text-[#444651]">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" class="w-full px-4 py-2 rounded-lg border border-[#c5c5d3] text-sm focus:ring-2 focus:ring-[#00236f] outline-none" placeholder="NIS atau Nama...">
            </div>
            <div class="min-w-[140px]">
                <label class="text-xs font-semibold text-[#444651]">Sesi</label>
                <select name="sesi" class="w-full px-4 py-2 rounded-lg border border-[#c5c5d3] text-sm focus:ring-2 focus:ring-[#00236f] outline-none">
                    <option value="">Semua</option>
                    @foreach($sesiList ?? [] as $s)
                    <option value="{{ $s->id }}" {{ request('sesi') == $s->id ? 'selected' : '' }}>{{ $s->nama_sesi }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[130px]">
                <label class="text-xs font-semibold text-[#444651]">Status</label>
                <select name="status" class="w-full px-4 py-2 rounded-lg border border-[#c5c5d3] text-sm focus:ring-2 focus:ring-[#00236f] outline-none">
                    <option value="">Semua</option>
                    <option value="Tepat Waktu" {{ request('status') == 'Tepat Waktu' ? 'selected' : '' }}>Tepat Waktu</option>
                    <option value="Terlambat" {{ request('status') == 'Terlambat' ? 'selected' : '' }}>Terlambat</option>
                </select>
            </div>
            <div class="flex items-end gap-2 pt-[18px]">
                <button type="submit" class="bg-[#00236f] text-white px-6 py-2 rounded-lg text-sm font-semibold hover:bg-[#00236f]/90 transition-all">Filter</button>
                <a href="{{ route('absensi.log') }}" class="px-4 py-2 border border-[#E2E8F0] rounded-lg text-sm hover:bg-[#f2f4f6] transition-all">Reset</a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-[#E2E8F0] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#00236f] text-white">
                        <th class="px-4 py-3 text-xs font-semibold">NO</th>
                        <th class="px-4 py-3 text-xs font-semibold">NIS</th>
                        <th class="px-4 py-3 text-xs font-semibold">Nama Lengkap</th>
                        <th class="px-4 py-3 text-xs font-semibold">Lembaga</th>
                        <th class="px-4 py-3 text-xs font-semibold">Sesi</th>
                        <th class="px-4 py-3 text-xs font-semibold">Jam Masuk</th>
                        <th class="px-4 py-3 text-xs font-semibold">Status</th>
                        <th class="px-4 py-3 text-xs font-semibold">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0]">
                    @forelse($absensi ?? [] as $log)
                    <tr class="hover:bg-[#f2f4f6] transition-colors">
                        <td class="px-4 py-3 text-[#64748B] text-sm">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 font-mono text-sm text-[#00236f]">{{ $log->peserta->nis ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm font-medium">{{ $log->peserta->nama_lengkap ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ ($log->peserta->lembaga ?? '') == 'MA' ? 'bg-[#00236f]/10 text-[#00236f]' : 'bg-[#a53936]/10 text-[#a53936]' }}">
                                {{ $log->peserta->lembaga ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-[#64748B]">{{ $log->sesi->nama_sesi ?? '-' }}</td>
                        <td class="px-4 py-3 font-mono text-sm">{{ $log->jam_masuk ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold {{ ($log->status ?? '') == 'Tepat Waktu' ? 'bg-[#10B981]/10 text-[#10B981]' : 'bg-[#EF4444]/10 text-[#EF4444]' }}">
                                {{ $log->status ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-[#64748B]">
                            {{ $log->created_at ? $log->created_at->format('H:i:s d/m/Y') : '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-[#64748B]">
                            <span class="material-symbols-outlined text-4xl block mb-2 text-[#c5c5d3]">inbox</span>
                            Belum ada data kehadiran
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 border-t border-[#E2E8F0] bg-[#f7f9fb]">
            {{ isset($absensi) ? $absensi->withQueryString()->links() : '' }}
        </div>
    </div>
</div>
@endsection