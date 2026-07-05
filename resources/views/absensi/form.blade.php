@extends('layouts.app')

@section('title', 'Form Absensi')

@section('content')
<div class="max-w-[480px] mx-auto px-4 py-4">
    <!-- Header -->
    <div class="text-center mb-6">
        <div class="w-20 h-20 mx-auto mb-3 rounded-full bg-white shadow-sm flex items-center justify-center p-2 border border-[#c5c5d3]">
            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuBcCiB3GRdHoDbEJUKkZcLov1JVga0smG4vR6tLdhOP3uL8P2_J5HByT-YtsZxaigAQIMxdSFwJe6T6mFUThQ_hFg2lYYCYAEBXJVGMNZCqUohM6mQwJPDdJ3jCpxKiAF76OxaZKupxJfeFTRthjjt9Vt5hSuWeQT_xqKs7TRh0hTsKKSuK6lYwhUWIkccSjSmwu9rC_FunOvPX-A0swYt7pZWqoQIBCPc4N1UQpvIkQc_a5Bs7Nfa285IgDLOwTWfmHqHZ_iE1SSY" class="w-full h-full object-contain" alt="Logo">
        </div>
        <h1 class="text-2xl font-bold text-[#00236f]">Absensi Digital</h1>
        <p class="text-sm text-[#444651] opacity-80 mt-1">Al-Khoeriyah Attendance System</p>
    </div>

    <!-- Active Session -->
    <div class="bg-[#e0e3e5]/30 border border-[#c5c5d3] rounded-xl p-4 flex items-center gap-4 mb-4">
        <div class="w-10 h-10 rounded-full bg-[#00236f]/10 flex items-center justify-center text-[#00236f] shrink-0">
            <span class="material-symbols-outlined">event_available</span>
        </div>
        <div>
            <p class="text-[12px] font-semibold text-[#00236f] uppercase tracking-wider">Sesi Aktif</p>
            <p class="text-xl font-semibold text-[#191c1e]">
                @if(isset($sesiAktif) && $sesiAktif)
                    {{ $sesiAktif->nama_sesi }}
                @else
                    Tidak ada sesi aktif
                @endif
            </p>
        </div>
    </div>

    @if(isset($error) || !isset($sesiAktif) || !$sesiAktif)
        <div class="bg-[#ffdad6] border border-[#ba1a1a]/30 text-[#93000a] p-4 rounded-xl mb-4">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined">warning</span>
                <div>
                    <p class="font-semibold">⚠️ Tidak ada sesi aktif saat ini</p>
                    <p class="text-sm">Silakan hubungi panitia untuk mengaktifkan sesi</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Institution Selection -->
    <div class="mb-4">
        <p class="text-sm font-semibold text-[#444651] ml-1">Pilih Institusi</p>
        <div class="grid grid-cols-2 gap-3 mt-1">
            <div class="relative">
                <input class="hidden peer" id="mts" name="institution" type="radio" value="MTs" checked>
                <label class="flex flex-col items-center justify-center p-4 border border-[#c5c5d3] bg-white rounded-xl transition-all duration-200 active:scale-95 cursor-pointer peer-checked:border-[#a53936] peer-checked:bg-[#ffdad7] peer-checked:shadow-[inset_0_0_0_1px_#a53936]" for="mts">
                    <span class="material-symbols-outlined text-3xl mb-1 text-[#00236f]">school</span>
                    <span class="font-semibold text-base">MTs</span>
                </label>
            </div>
            <div class="relative">
                <input class="hidden peer" id="ma" name="institution" type="radio" value="MA">
                <label class="flex flex-col items-center justify-center p-4 border border-[#c5c5d3] bg-white rounded-xl transition-all duration-200 active:scale-95 cursor-pointer peer-checked:border-[#a53936] peer-checked:bg-[#ffdad7] peer-checked:shadow-[inset_0_0_0_1px_#a53936]" for="ma">
                    <span class="material-symbols-outlined text-3xl mb-1 text-[#00236f]">account_balance</span>
                    <span class="font-semibold text-base">MA</span>
                </label>
            </div>
        </div>
    </div>

    <!-- NIS Input -->
    <div class="mb-4">
        <label class="text-sm font-semibold text-[#444651] ml-1" for="nis">Nomor Induk Siswa (NIS)</label>
        <div class="relative mt-1">
            <input type="text" id="nis" 
                   class="w-full h-14 px-4 bg-white border border-[#c5c5d3] rounded-xl font-mono text-base focus:ring-2 focus:ring-[#00236f] focus:border-transparent outline-none transition-all placeholder:opacity-50" 
                   placeholder="Contoh: 121132..." 
                   {{ !isset($sesiAktif) || !$sesiAktif ? 'disabled' : '' }}>
            <div class="hidden absolute right-4 top-1/2 -translate-y-1/2" id="loading-indicator">
                <span class="material-symbols-outlined animate-spin text-[#00236f]">sync</span>
            </div>
        </div>
    </div>

    <!-- Nama Lengkap -->
    <div class="mb-4">
        <label class="text-sm font-semibold text-[#444651] ml-1">Nama Lengkap Siswa</label>
        <div class="w-full h-14 px-4 bg-[#f2f4f6] border border-[#c5c5d3] rounded-xl flex items-center text-[#444651] text-base opacity-70 italic">
            <span id="student-name">Masukkan NIS untuk memverifikasi...</span>
        </div>
    </div>

    <!-- Signature Pad -->
    <div class="mb-4">
        <div class="flex justify-between items-end ml-1">
            <label class="text-sm font-semibold text-[#444651]">Tanda Tangan Digital</label>
            <button class="flex items-center gap-1 text-[#a53936] font-semibold text-[14px] hover:underline active:opacity-70" id="clear-signature" type="button" {{ !isset($sesiAktif) || !$sesiAktif ? 'disabled' : '' }}>
                <span class="material-symbols-outlined text-[18px]">history</span>
                Reset Coretan
            </button>
        </div>
        <div class="relative w-full aspect-[4/3] bg-white border border-[#c5c5d3] rounded-xl overflow-hidden shadow-inner ring-1 ring-black/5 mt-1">
            <canvas class="signature-canvas w-full h-full touch-none" id="signature-pad" {{ !isset($sesiAktif) || !$sesiAktif ? 'style="pointer-events:none;opacity:0.6;"' : '' }}></canvas>
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-20 flex-col gap-2" id="sign-hint">
                <span class="material-symbols-outlined text-5xl">edit</span>
                <span class="text-sm">Gunakan jari untuk tanda tangan</span>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="pt-2">
        <button class="w-full h-16 bg-[#7F1D1D] text-white rounded-xl font-semibold text-lg shadow-lg shadow-[#7F1D1D]/20 active:scale-[0.98] transition-all flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed" 
                id="submit-btn" 
                {{ !isset($sesiAktif) || !$sesiAktif ? 'disabled' : '' }}>
            <span class="material-symbols-outlined">how_to_reg</span>
            KONFIRMASI KEHADIRAN
        </button>
        <p class="text-center mt-4 text-sm text-[#444651] opacity-60">Pastikan data yang Anda masukkan sudah benar.</p>
    </div>
