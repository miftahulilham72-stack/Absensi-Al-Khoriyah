@extends('layouts.app')

@section('title', 'Manajemen Siswa')

@section('content')
<div style="max-width:1200px;margin:0 auto;">
    <!-- Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
        <div>
            <h1 style="font-size:20px;font-weight:700;color:#0f172a;">Manajemen Data Siswa</h1>
            <p style="color:#64748b;font-size:13px;">Kelola registrasi siswa dan monitoring basis data peserta</p>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <a href="{{ route('peserta.import.form') }}" style="background:#1e293b;color:#fff;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;border:none;cursor:pointer;transition:all 0.15s;">
                📥 Import Excel
            </a>
            <a href="{{ route('peserta.export.template') }}" style="background:#f59e0b;color:#fff;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px;border:none;cursor:pointer;transition:all 0.15s;">
                📄 Template
            </a>
            <button onclick="bukaModalTambah()" style="background:#10b981;color:#fff;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:6px;transition:all 0.15s;">
                ➕ Tambah Peserta
            </button>
            <button onclick="hapusSemuaData()" id="btnHapusSemua" style="background:#dc2626;color:#fff;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:6px;transition:all 0.15s;">
                🗑️ Hapus Semua Data
            </button>
        </div>
    </div>

    <!-- Search Result -->
    @if(request('search') || request('lembaga'))
        <div style="background:#e0f2fe;border:1px solid #38bdf8;border-radius:8px;padding:8px 14px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
            <span style="font-size:13px;color:#0369a1;">
                🔍 Filter aktif: 
                @if(request('search'))
                    <strong>"{{ request('search') }}"</strong>
                @endif
                @if(request('lembaga'))
                    <span style="background:#1e293b;color:#fff;padding:2px 10px;border-radius:20px;font-size:11px;">
                        {{ request('lembaga') }}
                    </span>
                @endif
            </span>
            <a href="{{ route('peserta.index') }}" style="font-size:12px;color:#0369a1;text-decoration:underline;">Hapus filter</a>
        </div>
    @endif

    <!-- Card Tabel -->
    <div style="background:#ffffff;border-radius:10px;border:1px solid #e2e8f0;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <!-- Filter & Search -->
        <div style="padding:12px 16px;border-bottom:1px solid #e2e8f0;display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:10px;">
            <span style="font-size:13px;color:#64748b;">Total: <strong style="color:#0f172a;">{{ $total ?? 0 }}</strong> siswa</span>
            
            <div style="display:flex;flex-wrap:wrap;gap:8px;">
                <!-- Search Input -->
                <form method="GET" action="{{ route('peserta.index') }}" style="display:flex;align-items:center;gap:6px;" id="filter-form">
                    <div style="position:relative;">
                        <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);font-size:16px;color:#94a3b8;">🔍</span>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               style="padding:6px 12px 6px 34px;border:1px solid #e2e8f0;border-radius:20px;font-size:13px;outline:none;width:180px;background:#f8fafc;transition:all 0.3s;"
                               placeholder="Cari NIS atau Nama..."
                               onfocus="this.style.width='220px';this.style.borderColor='#1e293b';"
                               onblur="this.style.width='180px';this.style.borderColor='#e2e8f0';">
                    </div>
                    
                    <!-- Filter Lembaga -->
                    <select name="lembaga" style="padding:6px 12px;border:1px solid #e2e8f0;border-radius:20px;font-size:13px;outline:none;background:#f8fafc;cursor:pointer;transition:all 0.3s;" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Semua Lembaga</option>
                        <option value="MTs" {{ request('lembaga') == 'MTs' ? 'selected' : '' }}>🏫 MTs</option>
                        <option value="MA" {{ request('lembaga') == 'MA' ? 'selected' : '' }}>🏛️ MA</option>
                    </select>
                    
                    <button type="submit" style="padding:6px 16px;background:#1e293b;color:#fff;border:none;border-radius:20px;font-size:13px;font-weight:600;cursor:pointer;transition:all 0.15s;">
                        Filter
                    </button>
                    @if(request('search') || request('lembaga'))
                        <a href="{{ route('peserta.index') }}" style="padding:6px 12px;border:1px solid #e2e8f0;border-radius:20px;font-size:13px;color:#64748b;text-decoration:none;transition:all 0.15s;">Reset</a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Tabel -->
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="background:#1e293b;color:#fff;">
                        <th style="padding:8px 12px;text-align:left;font-size:11px;font-weight:600;">NIS</th>
                        <th style="padding:8px 12px;text-align:left;font-size:11px;font-weight:600;">Nama Lengkap</th>
                        <th style="padding:8px 12px;text-align:left;font-size:11px;font-weight:600;">Lembaga</th>
                        <th style="padding:8px 12px;text-align:left;font-size:11px;font-weight:600;">Gugus</th>
                        <th style="padding:8px 12px;text-align:center;font-size:11px;font-weight:600;">Aksi</th>
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
                            <a href="{{ route('peserta.show', $p->id) }}" style="background:none;border:none;color:#3b82f6;cursor:pointer;padding:4px 6px;text-decoration:none;">👁️</a>
                            <a href="{{ route('peserta.edit', $p->id) }}" style="background:none;border:none;color:#1e293b;cursor:pointer;padding:4px 6px;text-decoration:none;">✏️</a>
                            <button onclick="hapusSiswa({{ $p->id }})" style="background:none;border:none;color:#ef4444;cursor:pointer;padding:4px 6px;">🗑️</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding:32px;text-align:center;color:#94a3b8;">
                            <span style="font-size:32px;display:block;margin-bottom:6px;color:#cbd5e1;">📭</span>
                            Belum ada data siswa
                            @if(request('search') || request('lembaga'))
                                <br><span style="font-size:12px;">Coba ubah filter pencarian</span>
                            @endif
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
</div>

