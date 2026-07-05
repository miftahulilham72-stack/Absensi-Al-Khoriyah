@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div style="max-width:1200px;margin:0 auto;">
    <!-- Header -->
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
        <div>
            <h1 style="font-size:22px;font-weight:700;color:#0f172a;">Pemantauan Real-Time</h1>
            <p style="color:#64748b;font-size:14px;">Monitoring kehadiran harian siswa secara langsung.</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('absensi.export.excel') }}" class="btn-sm" style="background:#10b981;color:#fff;">📊 Export Excel</a>
            <a href="{{ route('absensi.export.pdf') }}" class="btn-sm" style="background:#ef4444;color:#fff;">📄 Export PDF</a>
        </div>
    </div>

    <!-- KPI -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:20px;">
        <div class="card" style="padding:16px;border-top:4px solid #10b981;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="color:#64748b;font-size:12px;font-weight:600;">HARI INI</span>
                <span style="font-size:10px;color:#10b981;background:#10b98115;padding:2px 10px;border-radius:20px;font-weight:700;">HADIR</span>
            </div>
            <div style="display:flex;align-items:baseline;gap:6px;margin-top:4px;">
                <span style="font-size:28px;font-weight:700;">{{ $hadir ?? 0 }}</span>
                <span style="color:#64748b;font-size:13px;">/ {{ $totalPeserta ?? 0 }} Siswa</span>
            </div>
            <div style="margin-top:8px;background:#e2e8f0;height:4px;border-radius:4px;overflow:hidden;">
                <div style="background:#10b981;height:100%;border-radius:4px;width:{{ $totalPeserta > 0 ? ($hadir/$totalPeserta)*100 : 0 }}%;"></div>
            </div>
        </div>
        <div class="card" style="padding:16px;border-top:4px solid #f59e0b;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="color:#64748b;font-size:12px;font-weight:600;">HARI INI</span>
                <span style="font-size:10px;color:#f59e0b;background:#f59e0b15;padding:2px 10px;border-radius:20px;font-weight:700;">BELUM HADIR</span>
            </div>
            <div style="display:flex;align-items:baseline;gap:6px;margin-top:4px;">
                <span style="font-size:28px;font-weight:700;">{{ $belumHadir ?? 0 }}</span>
                <span style="color:#64748b;font-size:13px;">Belum Scan</span>
            </div>
            <div style="margin-top:8px;background:#e2e8f0;height:4px;border-radius:4px;overflow:hidden;">
                <div style="background:#f59e0b;height:100%;border-radius:4px;width:{{ $totalPeserta > 0 ? ($belumHadir/$totalPeserta)*100 : 0 }}%;"></div>
            </div>
        </div>
        <div class="card" style="padding:16px;border-top:4px solid #ef4444;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="color:#64748b;font-size:12px;font-weight:600;">HARI INI</span>
                <span style="font-size:10px;color:#ef4444;background:#ef444415;padding:2px 10px;border-radius:20px;font-weight:700;">TERLAMBAT</span>
            </div>
            <div style="display:flex;align-items:baseline;gap:6px;margin-top:4px;">
                <span style="font-size:28px;font-weight:700;">{{ $terlambat ?? 0 }}</span>
                <span style="color:#64748b;font-size:13px;">Siswa</span>
            </div>
            <div style="margin-top:8px;background:#e2e8f0;height:4px;border-radius:4px;overflow:hidden;">
                <div style="background:#ef4444;height:100%;border-radius:4px;width:{{ $totalPeserta > 0 ? ($terlambat/$totalPeserta)*100 : 0 }}%;"></div>
            </div>
        </div>
    </div>

    <!-- Log Table -->
    <div class="card">
        <div style="padding:12px 16px;border-bottom:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
            <span style="font-weight:600;font-size:14px;color:#0f172a;display:flex;align-items:center;gap:6px;">
                <span class="material-symbols-outlined" style="font-size:18px;">list_alt</span> Log Riwayat Kehadiran Terkini
            </span>
            <span style="font-size:10px;color:#94a3b8;">Update otomatis setiap 30 detik</span>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="background:#1e293b;color:#fff;">
                        <th style="padding:8px 12px;text-align:left;font-size:11px;">NO</th>
                        <th style="padding:8px 12px;text-align:left;font-size:11px;">NAMA LENGKAP</th>
                        <th style="padding:8px 12px;text-align:left;font-size:11px;">NIS</th>
                        <th style="padding:8px 12px;text-align:left;font-size:11px;">LEMBAGA</th>
                        <th style="padding:8px 12px;text-align:left;font-size:11px;">SESI</th>
                        <th style="padding:8px 12px;text-align:left;font-size:11px;">JAM MASUK</th>
                        <th style="padding:8px 12px;text-align:left;font-size:11px;">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs ?? [] as $log)
                    <tr style="border-bottom:1px solid #e2e8f0;">
                        <td style="padding:8px 12px;color:#64748b;">{{ $loop->iteration }}</td>
                        <td style="padding:8px 12px;font-weight:500;">{{ $log->peserta->nama_lengkap ?? '-' }}</td>
                        <td style="padding:8px 12px;font-family:monospace;color:#1e293b;">{{ $log->peserta->nis ?? '-' }}</td>
                        <td style="padding:8px 12px;">
                            <span style="font-size:10px;padding:2px 10px;border-radius:20px;font-weight:700;background:{{ ($log->peserta->lembaga ?? '') == 'MA' ? '#1e293b15' : '#dc262615' }};color:{{ ($log->peserta->lembaga ?? '') == 'MA' ? '#1e293b' : '#dc2626' }};">
                                {{ $log->peserta->lembaga ?? '-' }}
                            </span>
                        </td>
                        <td style="padding:8px 12px;color:#64748b;">{{ $log->sesi->nama_sesi ?? '-' }}</td>
                        <td style="padding:8px 12px;font-family:monospace;">{{ $log->jam_masuk ?? '-' }}</td>
                        <td style="padding:8px 12px;">
                            <span style="font-size:10px;padding:2px 12px;border-radius:20px;font-weight:700;background:{{ ($log->status ?? '') == 'Tepat Waktu' ? '#10b98115' : '#ef444415' }};color:{{ ($log->status ?? '') == 'Tepat Waktu' ? '#10b981' : '#ef4444' }};">
                                {{ $log->status ?? '-' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="padding:32px;text-align:center;color:#94a3b8;">
                            <span class="material-symbols-outlined" style="font-size:32px;display:block;margin-bottom:6px;color:#cbd5e1;">inbox</span>
                            Belum ada data kehadiran
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection