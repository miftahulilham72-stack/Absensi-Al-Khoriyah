<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rekap Absensi</title>
    <style>
        body { font-family: 'Arial', sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #00236f; color: white; padding: 10px; text-align: left; }
        td { padding: 8px 10px; border-bottom: 1px solid #ddd; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #00236f; }
        .badge-tepat { background: #10B981; color: white; padding: 2px 10px; border-radius: 20px; font-size: 11px; }
        .badge-terlambat { background: #EF4444; color: white; padding: 2px 10px; border-radius: 20px; font-size: 11px; }
        .footer { margin-top: 30px; text-align: center; color: #64748B; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>📋 Rekap Absensi Al-Khoeriyah</h1>
        <p>{{ date('d/m/Y H:i:s') }}</p>
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
            </tr>
        </thead>
        <tbody>
            @foreach($absensi as $i => $log)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $log->peserta->nis }}</td>
                <td>{{ $log->peserta->nama_lengkap }}</td>
                <td>{{ $log->peserta->lembaga }}</td>
                <td>{{ $log->sesi->nama_sesi }}</td>
                <td>{{ $log->jam_masuk }}</td>
                <td><span class="badge-{{ $log->status == 'Tepat Waktu' ? 'tepat' : 'terlambat' }}">{{ $log->status }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak dari Sistem Absensi Al-Khoeriyah | {{ date('d/m/Y') }}
    </div>
</body>
</html>