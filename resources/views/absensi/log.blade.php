@extends('layouts.app')

@section('title', 'Riwayat Absensi')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-[#00236f]">Riwayat Kehadiran</h1>
            <p class="text-[#64748B] text-sm">Log seluruh data absensi siswa</p>
        </div>
        <div class="flex gap-3 flex-wrap">
            <!-- Export Excel -->
            <a href="{{ route('absensi.export.excel', request()->query()) }}" class="bg-[#10B981] text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-[#059669] transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">table_rows</span> Export Excel
            </a>
            <!-- Export PDF -->
            <a href="{{ route('absensi.export.pdf', request()->query()) }}" class="bg-[#EF4444] text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-[#DC2626] transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">picture_as_pdf</span> Export PDF
            </a>
            <!-- ===== EXPORT WORD (.docx) ===== -->
            <a href="{{ route('absensi.export.word', array_merge(request()->query(), ['jenjang' => 'mts'])) }}" class="bg-[#3B82F6] text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-[#2563EB] transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">description</span> Export Word
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
                    <option value="Terlambat (Toleransi)" {{ request('status') == 'Terlambat (Toleransi)' ? 'selected' : '' }}>Terlambat (Toleransi)</option>
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
                        <th class="px-4 py-3 text-xs font-semibold">KODE</th>
                        <th class="px-4 py-3 text-xs font-semibold">NAMA LENGKAP</th>
                        <th class="px-4 py-3 text-xs font-semibold">NIS</th>
                        <th class="px-4 py-3 text-xs font-semibold">LEMBAGA</th>
                        <th class="px-4 py-3 text-xs font-semibold">SESI</th>
                        <th class="px-4 py-3 text-xs font-semibold">JAM MASUK</th>
                        <th class="px-4 py-3 text-xs font-semibold">STATUS</th>
                        <th class="px-4 py-3 text-xs font-semibold">KETERANGAN</th>
                        <th class="px-4 py-3 text-xs font-semibold text-center">TTD</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0]">
                    @forelse($absensi ?? [] as $log)
                    <tr class="hover:bg-[#f2f4f6] transition-colors">
                        <td class="px-4 py-3 text-[#64748B] text-sm">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 font-mono text-sm font-bold text-[#00236f]">{{ $log->sesi->kode_sesi ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm font-medium">{{ $log->peserta->nama_lengkap ?? '-' }}</td>
                        <td class="px-4 py-3 font-mono text-sm text-[#00236f]">{{ $log->peserta->nis ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ ($log->peserta->lembaga ?? '') == 'MA' ? 'bg-[#00236f]/10 text-[#00236f]' : 'bg-[#a53936]/10 text-[#a53936]' }}">
                                {{ $log->peserta->lembaga ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-[#64748B]">{{ $log->sesi->nama_sesi ?? '-' }}</td>
                        <td class="px-4 py-3 font-mono text-sm">{{ $log->jam_masuk ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @php
                                $statusClass = match($log->status ?? '') {
                                    'Tepat Waktu' => 'bg-[#10b98115] text-[#10b981]',
                                    'Terlambat (Toleransi)' => 'bg-[#f59e0b15] text-[#f59e0b]',
                                    'Terlambat' => 'bg-[#ef444415] text-[#ef4444]',
                                    default => 'bg-[#e2e8f0] text-[#94a3b8]'
                                };
                            @endphp
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold {{ $statusClass }}">
                                {{ $log->status ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($log->keterangan)
                                @php
                                    $ketClass = match($log->keterangan) {
                                        'Hadir' => 'bg-[#10b98115] text-[#10b981]',
                                        'Sakit' => 'bg-[#f59e0b15] text-[#f59e0b]',
                                        'Izin' => 'bg-[#3b82f615] text-[#3b82f6]',
                                        'Alpa' => 'bg-[#ef444415] text-[#ef4444]',
                                        default => 'bg-[#e2e8f0] text-[#94a3b8]'
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold {{ $ketClass }}">
                                    {{ $log->keterangan }}
                                </span>
                            @else
                                <span class="text-[#94a3b8] text-sm">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($log->ttd_image && $log->ttd_image != 'manual_absensi')
                                <img src="{{ asset($log->ttd_image) }}" 
                                     style="width:50px;height:25px;object-fit:contain;border:1px solid #e2e8f0;border-radius:4px;cursor:pointer;transition:transform 0.2s;"
                                     onmouseover="this.style.transform='scale(2.5)';this.style.zIndex='10';this.style.position='relative';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.2)';"
                                     onmouseout="this.style.transform='scale(1)';this.style.zIndex='auto';this.style.position='static';this.style.boxShadow='none';"
                                     onclick="showTtd('{{ asset($log->ttd_image) }}', '{{ $log->peserta->nama_lengkap ?? 'TTD' }}')"
                                     alt="TTD {{ $log->peserta->nama_lengkap ?? 'TTD' }}">
                            @else
                                <span style="font-size:10px;color:#94a3b8;background:#f1f5f9;padding:2px 8px;border-radius:4px;">Manual</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-8 text-center text-[#64748B]">
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

<!-- MODAL ZOOM TTD -->
<div id="modalTtd" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);z-index:1000;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:16px;padding:24px;max-width:420px;width:100%;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,0.3);" onclick="event.stopPropagation();">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
            <h3 style="font-size:16px;font-weight:700;color:#0f172a;" id="ttdNama">✍️ Tanda Tangan</h3>
            <button onclick="closeTtd()" style="background:none;border:none;font-size:24px;cursor:pointer;color:#94a3b8;padding:0 8px;">✕</button>
        </div>
        <div style="border:2px solid #e2e8f0;border-radius:8px;padding:16px;background:#f8fafc;">
            <img id="ttdImage" src="" style="width:100%;max-height:300px;object-fit:contain;">
        </div>
        <p style="font-size:11px;color:#94a3b8;margin-top:8px;">Klik di luar gambar atau tekan ESC untuk menutup</p>
    </div>
</div>

<script>
    function showTtd(src, nama) {
        document.getElementById('ttdImage').src = src;
        document.getElementById('ttdNama').textContent = '✍️ ' + nama;
        document.getElementById('modalTtd').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeTtd() {
        document.getElementById('modalTtd').style.display = 'none';
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeTtd();
    });

    document.getElementById('modalTtd').addEventListener('click', function(e) {
        if (e.target === this) closeTtd();
    });
</script>
@endsection