<!-- ================================================================ -->
<!-- MODAL TAMBAH PESERTA -->
<!-- ================================================================ -->
<div id="modalTambah" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);z-index:1000;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:16px;padding:28px;max-width:450px;width:100%;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,0.2);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h3 style="font-size:18px;font-weight:700;color:#0f172a;display:flex;align-items:center;gap:8px;">
                <span style="font-size:24px;">➕</span> Tambah Peserta
            </h3>
            <button onclick="tutupModalTambah()" style="background:none;border:none;font-size:24px;cursor:pointer;color:#94a3b8;">✕</button>
        </div>
        <form id="formTambah">
            @csrf
            <div style="space-y:14px;">
                <div>
                    <label style="font-size:13px;font-weight:600;color:#475569;">NIS <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="nis" id="tambahNis" required
                           style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;outline:none;font-family:monospace;"
                           placeholder="Masukkan NIS"
                           onfocus="this.style.borderColor='#1e293b'"
                           onblur="this.style.borderColor='#e2e8f0'">
                </div>
                <div>
                    <label style="font-size:13px;font-weight:600;color:#475569;">Nama Lengkap <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="nama_lengkap" id="tambahNama" required
                           style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;outline:none;"
                           placeholder="Masukkan nama lengkap"
                           onfocus="this.style.borderColor='#1e293b'"
                           onblur="this.style.borderColor='#e2e8f0'">
                </div>
                <div>
                    <label style="font-size:13px;font-weight:600;color:#475569;">Lembaga <span style="color:#ef4444;">*</span></label>
                    <select name="lembaga" id="tambahLembaga" required
                            style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;outline:none;background:#fff;">
                        <option value="MTs">🏫 MTs</option>
                        <option value="MA">🏛️ MA</option>
                    </select>
                </div>
                <div>
                    <label style="font-size:13px;font-weight:600;color:#475569;">Gugus/Kelompok</label>
                    <input type="text" name="gugus" id="tambahGugus"
                           style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;outline:none;"
                           placeholder="Contoh: Kelompok Al-Fatih (opsional)"
                           onfocus="this.style.borderColor='#1e293b'"
                           onblur="this.style.borderColor='#e2e8f0'">
                </div>
                <div style="display:flex;gap:10px;padding-top:8px;">
                    <button type="button" onclick="tutupModalTambah()" style="flex:1;padding:12px;border:2px solid #e2e8f0;border-radius:10px;font-weight:600;cursor:pointer;background:#fff;color:#64748b;">Batal</button>
                    <button type="submit" id="btnSimpanTambah" style="flex:2;padding:12px;border:none;border-radius:10px;font-weight:600;cursor:pointer;background:#10b981;color:#fff;transition:all 0.15s;">
                        💾 Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // ================================================================
    // MODAL TAMBAH PESERTA
    // ================================================================
    function bukaModalTambah() {
        document.getElementById('modalTambah').style.display = 'flex';
        document.getElementById('tambahNis').focus();
    }

    function tutupModalTambah() {
        document.getElementById('modalTambah').style.display = 'none';
        document.getElementById('formTambah').reset();
    }

    // Submit form tambah
    document.getElementById('formTambah').addEventListener('submit', function(e) {
        e.preventDefault();

        const nis = document.getElementById('tambahNis').value.trim();
        const nama = document.getElementById('tambahNama').value.trim();
        const lembaga = document.getElementById('tambahLembaga').value;
        const gugus = document.getElementById('tambahGugus').value.trim();

        if (!nis || !nama) {
            alert('⚠️ NIS dan Nama harus diisi!');
            return;
        }

        const btn = document.getElementById('btnSimpanTambah');
        btn.disabled = true;
        btn.textContent = '⏳ Menyimpan...';

        fetch('/peserta', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ nis, nama_lengkap: nama, lembaga, gugus })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                tutupModalTambah();
                location.reload();
            } else {
                let msg = data.errors ? Object.values(data.errors).flat().join('\n') : data.message;
                alert('❌ ' + msg);
            }
        })
        .catch(error => {
            alert('⚠️ Terjadi kesalahan: ' + error.message);
        })
        .finally(() => {
            btn.disabled = false;
            btn.textContent = '💾 Simpan';
        });
    });

    // Tutup modal dengan ESC atau klik luar
    document.getElementById('modalTambah').addEventListener('click', function(e) {
        if (e.target === this) tutupModalTambah();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') tutupModalTambah();
    });

    // ================================================================
    // SEARCH - Sudah pakai form filter
    // ================================================================

    // ================================================================
    // HAPUS SISWA SATU PER SATU
    // ================================================================
    function hapusSiswa(id) {
        if (!confirm('Yakin ingin menghapus siswa ini?')) return;
        
        fetch(`/peserta/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(() => alert('⚠️ Terjadi kesalahan'));
    }

    // ================================================================
    // HAPUS SEMUA DATA
    // ================================================================
    function hapusSemuaData() {
        if (!confirm('⚠️ PERINGATAN!\n\nAnda akan menghapus SEMUA data peserta dan absensi!\n\nData yang dihapus TIDAK BISA DIKEMBALIKAN!\n\nYakin ingin melanjutkan?')) {
            return;
        }

        const password = prompt('🔐 Masukkan password admin untuk konfirmasi:');
        if (password === null) return;
        if (password.trim() === '') {
            alert('❌ Password tidak boleh kosong!');
            return;
        }

        const btn = document.getElementById('btnHapusSemua');
        const originalText = btn.textContent;
        btn.disabled = true;
        btn.textContent = '⏳ Memproses...';
        btn.style.opacity = '0.6';

        fetch('/peserta/hapus-semua', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ password: password })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            alert('⚠️ Terjadi kesalahan: ' + error.message);
        })
        .finally(() => {
            btn.disabled = false;
            btn.textContent = originalText;
            btn.style.opacity = '1';
        });
    }
</script>
@endpush
@endsection