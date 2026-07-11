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
                <div class="bg-[#10B981]/10 text-[#10B981] p-3 rounded-lg text-sm mb-4">{!! session('success') !!}</div>
            @endif

            <form method="POST" action="{{ route('sesi.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="text-sm font-semibold text-[#444651]">Nama Sesi</label>
                    <input type="text" name="nama_sesi" class="w-full px-4 py-2.5 rounded-lg border border-[#c5c5d3] focus:ring-2 focus:ring-[#00236f] outline-none text-sm" placeholder="Contoh: Pembukaan Matsama" required>
                    @error('nama_sesi')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="text-sm font-semibold text-[#444651]">Jam Mulai Sesi</label>
                    <input type="time" name="jam_mulai" class="w-full px-4 py-2.5 rounded-lg border border-[#c5c5d3] focus:ring-2 focus:ring-[#00236f] outline-none text-sm" required>
                    <p class="text-xs text-[#64748B] mt-1">Waktu sesi dimulai</p>
                    @error('jam_mulai')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="text-sm font-semibold text-[#444651]">Batas Toleransi Absen</label>
                    <input type="time" name="batas_waktu" class="w-full px-4 py-2.5 rounded-lg border border-[#c5c5d3] focus:ring-2 focus:ring-[#00236f] outline-none text-sm" required>
                    <p class="text-xs text-[#64748B] mt-1">+3 menit toleransi setelah batas waktu</p>
                    @error('batas_waktu')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="text-sm font-semibold text-[#444651]">Peruntukan</label>
                    <select name="peruntukan" class="w-full px-4 py-2.5 rounded-lg border border-[#c5c5d3] focus:ring-2 focus:ring-[#00236f] outline-none text-sm">
                        <option value="Semua">Semua</option>
                        <option value="MTs">MTs</option>
                        <option value="MA">MA</option>
                    </select>
                    @error('peruntukan')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="w-full bg-[#a53936] text-white py-3 rounded-lg font-semibold text-sm hover:bg-[#852221] transition-all">
                    SIMPAN SESI
                </button>
            </form>

            @if(session('success'))
                <div class="mt-3 text-xs text-[#64748B]">
                    💡 Kode sesi akan dibuat otomatis (contoh: 001PKN)
                </div>
            @endif
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
                            <th class="px-4 py-3 text-xs font-semibold">Kode</th>
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
                        <tr style="border-bottom:1px solid #e2e8f0;" data-sesi-id="{{ $s->id }}">
                            <td class="px-4 py-3 font-mono text-sm font-bold text-[#00236f]">{{ $s->kode_sesi ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm font-medium nama-sesi">{{ $s->nama_sesi }}</td>
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
                            <td colspan="7" class="px-4 py-8 text-center text-[#64748B]">
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

<!-- ================================================================ -->
<!-- MODAL EDIT SESI -->
<!-- ================================================================ -->
<div id="modalEditSesi" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);z-index:1000;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:16px;padding:28px;max-width:450px;width:100%;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,0.2);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h3 style="font-size:18px;font-weight:700;color:#0f172a;display:flex;align-items:center;gap:8px;">
                <span class="material-symbols-outlined">edit</span> Edit Sesi
            </h3>
            <button onclick="tutupModalEdit()" style="background:none;border:none;font-size:24px;cursor:pointer;color:#94a3b8;">✕</button>
        </div>
        <form id="formEditSesi">
            @csrf
            @method('PUT')
            <input type="hidden" id="editSesiId">
            <div style="space-y:14px;">
                <div>
                    <label style="font-size:13px;font-weight:600;color:#475569;">Nama Sesi</label>
                    <input type="text" id="editNamaSesi" required
                           style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;outline:none;"
                           placeholder="Nama sesi"
                           onfocus="this.style.borderColor='#1e293b'"
                           onblur="this.style.borderColor='#e2e8f0'">
                </div>
                <div>
                    <label style="font-size:13px;font-weight:600;color:#475569;">Jam Mulai</label>
                    <input type="time" id="editJamMulai" required
                           style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;outline:none;"
                           onfocus="this.style.borderColor='#1e293b'"
                           onblur="this.style.borderColor='#e2e8f0'">
                </div>
                <div>
                    <label style="font-size:13px;font-weight:600;color:#475569;">Batas Waktu</label>
                    <input type="time" id="editBatasWaktu" required
                           style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;outline:none;"
                           onfocus="this.style.borderColor='#1e293b'"
                           onblur="this.style.borderColor='#e2e8f0'">
                </div>
                <div>
                    <label style="font-size:13px;font-weight:600;color:#475569;">Peruntukan</label>
                    <select id="editPeruntukan" style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;outline:none;background:#fff;">
                        <option value="Semua">Semua</option>
                        <option value="MTs">MTs</option>
                        <option value="MA">MA</option>
                    </select>
                </div>
                <div style="display:flex;gap:10px;padding-top:8px;">
                    <button type="button" onclick="tutupModalEdit()" style="flex:1;padding:12px;border:2px solid #e2e8f0;border-radius:10px;font-weight:600;cursor:pointer;background:#fff;color:#64748b;">Batal</button>
                    <button type="submit" id="btnSimpanEdit" style="flex:2;padding:12px;border:none;border-radius:10px;font-weight:600;cursor:pointer;background:#00236f;color:#fff;transition:all 0.15s;">
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
    // TOGGLE SESI
    // ================================================================
    function toggleSesi(id) {
        fetch(`/sesi/${id}/toggle-active`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(res => res.json()).then(data => { 
            if(data.success) location.reload(); 
        });
    }

    // ================================================================
    // HAPUS SESI DENGAN KONFIRMASI PASSWORD
    // ================================================================
    function hapusSesi(id) {
        const row = document.querySelector(`tr[data-sesi-id="${id}"]`);
        const namaSesi = row ? row.querySelector('.nama-sesi').textContent : 'sesi ini';
        
        if (!confirm(`⚠️ PERINGATAN!\n\nAnda akan menghapus sesi:\n"${namaSesi}"\n\nSemua data absensi terkait juga akan terhapus!\n\nData yang dihapus TIDAK BISA DIKEMBALIKAN!\n\nLanjutkan?`)) {
            return;
        }

        const password = prompt('🔐 Masukkan password admin untuk konfirmasi:');
        if (password === null) return;
        if (password.trim() === '') {
            alert('❌ Password tidak boleh kosong!');
            return;
        }

        const btn = document.querySelector(`button[onclick="hapusSesi(${id})"]`);
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '⏳';

        fetch(`/sesi/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ password: password })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.message || 'Gagal menghapus');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            alert('⚠️ ' + error.message);
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }

    // ================================================================
    // EDIT SESI
    // ================================================================
    function editSesi(id) {
        fetch(`/sesi/${id}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('editSesiId').value = data.id;
                document.getElementById('editNamaSesi').value = data.nama_sesi;
                document.getElementById('editJamMulai').value = data.jam_mulai ? data.jam_mulai.substring(0, 5) : '';
                document.getElementById('editBatasWaktu').value = data.batas_waktu.substring(0, 5);
                document.getElementById('editPeruntukan').value = data.peruntukan;
                document.getElementById('modalEditSesi').style.display = 'flex';
            })
            .catch(() => {
                alert('❌ Gagal mengambil data sesi');
            });
    }

    function tutupModalEdit() {
        document.getElementById('modalEditSesi').style.display = 'none';
    }

    document.getElementById('formEditSesi').addEventListener('submit', function(e) {
        e.preventDefault();

        const id = document.getElementById('editSesiId').value;
        const nama_sesi = document.getElementById('editNamaSesi').value.trim();
        const jam_mulai = document.getElementById('editJamMulai').value;
        const batas_waktu = document.getElementById('editBatasWaktu').value;
        const peruntukan = document.getElementById('editPeruntukan').value;

        if (!nama_sesi || !jam_mulai || !batas_waktu) {
            alert('⚠️ Semua field harus diisi!');
            return;
        }

        const btn = document.getElementById('btnSimpanEdit');
        btn.disabled = true;
        btn.textContent = '⏳ Menyimpan...';

        fetch(`/sesi/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ nama_sesi, jam_mulai, batas_waktu, peruntukan })
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`Server error: ${text.substring(0, 100)}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                tutupModalEdit();
                location.reload();
            } else {
                alert('❌ ' + (data.message || 'Gagal update sesi'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('⚠️ Terjadi kesalahan: ' + error.message);
        })
        .finally(() => {
            btn.disabled = false;
            btn.textContent = '💾 Simpan';
        });
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') tutupModalEdit();
    });

    document.getElementById('modalEditSesi').addEventListener('click', function(e) {
        if (e.target === this) tutupModalEdit();
    });
</script>
@endpush
@endsection