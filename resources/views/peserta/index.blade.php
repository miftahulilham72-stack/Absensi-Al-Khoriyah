@extends('layouts.app')

@section('title', 'Manajemen Siswa')

@section('content')
<div style="max-width:1200px;margin:0 auto;">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
        <div>
            <h1 style="font-size:20px;font-weight:700;color:#0f172a;">Manajemen Data Siswa</h1>
            <p style="color:#64748b;font-size:13px;">Kelola registrasi siswa dan monitoring basis data peserta</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('peserta.import.form') }}" class="btn-sm" style="background:#1e293b;color:#fff;">📥 Import Excel</a>
            <a href="{{ route('peserta.export.template') }}" class="btn-sm" style="background:#f59e0b;color:#fff;">📄 Template</a>
        </div>
    </div>

    @if(request('search'))
    <div style="background:#e0f2fe;border:1px solid #38bdf8;border-radius:8px;padding:8px 14px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;gap:8px;">
        <span style="font-size:13px;color:#0369a1;">
            🔍 Hasil pencarian untuk: <strong>"{{ request('search') }}"</strong>
        </span>
        <a href="{{ route('peserta.index') }}" style="font-size:12px;color:#0369a1;text-decoration:underline;">Hapus filter</a>
    </div>
    @endif

    <div class="card">
        <div style="padding:10px 16px;border-bottom:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
            <span style="font-size:13px;color:#64748b;">Total: <strong style="color:#0f172a;">{{ $total ?? 0 }}</strong> siswa</span>
            <div style="position:relative;">
                <span class="material-symbols-outlined" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);font-size:16px;color:#94a3b8;">search</span>
                <input type="text" id="search-siswa" style="padding:6px 12px 6px 34px;border:1px solid #e2e8f0;border-radius:20px;font-size:13px;outline:none;width:200px;background:#f8fafc;" placeholder="Cari NIS atau Nama...">
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="background:#1e293b;color:#fff;">
                        <th style="padding:8px 12px;text-align:left;font-size:11px;">NIS</th>
                        <th style="padding:8px 12px;text-align:left;font-size:11px;">Nama Lengkap</th>
                        <th style="padding:8px 12px;text-align:left;font-size:11px;">Lembaga</th>
                        <th style="padding:8px 12px;text-align:left;font-size:11px;">Gugus</th>
                        <th style="padding:8px 12px;text-align:center;font-size:11px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    @forelse($peserta ?? [] as $p)
                    <tr style="border-bottom:1px solid #e2e8f0;">
                        <td style="padding:8px 12px;font-family:monospace;color:#1e293b;">{{ $p->nis }}</td>
                        <td style="padding:8px 12px;font-weight:500;">{{ $p->nama_lengkap }}</td>
                        <td style="padding:8px 12px;">
                            <span style="font-size:10px;padding:2px 10px;border-radius:20px;font-weight:700;background:{{ $p->lembaga == 'MA' ? '#1e293b15' : '#dc262615' }};color:{{ $p->lembaga == 'MA' ? '#1e293b' : '#dc2626' }};">
                                {{ $p->lembaga }}
                            </span>
                        </td>
                        <td style="padding:8px 12px;color:#64748b;">{{ $p->gugus ?? '-' }}</td>
                        <td style="padding:8px 12px;text-align:center;">
                            <button onclick="editSiswa({{ $p->id }})" style="background:none;border:none;color:#1e293b;cursor:pointer;padding:4px 6px;">✏️</button>
                            <button onclick="hapusSiswa({{ $p->id }})" style="background:none;border:none;color:#ef4444;cursor:pointer;padding:4px 6px;">🗑️</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding:32px;text-align:center;color:#94a3b8;">Belum ada data siswa</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:10px 16px;border-top:1px solid #e2e8f0;background:#f8fafc;">
            {{ isset($peserta) ? $peserta->links() : '' }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('search-siswa')?.addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#table-body tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>
@endpush
@endsection