</div>

<!-- Success Modal -->
<div class="fixed inset-0 z-[100] flex items-center justify-center px-6 bg-black/40 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300" id="success-modal">
    <div class="bg-white rounded-3xl p-8 w-full max-w-sm flex flex-col items-center text-center shadow-2xl scale-90 transition-transform duration-300" id="modal-content">
        <div class="w-20 h-20 bg-[#10B981]/10 rounded-full flex items-center justify-center text-[#10B981] mb-6">
            <span class="material-symbols-outlined text-5xl" style="font-variation-settings: 'FILL' 1;">check_circle</span>
        </div>
        <h3 class="text-2xl font-bold text-[#191c1e] mb-2">Absensi Berhasil</h3>
        <p class="text-base text-[#444651] mb-8">Terima kasih, data kehadiran Anda telah tercatat dalam sistem kami.</p>
        <button class="w-full py-4 bg-[#00236f] text-white rounded-xl font-semibold" onclick="window.location.reload()">SELESAI</button>
    </div>
</div>

@push('scripts')
@if(isset($sesiAktif) && $sesiAktif)
<script>
    // ===== SIGNATURE LOGIC =====
    const canvas = document.getElementById('signature-pad');
    const ctx = canvas.getContext('2d');
    const clearBtn = document.getElementById('clear-signature');
    const hint = document.getElementById('sign-hint');
    let drawing = false;

    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        ctx.scale(ratio, ratio);
        ctx.strokeStyle = "#1E293B";
        ctx.lineWidth = 2.5;
        ctx.lineCap = "round";
        ctx.lineJoin = "round";
    }

    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();

    function startPosition(e) {
        drawing = true;
        hint.classList.add('hidden');
        draw(e);
    }

    function finishedPosition() {
        drawing = false;
        ctx.beginPath();
    }

    function draw(e) {
        if (!drawing) return;
        e.preventDefault();
        
        const rect = canvas.getBoundingClientRect();
        let x, y;
        
        if(e.touches) {
            x = e.touches[0].clientX - rect.left;
            y = e.touches[0].clientY - rect.top;
        } else {
            x = e.clientX - rect.left;
            y = e.clientY - rect.top;
        }

        ctx.lineTo(x, y);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(x, y);
    }

    canvas.addEventListener('mousedown', startPosition);
    canvas.addEventListener('touchstart', startPosition, {passive: false});
    canvas.addEventListener('mouseup', finishedPosition);
    canvas.addEventListener('touchend', finishedPosition);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('touchmove', draw, {passive: false});

    clearBtn.addEventListener('click', () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        hint.classList.remove('hidden');
    });

    // ===== AUTO-FILL =====
    const nisInput = document.getElementById('nis');
    const nameOutput = document.getElementById('student-name');
    const loader = document.getElementById('loading-indicator');
    let namaDitemukan = false;

    nisInput.addEventListener('input', function() {
        const nis = this.value.trim();
        if (nis.length >= 4) {
            loader.classList.remove('hidden');
            nameOutput.textContent = 'Mencari...';
            nameOutput.className = 'text-[#444651] text-base';

            fetch(`/peserta/cari/${nis}`)
                .then(res => res.json())
                .then(data => {
                    loader.classList.add('hidden');
                    if (data.found) {
                        nameOutput.textContent = data.nama;
                        nameOutput.className = 'text-[#00236f] font-semibold';
                        namaDitemukan = true;
                    } else {
                        nameOutput.textContent = '❌ NIS tidak terdaftar! Silakan hubungi panitia.';
                        nameOutput.className = 'text-[#ba1a1a] font-semibold';
                        namaDitemukan = false;
                    }
                })
                .catch(() => {
                    loader.classList.add('hidden');
                    nameOutput.textContent = '⚠️ Gagal memuat data';
                    nameOutput.className = 'text-[#ba1a1a] font-semibold';
                });
        } else {
            nameOutput.textContent = 'Masukkan NIS untuk memverifikasi...';
            nameOutput.className = 'text-[#444651] text-base opacity-70 italic';
            namaDitemukan = false;
        }
    });

    // ===== SUBMIT =====
    const submitBtn = document.getElementById('submit-btn');
    const modal = document.getElementById('success-modal');
    const modalContent = document.getElementById('modal-content');

    submitBtn.addEventListener('click', function() {
        const nis = nisInput.value.trim();
        
        if (!namaDitemukan) {
            alert('❌ NIS tidak terdaftar! Silakan hubungi panitia.');
            nisInput.focus();
            return;
        }

        // Cek canvas kosong
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const pixels = imageData.data;
        let isEmpty = true;
        for (let i = 0; i < pixels.length; i += 4) {
            if (pixels[i] !== 255 || pixels[i+1] !== 255 || pixels[i+2] !== 255) {
                isEmpty = false;
                break;
            }
        }
        if (isEmpty) {
            alert('⚠️ Silakan isi tanda tangan terlebih dahulu!');
            return;
        }

        submitBtn.innerHTML = `<span class="material-symbols-outlined animate-spin">sync</span> Memproses...`;
        submitBtn.disabled = true;

        fetch('/absensi/store', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                nis: nis,
                ttd: canvas.toDataURL('image/png')
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    modalContent.classList.remove('scale-90');
                }, 50);
            } else {
                alert('❌ ' + data.message);
                submitBtn.innerHTML = `<span class="material-symbols-outlined">how_to_reg</span> KONFIRMASI KEHADIRAN`;
                submitBtn.disabled = false;
            }
        })
        .catch(error => {
            alert('⚠️ Terjadi kesalahan: ' + error.message);
            submitBtn.innerHTML = `<span class="material-symbols-outlined">how_to_reg</span> KONFIRMASI KEHADIRAN`;
            submitBtn.disabled = false;
        });
    });
</script>
@endif
@endpush
@endsection