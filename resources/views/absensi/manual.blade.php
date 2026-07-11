@extends('layouts.app')

@section('title', 'Absensi Manual')

@section('content')
<div style="max-width:1200px;margin:0 auto;">

    <!-- Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
        <div>
            <h1 style="font-size:20px;font-weight:700;color:#0f172a;display:flex;align-items:center;gap:8px;">
                <span class="material-symbols-outlined" style="font-size:24px;">edit_note</span> Absensi Manual
            </h1>
            <p style="color:#64748b;font-size:13px;">Panitia/Admin dapat mengabsensi peserta yang tidak sempat absen digital</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <button onclick="simpanSemua()" id="btnSimpanSemua" class="btn-sm" style="background:#10b981;color:#fff;padding:8px 16px;border:none;border-radius:8px;cursor:pointer;display:flex;align-items:center;gap:6px;font-weight:600;font-size:13px;transition:all 0.15s;">
                <span class="material-symbols-outlined" style="font-size:16px;">save</span> Simpan Semua
            </button>
            <a href="{{ route('absensi.log') }}" class="btn-sm" style="background:#1e293b;color:#fff;padding:8px 16px;border-radius:8px;text-decoration:none;display:flex;align-items:center;gap:6px;font-weight:600;font-size:13px;transition:all 0.15s;">
                <span class="material-symbols-outlined" style="font-size:16px;">list_alt</span> Lihat Riwayat
            </a>
        </div>
    </div>

    @if(session('success'))
        <div style="background:#10b98115;border:1px solid #10b981;border-radius:8px;padding:12px 16px;margin-bottom:16px;color:#10b981;font-weight:600;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background:#ef444415;border:1px solid #ef4444;border-radius:8px;padding:12px 16px;margin-bottom:16px;color:#ef4444;font-weight:600;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Info Sesi -->
    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:14px 18px;margin-bottom:16px;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:8px;">
        <div style="display:flex;align-items:center;gap:12px;">
            <span class="material-symbols-outlined" style="color:#1e293b;font-size:28px;">event_available</span>
            <div>
                <p style="font-size:10px;font-weight:700;color:#1e293b;text-transform:uppercase;letter-spacing:0.5px;">Sesi Aktif</p>
                <p style="font-weight:700;color:#0f172a;">
                    @if($sesiAktif)
                        {{ $sesiAktif->nama_sesi }}
                        <span style="font-size:13px;font-weight:400;color:#64748b;margin-left:8px;">(Batas: {{ $sesiAktif->batas_waktu }} WIB)</span>
                    @else
                        <span style="color:#dc2626;">Tidak ada sesi aktif!</span>
                    @endif
                </p>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:6px;">
            <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:{{ $sesiAktif ? '#10b981' : '#dc2626' }};{{ $sesiAktif ? 'animation:pulse 2s infinite;' : '' }}"></span>
            <span style="font-size:13px;color:#64748b;">{{ $sesiAktif ? 'Aktif' : 'Tidak Aktif' }}</span>
        </div>
    </div>

    <!-- Filter -->
    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:10px;padding:14px 16px;margin-bottom:16px;">
        <form method="GET" action="{{ route('absensi.manual') }}" style="display:flex;flex-wrap:wrap;align-items:flex-end;gap:12px;">
            <div style="flex:1;min-width:160px;">
                <label style="font-size:11px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;background:#f8fafc;transition:border 0.15s;"
                       placeholder="NIS atau Nama..."
                       onfocus="this.style.borderColor='#1e293b'"
                       onblur="this.style.borderColor='#e2e8f0'">
            </div>

            <div style="min-width:130px;">
                <label style="font-size:11px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">Lembaga</label>
                <select name="lembaga" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;background:#f8fafc;cursor:pointer;transition:border 0.15s;" onfocus="this.style.borderColor='#1e293b'" onblur="this.style.borderColor='#e2e8f0'">
                    <option value="">Semua</option>
                    <option value="MTs" {{ request('lembaga') == 'MTs' ? 'selected' : '' }}>MTs</option>
                    <option value="MA" {{ request('lembaga') == 'MA' ? 'selected' : '' }}>MA</option>
                </select>
            </div>

            <div style="min-width:130px;">
                <label style="font-size:11px;font-weight:600;color:#64748b;display:block;margin-bottom:4px;">Filter Status</label>
                <select name="status_filter" style="width:100%;padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;background:#f8fafc;cursor:pointer;transition:border 0.15s;" onfocus="this.style.borderColor='#1e293b'" onblur="this.style.borderColor='#e2e8f0'">
                    <option value="semua" {{ request('status_filter') == 'semua' ? 'selected' : '' }}>Semua</option>
                    <option value="belum" {{ request('status_filter') == 'belum' ? 'selected' : '' }}>Belum Absen</option>
                    <option value="sudah" {{ request('status_filter') == 'sudah' ? 'selected' : '' }}>Sudah Absen</option>
                </select>
            </div>

            <div style="display:flex;gap:6px;padding-bottom:1px;">
                <button type="submit" style="background:#1e293b;color:#fff;padding:8px 20px;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;transition:background 0.15s;">
                    Filter
                </button>
                <a href="{{ route('absensi.manual') }}" style="padding:8px 16px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#64748b;text-decoration:none;background:#fff;transition:all 0.15s;">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Statistik -->
    <div style="display:flex;flex-wrap:wrap;gap:16px;margin-bottom:16px;font-size:14px;">
        <span style="display:flex;align-items:center;gap:6px;background:#f8fafc;padding:4px 14px;border-radius:20px;border:1px solid #e2e8f0;">
            <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#10b981;"></span>
            Hadir: <strong style="color:#10b981;">{{ $statistik['hadir'] ?? 0 }}</strong>
        </span>
        <span style="display:flex;align-items:center;gap:6px;background:#f8fafc;padding:4px 14px;border-radius:20px;border:1px solid #e2e8f0;">
            <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#f59e0b;"></span>
            Sakit: <strong style="color:#f59e0b;">{{ $statistik['sakit'] ?? 0 }}</strong>
        </span>
        <span style="display:flex;align-items:center;gap:6px;background:#f8fafc;padding:4px 14px;border-radius:20px;border:1px solid #e2e8f0;">
            <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#3b82f6;"></span>
            Izin: <strong style="color:#3b82f6;">{{ $statistik['izin'] ?? 0 }}</strong>
        </span>
        <span style="display:flex;align-items:center;gap:6px;background:#f8fafc;padding:4px 14px;border-radius:20px;border:1px solid #e2e8f0;">
            <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#ef4444;"></span>
            Alpa: <strong style="color:#ef4444;">{{ $statistik['alpa'] ?? 0 }}</strong>
        </span>
    </div>

    <!-- Tabel -->
    <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="background:#1e293b;color:#fff;">
                        <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;">No</th>
                        <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;">NIS</th>
                        <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;">Nama Lengkap</th>
                        <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;">Lembaga</th>
                        <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:600;">Status</th>
                        <th style="padding:10px 14px;text-align:center;font-size:11px;font-weight:600;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($peserta ?? [] as $item)
                    @php 
                        $absen = $item->absensi_manual->first();
                    @endphp
                    <tr style="border-bottom:1px solid #e2e8f0;transition:background 0.15s;" 
                        onmouseover="this.style.background='#f8fafc'" 
                        onmouseout="this.style.background=''"
                        data-peserta-id="{{ $item->id }}">
                        <td style="padding:10px 14px;color:#94a3b8;font-size:13px;">{{ $loop->iteration }}</td>
                        <td style="padding:10px 14px;font-family:monospace;color:#1e293b;font-size:13px;">{{ $item->nis }}</td>
                        <td style="padding:10px 14px;font-weight:500;font-size:13px;">{{ $item->nama_lengkap }}</td>
                        <td style="padding:10px 14px;">
                            <span style="font-size:10px;padding:2px 12px;border-radius:20px;font-weight:700;background:{{ $item->lembaga == 'MA' ? '#1e293b15' : '#dc262615' }};color:{{ $item->lembaga == 'MA' ? '#1e293b' : '#dc2626' }};">
                                {{ $item->lembaga }}
                            </span>
                        </td>
                        <td style="padding:10px 14px;">
                            @if($absen)
                                <span style="font-size:10px;padding:3px 14px;border-radius:20px;font-weight:700;background:{{ $absen->keterangan == 'Hadir' ? '#10b98115' : ($absen->keterangan == 'Sakit' ? '#f59e0b15' : ($absen->keterangan == 'Izin' ? '#3b82f615' : '#ef444415')) }};color:{{ $absen->keterangan == 'Hadir' ? '#10b981' : ($absen->keterangan == 'Sakit' ? '#f59e0b' : ($absen->keterangan == 'Izin' ? '#3b82f6' : '#ef4444')) }};">
                                    {{ $absen->keterangan }}
                                </span>
                            @else
                                <span style="color:#94a3b8;font-size:13px;">Belum Absen</span>
                            @endif
                        </td>
                        <td style="padding:10px 14px;text-align:center;">
                            <div style="display:flex;gap:4px;justify-content:center;flex-wrap:wrap;">
                                <button onclick="setKeterangan({{ $item->id }}, 'Hadir')" 
                                        style="padding:4px 12px;border:none;border-radius:6px;font-size:10px;font-weight:600;cursor:pointer;transition:all 0.15s;background:#10b98115;color:#10b981;">
                                    ✅ Hadir
                                </button>
                                <button onclick="setKeterangan({{ $item->id }}, 'Sakit')" 
                                        style="padding:4px 12px;border:none;border-radius:6px;font-size:10px;font-weight:600;cursor:pointer;transition:all 0.15s;background:#f59e0b15;color:#f59e0b;">
                                    🤒 Sakit
                                </button>
                                <button onclick="setKeterangan({{ $item->id }}, 'Izin')" 
                                        style="padding:4px 12px;border:none;border-radius:6px;font-size:10px;font-weight:600;cursor:pointer;transition:all 0.15s;background:#3b82f615;color:#3b82f6;">
                                    📝 Izin
                                </button>
                                <button onclick="setKeterangan({{ $item->id }}, 'Alpa')" 
                                        style="padding:4px 12px;border:none;border-radius:6px;font-size:10px;font-weight:600;cursor:pointer;transition:all 0.15s;background:#ef444415;color:#ef4444;">
                                    ❌ Alpa
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="padding:40px;text-align:center;color:#94a3b8;">
                            <span class="material-symbols-outlined" style="font-size:36px;display:block;margin-bottom:6px;color:#cbd5e1;">group</span>
                            Belum ada peserta terdaftar
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:10px 16px;border-top:1px solid #e2e8f0;background:#f8fafc;">
            {{ isset($peserta) ? $peserta->withQueryString()->links() : '' }}
        </div>
    </div>

    <!-- Catatan -->
    <div style="margin-top:16px;padding:14px 18px;background:#fef3c7;border:1px solid #f59e0b30;border-radius:10px;">
        <div style="display:flex;gap:10px;">
            <span class="material-symbols-outlined" style="color:#f59e0b;">info</span>
            <div style="font-size:13px;color:#92400e;">
                <p style="font-weight:600;">📌 Catatan:</p>
                <ul style="list-style:disc;padding-left:20px;margin-top:4px;space-y:1px;">
                    <li>Absensi manual untuk peserta yang <strong>tidak sempat absen digital</strong></li>
                    <li>Data tercatat sebagai <strong>"Absen Manual"</strong> dengan nama pengabsensi</li>
                    <li>Klik <strong>"Simpan Semua"</strong> untuk menyimpan perubahan</li>
                    <li>Gunakan filter <strong>Lembaga</strong> untuk memisahkan MTs dan MA</li>
                </ul>
            </div>
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
    console.log('✅ ===== ABSENSI MANUAL JS LOADED =====');
    console.log('📌 Versi: 2026-07-09 FINAL');

    let changes = {};

    function setKeterangan(pesertaId, keterangan) {
        console.log('🟢 Klik tombol:', keterangan, 'untuk ID:', pesertaId);
        
        // Simpan ke changes
        changes[pesertaId] = keterangan;
        
        console.log('📦 Changes:', changes);
        console.log('📦 JSON:', JSON.stringify(changes));
        
        // Update tampilan tombol
        const row = document.querySelector(`tr[data-peserta-id="${pesertaId}"]`);
        if (!row) {
            console.error('❌ Row tidak ditemukan!');
            return;
        }

        // Update semua tombol di row
        row.querySelectorAll('button').forEach(btn => {
            const text = btn.textContent.trim();
            let key = 'Hadir';
            if (text.includes('Sakit')) key = 'Sakit';
            else if (text.includes('Izin')) key = 'Izin';
            else if (text.includes('Alpa')) key = 'Alpa';
            else if (text.includes('Hadir')) key = 'Hadir';
            
            const colors = {
                'Hadir': { bg: '#10b981', hover: '#10b98115', text: '#10b981' },
                'Sakit': { bg: '#f59e0b', hover: '#f59e0b15', text: '#f59e0b' },
                'Izin': { bg: '#3b82f6', hover: '#3b82f615', text: '#3b82f6' },
                'Alpa': { bg: '#ef4444', hover: '#ef444415', text: '#ef4444' }
            };

            if (key === keterangan) {
                btn.style.background = colors[key].bg;
                btn.style.color = '#ffffff';
            } else {
                btn.style.background = colors[key].hover;
                btn.style.color = colors[key].text;
            }
        });

        // Update status cell
        const statusCell = row.querySelector('td:nth-child(5)');
        const colorMap = {
            'Hadir': 'background:#10b98115;color:#10b981;',
            'Sakit': 'background:#f59e0b15;color:#f59e0b;',
            'Izin': 'background:#3b82f615;color:#3b82f6;',
            'Alpa': 'background:#ef444415;color:#ef4444;'
        };
        statusCell.innerHTML = `<span style="font-size:10px;padding:3px 14px;border-radius:20px;font-weight:700;${colorMap[keterangan]}">${keterangan}</span>`;
        
        // Feedback visual
        row.style.transition = 'background 0.2s';
        row.style.background = '#f0fdf4';
        setTimeout(() => { row.style.background = ''; }, 500);
    }

    function simpanSemua() {
        console.log('🟢 SIMPAN SEMUA diklik!');
        console.log('📦 Data changes:', changes);
        console.log('📦 JSON:', JSON.stringify(changes));
        
        // Cek data
        if (typeof changes !== 'object' || Object.keys(changes).length === 0) {
            alert('Tidak ada perubahan yang disimpan.');
            return;
        }

        // Validasi
        const validKeys = ['Hadir', 'Sakit', 'Izin', 'Alpa'];
        let hasError = false;
        for (let key in changes) {
            if (!validKeys.includes(changes[key])) {
                console.error('❌ Invalid:', key, changes[key]);
                hasError = true;
            }
        }
        if (hasError) {
            alert('❌ Ada data yang tidak valid!');
            return;
        }

        // Cek apakah ada Sakit/Izin/Alpa
        let hasNonHadir = false;
        for (let key in changes) {
            if (changes[key] !== 'Hadir') {
                hasNonHadir = true;
                console.log('✅ Ditemukan:', key, '→', changes[key]);
            }
        }
        if (!hasNonHadir) {
            console.warn('⚠️ Tidak ada Sakit/Izin/Alpa!');
        }

        if (!confirm(`Yakin menyimpan ${Object.keys(changes).length} data?`)) {
            return;
        }

        const btn = document.getElementById('btnSimpanSemua');
        btn.disabled = true;
        btn.innerHTML = '⏳ Menyimpan...';

        // ===== KIRIM KE SERVER =====
        fetch('{{ route("absensi.manual.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ changes: changes })
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error('Server: ' + text.substring(0, 200));
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Response:', data);
            if (data.success) {
                alert('✅ ' + data.message);
                location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Error: ' + error.message);
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '💾 Simpan Semua';
        });
    }

    console.log('✅ setKeterangan terdefinisi:', typeof setKeterangan === 'function');
    console.log('✅ simpanSemua terdefinisi:', typeof simpanSemua === 'function');
    console.log('🔍 Cek tombol di tabel...');
</script>
@endpush
@endsection