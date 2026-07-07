<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rekap Absensi</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #00236f; color: white; padding: 8px; text-align: left; }
        td { padding: 6px 8px; border-bottom: 1px solid #ddd; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { color: #00236f; font-size: 20px; }
        .badge-tepat { background: #10B981; color: white; padding: 2px 10px; border-radius: 20px; font-size: 9px; }
        .badge-toleransi { background: #F59E0B; color: white; padding: 2px 10px; border-radius: 20px; font-size: 9px; }
        .badge-terlambat { background: #EF4444; color: white; padding: 2px 10px; border-radius: 20px; font-size: 9px; }
        .ttd-cell { text-align: center; font-size: 10px; color: #64748b; }
        .ttd-cell img { max-height: 40px; max-width: 80px; object-fit: contain; }
        .footer { margin-top: 20px; text-align: center; color: #64748B; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>📋 Rekap Absensi Al-Khoeriyah</h1>
        <p>{{ date('d/m/Y H:i:s') }} WIB</p>
        <p>Total: {{ $absensi->count() }} data</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama</th>
                <th>Lembaga</th>
                <th>Sesi</th>
                <th>Jam Masuk</th>
                <th>Status</th>
                <th>TTD</th>
            </tr>
        </thead>
        <tbody>
            @foreach($absensi as $i => $log)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $log->peserta->nis ?? '-' }}</td>
                <td>{{ $log->peserta->nama_lengkap ?? '-' }}</td>
                <td>{{ $log->peserta->lembaga ?? '-' }}</td>
                <td>{{ $log->sesi->nama_sesi ?? '-' }}</td>
                <td>{{ $log->jam_masuk ?? '-' }}</td>
                <td>
                    @if($log->status == 'Tepat Waktu')
                        <span class="badge-tepat">Tepat Waktu</span>
                    @elseif($log->status == 'Terlambat (Toleransi)')
                        <span class="badge-toleransi">Terlambat (Toleransi)</span>
                    @else
                        <span class="badge-terlambat">Terlambat</span>
                    @endif
                </td>
                <td class="ttd-cell">
                    @if($log->ttd_image && $log->ttd_image != 'manual_absensi')
                        <img src="{{ public_path($log->ttd_image) }}" alt="TTD">
                    @else
                        Manual
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak dari Sistem Absensi Al-Khoeriyah | {{ date('d/m/Y') }}
    </div>
</body>
</html>