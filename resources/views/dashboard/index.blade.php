@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div style="max-width:1200px;margin:0 auto;">
    <!-- Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
        <div>
            <h1 style="font-size:22px;font-weight:700;color:#0f172a;">Pemantauan Real-Time</h1>
            <p style="color:#64748b;font-size:14px;">Monitoring kehadiran harian siswa secara langsung.</p>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <a href="{{ route('absensi.export.excel') }}" style="background:#10b981;color:#fff;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;border:none;cursor:pointer;transition:background 0.15s;">
                <span class="material-symbols-outlined" style="font-size:18px;">table_rows</span> Export Excel
            </a>
            <a href="{{ route('absensi.export.pdf') }}" style="background:#ef4444;color:#fff;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;border:none;cursor:pointer;transition:background 0.15s;">
                <span class="material-symbols-outlined" style="font-size:18px;">picture_as_pdf</span> Export PDF
            </a>
        </div>
    </div>

    <!-- ===== SESI AKTIF CARD ===== -->
    <div style="background:#ffffff;border-radius:10px;border:1px solid #e2e8f0;padding:16px 20px;margin-bottom:20px;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <div style="display:flex;align-items:center;gap:12px;">
            <div style="width:44px;height:44px;background:#1e293b10;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <span class="material-symbols-outlined" style="color:#1e293b;font-size:28px;">event_available</span>
            </div>
            <div>
                <p style="font-size:11px;font-weight:700;color:#1e293b;text-transform:uppercase;letter-spacing:0.3px;">Sesi Aktif</p>
                @if($sesiAktif)
                    <p style="font-weight:700;color:#0f172a;font-size:16px;">
                        {{ $sesiAktif->nama_sesi }}
                        <span style="font-size:13px;font-weight:400;color:#64748b;margin-left:8px;">
                            ({{ $sesiAktif->jam_mulai ?? '-' }} - {{ $sesiAktif->batas_waktu }} WIB)
                        </span>
                    </p>
                @else
                    <p style="color:#dc2626;font-weight:600;">Tidak ada sesi aktif</p>
                @endif
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:12px;">
            @if($sesiAktif)
                <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#10b981;animation:pulse 2s infinite;"></span>
                <span style="font-size:13px;color:#10b981;font-weight:600;">Aktif</span>
                <span style="font-size:13px;color:#64748b;margin-left:4px;">
                    ⏱️ Sisa: <strong id="sesiCountdown">--:--</strong>
                </span>
            @else
                <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#dc2626;"></span>
                <span style="font-size:13px;color:#dc2626;font-weight:600;">Tidak Aktif</span>
            @endif
        </div>
    </div>

    <!-- KPI Cards -->
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:20px;">
        <!-- Card 1: HADIR -->
        <div style="background:#ffffff;border-radius:10px;border:1px solid #e2e8f0;padding:16px 18px;border-top:4px solid #10b981;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                <span style="color:#64748b;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.3px;">HARI INI</span>
                <span style="font-size:9px;color:#10b981;background:#10b98115;padding:2px 10px;border-radius:20px;font-weight:700;">HADIR</span>
            </div>
            <div style="display:flex;align-items:baseline;gap:6px;">
                <span style="font-size:28px;font-weight:700;color:#0f172a;">{{ $hadir ?? 0 }}</span>
                <span style="color:#64748b;font-size:13px;">/ {{ $totalPeserta ?? 0 }} Siswa</span>
            </div>
            <div style="margin-top:8px;background:#e2e8f0;height:4px;border-radius:4px;overflow:hidden;">
                <div style="background:#10b981;height:100%;border-radius:4px;width:{{ $totalPeserta > 0 ? ($hadir/$totalPeserta)*100 : 0 }}%;transition:width 0.5s;"></div>
            </div>
        </div>

        <!-- Card 2: BELUM HADIR -->
        <div style="background:#ffffff;border-radius:10px;border:1px solid #e2e8f0;padding:16px 18px;border-top:4px solid #f59e0b;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                <span style="color:#64748b;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.3px;">HARI INI</span>
                <span style="font-size:9px;color:#f59e0b;background:#f59e0b15;padding:2px 10px;border-radius:20px;font-weight:700;">BELUM HADIR</span>
            </div>
            <div style="display:flex;align-items:baseline;gap:6px;">
                <span style="font-size:28px;font-weight:700;color:#0f172a;">{{ $belumHadir ?? 0 }}</span>
                <span style="color:#64748b;font-size:13px;">Belum Scan</span>
            </div>
            <div style="margin-top:8px;background:#e2e8f0;height:4px;border-radius:4px;overflow:hidden;">
                <div style="background:#f59e0b;height:100%;border-radius:4px;width:{{ $totalPeserta > 0 ? ($belumHadir/$totalPeserta)*100 : 0 }}%;transition:width 0.5s;"></div>
            </div>
        </div>

        <!-- Card 3: TERLAMBAT -->
        <div style="background:#ffffff;border-radius:10px;border:1px solid #e2e8f0;padding:16px 18px;border-top:4px solid #ef4444;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                <span style="color:#64748b;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.3px;">HARI INI</span>
                <span style="font-size:9px;color:#ef4444;background:#ef444415;padding:2px 10px;border-radius:20px;font-weight:700;">TERLAMBAT</span>
            </div>
            <div style="display:flex;align-items:baseline;gap:6px;">
                <span style="font-size:28px;font-weight:700;color:#0f172a;">{{ $terlambat ?? 0 }}</span>
                <span style="color:#64748b;font-size:13px;">Siswa</span>
            </div>
            <div style="margin-top:8px;background:#e2e8f0;height:4px;border-radius:4px;overflow:hidden;">
                <div style="background:#ef4444;height:100%;border-radius:4px;width:{{ $totalPeserta > 0 ? ($terlambat/$totalPeserta)*100 : 0 }}%;transition:width 0.5s;"></div>
            </div>
        </div>
    </div>

    <!-- Log Table -->
    <div style="background:#ffffff;border-radius:10px;border:1px solid #e2e8f0;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <div style="padding:12px 18px;border-bottom:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
            <span style="font-weight:600;font-size:14px;color:#0f172a;display:flex;align-items:center;gap:6px;">
                <span class="material-symbols-outlined" style="font-size:18px;">list_alt</span> Log Riwayat Kehadiran Terkini
            </span>
            <span style="font-size:10px;color:#94a3b8;">🔄 Update otomatis setiap 30 detik</span>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="background:#1e293b;color:#ffffff;">
                        <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;">NO</th>
                        <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;">NAMA LENGKAP</th>
                        <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;">NIS</th>
                        <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;">LEMBAGA</th>
                        <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;">SESI</th>
                        <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;">JAM MASUK</th>
                        <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs ?? [] as $log)
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:10px 14px;color:#94a3b8;">{{ $loop->iteration }}</td>
                        <td style="padding:10px 14px;font-weight:500;">{{ $log->peserta->nama_lengkap ?? '-' }}</td>
                        <td style="padding:10px 14px;font-family:monospace;color:#1e293b;">{{ $log->peserta->nis ?? '-' }}</td>
                        <td style="padding:10px 14px;">
                            <span style="font-size:9px;padding:2px 12px;border-radius:20px;font-weight:700;background:{{ ($log->peserta->lembaga ?? '') == 'MA' ? '#1e293b15' : '#dc262615' }};color:{{ ($log->peserta->lembaga ?? '') == 'MA' ? '#1e293b' : '#dc2626' }};">
                                {{ $log->peserta->lembaga ?? '-' }}
                            </span>
                        </td>
                        <td style="padding:10px 14px;color:#64748b;">{{ $log->sesi->nama_sesi ?? '-' }}</td>
                        <td style="padding:10px 14px;font-family:monospace;">{{ $log->jam_masuk ?? '-' }}</td>
                        <td style="padding:10px 14px;">
                            @php
                                $statusClass = match($log->status ?? '') {
                                    'Tepat Waktu' => 'bg-[#10b98115] text-[#10b981]',
                                    'Terlambat (Toleransi)' => 'bg-[#f59e0b15] text-[#f59e0b]',
                                    'Terlambat' => 'bg-[#ef444415] text-[#ef4444]',
                                    default => 'bg-[#e2e8f0] text-[#94a3b8]'
                                };
                            @endphp
                            <span style="font-size:9px;padding:2px 14px;border-radius:20px;font-weight:700;{{ $statusClass }};">
                                {{ $log->status ?? '-' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="padding:40px;text-align:center;color:#94a3b8;">
                            <span class="material-symbols-outlined" style="font-size:36px;display:block;margin-bottom:6px;color:#cbd5e1;">inbox</span>
                            Belum ada data kehadiran
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.3; }
    }
</style>

@push('scripts')
<script>
    // ================================================================
    // COUNTDOWN SESSION (Sisa Waktu)
    // ================================================================
    function updateCountdown() {
        @if($sesiAktif)
            const now = new Date();
            const [hours, minutes, seconds] = '{{ $sesiAktif->batas_waktu }}'.split(':').map(Number);
            const endTime = new Date(now);
            endTime.setHours(hours, minutes, seconds, 0);
            
            // Jika waktu sudah lewat, tambah 1 hari
            if (now > endTime) {
                endTime.setDate(endTime.getDate() + 1);
            }
            
            const diff = Math.floor((endTime - now) / 1000);
            if (diff > 0) {
                const h = Math.floor(diff / 3600);
                const m = Math.floor((diff % 3600) / 60);
                const s = diff % 60;
                document.getElementById('sesiCountdown').textContent = 
                    String(h).padStart(2, '0') + ':' + 
                    String(m).padStart(2, '0') + ':' + 
                    String(s).padStart(2, '0');
            } else {
                document.getElementById('sesiCountdown').textContent = '00:00:00';
            }
        @endif
    }

    // Update setiap 1 detik
    updateCountdown();
    setInterval(updateCountdown, 1000);

    // ================================================================
    // AUTO REFRESH LOG (Setiap 30 detik)
    // ================================================================
    function refreshLog() {
        fetch(window.location.href)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTable = doc.querySelector('tbody');
                if (newTable) {
                    document.querySelector('tbody').innerHTML = newTable.innerHTML;
                }
            })
            .catch(() => {});
    }

    // Refresh setiap 30 detik
    setInterval(refreshLog, 30000);
</script>
@endpush
@endsection