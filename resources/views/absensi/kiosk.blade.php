<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Absensi Digital - Al-Khoeriyah</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            color: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        
        .kiosk-container {
            max-width: 500px;
            width: 100%;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.12);
            padding: 32px 28px;
            position: relative;
        }
        
        .kiosk-header { text-align: center; margin-bottom: 24px; }
        .kiosk-header .logo {
            width: 72px;
            height: 72px;
            margin: 0 auto 12px;
            background: #1e293b;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }
        .kiosk-header .logo span { color: #fff; font-size: 36px; }
        .kiosk-header h1 { font-size: 22px; font-weight: 700; color: #1e293b; }
        .kiosk-header p { font-size: 13px; color: #64748B; margin-top: 2px; }
        
        .badge-lembaga {
            display: inline-block;
            padding: 6px 20px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-lembaga.mts { background: #dc262615; color: #dc2626; border: 2px solid #dc2626; }
        .badge-lembaga.ma { background: #1e293b15; color: #1e293b; border: 2px solid #1e293b; }
        .badge-lembaga.semua { background: #10b98115; color: #10b981; border: 2px solid #10b981; }
        
        .session-card {
            background: #f8fafc;
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            border: 1px solid #e2e8f0;
        }
        .session-card .icon {
            width: 40px;
            height: 40px;
            background: #1e293b10;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .session-card .icon span { color: #1e293b; font-size: 24px; }
        .session-card .info p { font-size: 10px; font-weight: 700; color: #1e293b; text-transform: uppercase; letter-spacing: 0.5px; }
        .session-card .info h3 { font-size: 16px; font-weight: 700; color: #0f172a; }
        
        .counter-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-bottom: 16px;
        }
        .counter-item {
            background: #f8fafc;
            border-radius: 10px;
            padding: 10px 8px;
            text-align: center;
            border: 1px solid #e2e8f0;
        }
        .counter-item .number { font-size: 22px; font-weight: 800; }
        .counter-item .label { font-size: 9px; color: #64748b; font-weight: 600; text-transform: uppercase; }
        .counter-item.mts .number { color: #dc2626; }
        .counter-item.ma .number { color: #1e293b; }
        .counter-item.total .number { color: #10b981; }
        
        .progress-wrapper { margin-bottom: 16px; }
        .progress-wrapper .progress-label {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
        }
        .progress-wrapper .progress-bar {
            height: 6px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 4px;
        }
        .progress-wrapper .progress-bar .fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.5s ease;
            background: linear-gradient(90deg, #1e293b, #10b981);
        }
        
        .form-group { margin-bottom: 16px; }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 4px;
        }
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 16px;
            font-family: 'JetBrains Mono', monospace;
            outline: none;
            transition: all 0.2s;
            background: #f8fafc;
        }
        .form-group input:focus {
            border-color: #1e293b;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(30,41,59,0.08);
        }
        .form-group input:disabled { opacity: 0.5; cursor: not-allowed; }
        .form-group .name-display {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 16px;
            background: #f1f5f9;
            color: #0f172a;
            min-height: 50px;
            display: flex;
            align-items: center;
            font-weight: 500;
        }
        .form-group .name-display.found { border-color: #10b981; background: #10b98108; color: #10b981; }
        .form-group .name-display.not-found { border-color: #ef4444; background: #ef444408; color: #ef4444; }
        .form-group .name-display.loading { border-color: #f59e0b; background: #f59e0b08; color: #f59e0b; }
        
        /* Tombol Daftar Peserta Baru */
        .btn-register {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 4px;
            background: #f59e0b;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-register:hover { background: #d97706; }
        
        .signature-wrapper {
            position: relative;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
            height: 180px;
        }
        .signature-wrapper canvas {
            width: 100%;
            height: 100%;
            touch-action: none;
            cursor: crosshair;
        }
        .signature-wrapper .hint {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #cbd5e1;
            pointer-events: none;
            transition: opacity 0.3s;
        }
        .signature-wrapper .hint.hidden { opacity: 0; }
        .signature-wrapper .hint span { font-size: 40px; }
        .signature-wrapper .hint p { font-size: 13px; margin-top: 4px; }
        .signature-actions { display: flex; justify-content: flex-end; margin-top: 6px; }
        .signature-actions button {
            background: none;
            border: none;
            color: #a53936;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .signature-actions button:hover { text-decoration: underline; }
        
        .btn-submit {
            width: 100%;
            padding: 16px;
            background: #7F1D1D;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.2s;
            box-shadow: 0 4px 16px rgba(127,29,29,0.3);
            margin-top: 8px;
        }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 24px rgba(127,29,29,0.4); }
        .btn-submit:active { transform: scale(0.97); }
        .btn-submit:disabled { opacity: 0.6; cursor: not-allowed; transform: none !important; }
        
        .kiosk-footer {
            text-align: center;
            margin-top: 16px;
            font-size: 12px;
            color: #94a3b8;
        }
        .kiosk-footer .btn-exit {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            color: #64748b;
            background: none;
            border: none;
            font-size: 13px;
            cursor: pointer;
            padding: 4px 12px;
            border-radius: 6px;
            transition: background 0.2s;
        }
        .kiosk-footer .btn-exit:hover { background: #f1f5f9; color: #0f172a; }
        
        /* MODALS */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(8px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 20px;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .modal-overlay.show { display: flex; opacity: 1; }
        .modal-content {
            background: #fff;
            border-radius: 24px;
            padding: 40px 32px;
            max-width: 400px;
            width: 100%;
            text-align: center;
            transform: scale(0.9);
            transition: transform 0.3s;
        }
        .modal-overlay.show .modal-content { transform: scale(1); }
        .modal-content .icon {
            width: 72px;
            height: 72px;
            background: #10b98110;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }
        .modal-content .icon span { color: #10b981; font-size: 40px; }
        .modal-content h2 { font-size: 22px; font-weight: 700; color: #0f172a; }
        .modal-content p { color: #64748b; font-size: 14px; margin-top: 4px; }
        .modal-content .timer { margin-top: 12px; font-size: 14px; color: #94a3b8; }
        .modal-content .timer strong { color: #1e293b; font-size: 18px; }
        
        .spinner { animation: spin 0.8s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        
        .password-modal .modal-content { max-width: 380px; }
        .password-modal .modal-content input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 16px;
            outline: none;
            margin: 12px 0;
        }
        .password-modal .modal-content input:focus { border-color: #1e293b; }
        .password-modal .modal-content .btn-group { display: flex; gap: 10px; }
        .password-modal .modal-content .btn-group button {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
        }
        .password-modal .modal-content .btn-group .btn-confirm { background: #1e293b; color: #fff; }
        .password-modal .modal-content .btn-group .btn-cancel { background: #f1f5f9; color: #64748b; }
        
        /* Register Modal */
        .register-modal .modal-content { max-width: 420px; text-align: left; }
        .register-modal .modal-content .form-group { margin-bottom: 12px; }
        .register-modal .modal-content .form-group label { font-size: 13px; font-weight: 600; color: #475569; display: block; margin-bottom: 4px; }
        .register-modal .modal-content .form-group input,
        .register-modal .modal-content .form-group select {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 15px;
            outline: none;
            background: #fff;
            font-family: 'Inter', sans-serif;
        }
        .register-modal .modal-content .form-group input:focus,
        .register-modal .modal-content .form-group select:focus {
            border-color: #1e293b;
        }
        .register-modal .modal-content .btn-group { display: flex; gap: 10px; margin-top: 16px; }
        .register-modal .modal-content .btn-group button {
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }
        .register-modal .modal-content .btn-group .btn-cancel { flex: 1; background: #f1f5f9; color: #64748b; }
        .register-modal .modal-content .btn-group .btn-submit-register { flex: 2; background: #1e293b; color: #fff; }
        .register-modal .modal-content .btn-group .btn-submit-register:hover { background: #0f172a; }
        .register-modal .modal-content .btn-group .btn-submit-register:disabled { opacity: 0.6; cursor: not-allowed; }
        
        @media (max-width: 480px) {
            .kiosk-container { padding: 20px 16px; }
            .counter-grid { grid-template-columns: repeat(3, 1fr); gap: 4px; }
            .counter-item .number { font-size: 18px; }
            .kiosk-header h1 { font-size: 18px; }
        }
    </style>
</head>
<body>

    <!-- ===== SUCCESS MODAL ===== -->
    <div class="modal-overlay" id="successModal">
        <div class="modal-content">
            <div class="icon">
                <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">check_circle</span>
            </div>
            <h2>Absensi Berhasil!</h2>
            <p id="successName">Terima kasih, data kehadiran Anda telah tercatat.</p>
            <div class="timer">⏱️ <strong id="countdownTimer">3</strong> detik lagi akan reset...</div>
        </div>
    </div>

    <!-- ===== PASSWORD MODAL ===== -->
    <div class="modal-overlay password-modal" id="passwordModal">
        <div class="modal-content">
            <div style="text-align:center;margin-bottom:16px;">
                <span class="material-symbols-outlined" style="font-size:48px;color:#1e293b;">lock</span>
                <h2 style="font-size:18px;font-weight:700;color:#0f172a;margin-top:4px;">Konfirmasi Keluar</h2>
                <p style="color:#64748b;font-size:13px;">Masukkan password admin untuk keluar dari mode kiosk</p>
            </div>
            <input type="password" id="exitPassword" placeholder="Masukkan Password">
            <div style="color:#ef4444;font-size:13px;margin-top:4px;display:none;" id="passwordError">❌ Password salah!</div>
            <div class="btn-group" style="margin-top:12px;">
                <button class="btn-cancel" onclick="closePasswordModal()">Batal</button>
                <button class="btn-confirm" onclick="confirmExit()">Konfirmasi</button>
            </div>
        </div>
    </div>

    <!-- ===== REGISTER MODAL (TAMBAH PESERTA BARU) ===== -->
    <div class="modal-overlay register-modal" id="registerModal">
        <div class="modal-content">
            <h3 style="font-size:18px;font-weight:700;color:#0f172a;margin-bottom:4px;">📝 Daftar Peserta Baru</h3>
            <p style="color:#64748b;font-size:14px;margin-bottom:16px;">
                NIS <strong id="registerNisDisplay"></strong> belum terdaftar. Silakan isi data berikut:
            </p>
            
            <form id="registerForm">
                <input type="hidden" id="regNis">
                
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" id="regNama" placeholder="Masukkan nama lengkap" required>
                </div>
                
                <div class="form-group">
                    <label>Lembaga</label>
                    <select id="regLembaga" required>
                        <option value="MTs">MTs</option>
                        <option value="MA">MA</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Gugus/Kelompok (Opsional)</label>
                    <input type="text" id="regGugus" placeholder="Contoh: Kelompok 1">
                </div>
                
                <div class="btn-group">
                    <button type="button" class="btn-cancel" onclick="closeRegisterModal()">Batal</button>
                    <button type="submit" class="btn-submit-register" id="registerSubmitBtn">
                        📝 Daftarkan & Lanjut Absen
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== MAIN KIOSK ===== -->
    <div class="kiosk-container">
        <!-- Header -->
        <div class="kiosk-header">
            <div class="logo">
                <span class="material-symbols-outlined">school</span>
            </div>
            <h1>Absensi Digital</h1>
            <p>Al-Khoeriyah Attendance System</p>
        </div>

        <!-- Badge Lembaga -->
        <div style="text-align:center;margin-bottom:16px;">
            <span class="badge-lembaga {{ $lembaga == 'MTs' ? 'mts' : ($lembaga == 'MA' ? 'ma' : 'semua') }}">
                📌 {{ ucfirst($lembaga) }}
                @if($lembaga == 'MTs' || $lembaga == 'MA')
                    <span style="font-weight:400;font-size:11px;">(Aktif)</span>
                @endif
            </span>
        </div>

        <!-- Session Info -->
        <div class="session-card">
            <div class="icon"><span class="material-symbols-outlined">event_available</span></div>
            <div class="info">
                <p>Sesi Aktif</p>
                <h3 id="sessionName">{{ $sesiAktif ? $sesiAktif->nama_sesi : 'Tidak ada sesi aktif' }}</h3>
            </div>
        </div>

        <!-- Counter -->
        <div class="counter-grid" id="counterGrid">
            <div class="counter-item mts">
                <div class="number" id="counterMts">0</div>
                <div class="label">MTs</div>
            </div>
            <div class="counter-item ma">
                <div class="number" id="counterMa">0</div>
                <div class="label">MA</div>
            </div>
            <div class="counter-item total">
                <div class="number" id="counterTotal">0</div>
                <div class="label">Total</div>
            </div>
        </div>

        <!-- Progress -->
        <div class="progress-wrapper">
            <div class="progress-label">
                <span>Progress Kehadiran</span>
                <span id="progressText">0%</span>
            </div>
            <div class="progress-bar">
                <div class="fill" id="progressFill" style="width:0%;"></div>
            </div>
        </div>

        <!-- Form -->
        <form id="kioskForm" autocomplete="off">
            @csrf
            <input type="hidden" id="lembagaInput" value="{{ $lembaga }}">

            <div class="form-group">
                <label for="nisInput">Nomor Induk Siswa (NIS)</label>
                <input type="text" id="nisInput" placeholder="Contoh: 121132..." class="font-mono" autofocus>
                <div id="loadingIndicator" style="display:none;font-size:13px;color:#f59e0b;margin-top:4px;">
                    <span class="material-symbols-outlined spinner" style="font-size:16px;display:inline-block;">sync</span> Mencari data...
                </div>
                <!-- Tombol Daftar Peserta Baru (muncul saat NIS tidak ditemukan) -->
                <button type="button" id="btnRegister" class="btn-register" style="display:none;" onclick="openRegisterModal()">
                    ➕ Daftarkan Peserta Baru
                </button>
            </div>

            <div class="form-group">
                <label>Nama Lengkap</label>
                <div class="name-display" id="nameDisplay">Masukkan NIS untuk verifikasi...</div>
            </div>

            <div class="form-group">
                <label>Tanda Tangan Digital</label>
                <div class="signature-wrapper">
                    <canvas id="signaturePad"></canvas>
                    <div class="hint" id="signHint">
                        <span class="material-symbols-outlined">edit</span>
                        <p>Gunakan jari untuk menandatangani</p>
                    </div>
                </div>
                <div class="signature-actions">
                    <button type="button" id="resetSignature">
                        <span class="material-symbols-outlined" style="font-size:16px;">history</span> Reset Coretan
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-submit" id="submitBtn">
                <span class="material-symbols-outlined">how_to_reg</span>
                KONFIRMASI KEHADIRAN
            </button>
        </form>

        <p style="text-align:center;font-size:13px;color:#94a3b8;margin-top:10px;">
            Pastikan data yang Anda masukkan sudah benar.
        </p>

        <div class="kiosk-footer">
            <button class="btn-exit" onclick="openPasswordModal()">
                <span class="material-symbols-outlined" style="font-size:16px;">lock</span> Kembali ke Dashboard
            </button>
            <div style="margin-top:6px;font-size:10px;color:#cbd5e1;">
                v1.0.0 | Al-Khoeriyah © 2026
            </div>
        </div>
    </div>

    <script>
        // ================================================================
        // 1. CONFIGURATION
        // ================================================================
        const LEMBAGA = document.getElementById('lembagaInput').value;
        const ADMIN_PASSWORD = '{{ env("KIOSK_PASSWORD", "login") }}';
        const AUTO_RESET_DELAY = 3000;

        // ================================================================
        // 2. DOM REFS
        // ================================================================
        const nisInput = document.getElementById('nisInput');
        const nameDisplay = document.getElementById('nameDisplay');
        const loadingIndicator = document.getElementById('loadingIndicator');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('kioskForm');
        const successModal = document.getElementById('successModal');
        const successName = document.getElementById('successName');
        const countdownTimer = document.getElementById('countdownTimer');
        const counterMts = document.getElementById('counterMts');
        const counterMa = document.getElementById('counterMa');
        const counterTotal = document.getElementById('counterTotal');
        const progressFill = document.getElementById('progressFill');
        const progressText = document.getElementById('progressText');
        const btnRegister = document.getElementById('btnRegister');
        const registerModal = document.getElementById('registerModal');

        // ================================================================
        // 3. SIGNATURE PAD
        // ================================================================
        const canvas = document.getElementById('signaturePad');
        const ctx = canvas.getContext('2d');
        const signHint = document.getElementById('signHint');
        let isDrawing = false;
        let hasSignature = false;

        function resizeCanvas() {
            const rect = canvas.parentElement.getBoundingClientRect();
            const ratio = window.devicePixelRatio || 1;
            canvas.width = rect.width * ratio;
            canvas.height = rect.height * ratio;
            canvas.style.width = rect.width + 'px';
            canvas.style.height = rect.height + 'px';
            ctx.scale(ratio, ratio);
            ctx.strokeStyle = '#1E293B';
            ctx.lineWidth = 3;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
        }
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);

        function getPos(e) {
            const rect = canvas.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            return { x: clientX - rect.left, y: clientY - rect.top };
        }

        function startDraw(e) {
            e.preventDefault();
            isDrawing = true;
            signHint.classList.add('hidden');
            const pos = getPos(e);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
        }

        function draw(e) {
            if (!isDrawing) return;
            e.preventDefault();
            const pos = getPos(e);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
            hasSignature = true;
        }

        function endDraw() {
            isDrawing = false;
            ctx.beginPath();
        }

        canvas.addEventListener('mousedown', startDraw);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', endDraw);
        canvas.addEventListener('mouseleave', endDraw);
        canvas.addEventListener('touchstart', startDraw, { passive: false });
        canvas.addEventListener('touchmove', draw, { passive: false });
        canvas.addEventListener('touchend', endDraw, { passive: false });

        document.getElementById('resetSignature').addEventListener('click', function() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            signHint.classList.remove('hidden');
            hasSignature = false;
        });

        function clearSignature() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            signHint.classList.remove('hidden');
            hasSignature = false;
        }

        function getSignatureData() {
            return canvas.toDataURL('image/png');
        }

        // ================================================================
        // 4. AUTO-FILL NAMA + TOMBOL REGISTER
        // ================================================================
        let namaDitemukan = false;
        let pesertaData = null;

        nisInput.addEventListener('input', function() {
            const nis = this.value.trim();

            if (nis.length >= 4) {
                loadingIndicator.style.display = 'block';
                btnRegister.style.display = 'none';
                nameDisplay.textContent = 'Mencari...';
                nameDisplay.className = 'name-display loading';

                fetch(`/peserta/cari/${nis}`)
                    .then(res => res.json())
                    .then(data => {
                        loadingIndicator.style.display = 'none';
                        if (data.found) {
                            const lembagaPeserta = data.data.lembaga;
                            if (LEMBAGA !== 'semua' && lembagaPeserta !== LEMBAGA) {
                                nameDisplay.textContent = `❌ NIS terdaftar di ${lembagaPeserta}, bukan ${LEMBAGA}!`;
                                nameDisplay.className = 'name-display not-found';
                                namaDitemukan = false;
                                pesertaData = null;
                                btnRegister.style.display = 'none';
                                return;
                            }
                            nameDisplay.textContent = data.nama;
                            nameDisplay.className = 'name-display found';
                            namaDitemukan = true;
                            pesertaData = data.data;
                            btnRegister.style.display = 'none';
                        } else {
                            nameDisplay.textContent = '❌ NIS tidak terdaftar!';
                            nameDisplay.className = 'name-display not-found';
                            namaDitemukan = false;
                            pesertaData = null;
                            // TAMPILKAN TOMBOL DAFTAR
                            btnRegister.style.display = 'block';
                            btnRegister.textContent = '➕ Daftarkan Peserta Baru (NIS: ' + nis + ')';
                            btnRegister.dataset.nis = nis;
                        }
                    })
                    .catch(() => {
                        loadingIndicator.style.display = 'none';
                        nameDisplay.textContent = '⚠️ Gagal memuat data';
                        nameDisplay.className = 'name-display not-found';
                        namaDitemukan = false;
                        btnRegister.style.display = 'none';
                    });
            } else {
                nameDisplay.textContent = 'Masukkan NIS untuk verifikasi...';
                nameDisplay.className = 'name-display';
                namaDitemukan = false;
                pesertaData = null;
                btnRegister.style.display = 'none';
            }
        });

        // ================================================================
        // 5. REGISTER MODAL (TAMBAH PESERTA BARU)
        // ================================================================
        function openRegisterModal() {
            const nis = btnRegister.dataset.nis || nisInput.value.trim();
            if (!nis) {
                alert('Silakan masukkan NIS terlebih dahulu!');
                return;
            }

            document.getElementById('registerNisDisplay').textContent = nis;
            document.getElementById('regNis').value = nis;
            document.getElementById('regNama').value = '';
            document.getElementById('regGugus').value = '';
            
            // Set lembaga default dari URL
            const lembagaSelect = document.getElementById('regLembaga');
            if (LEMBAGA === 'MTs' || LEMBAGA === 'MA') {
                lembagaSelect.value = LEMBAGA;
            }
            
            registerModal.classList.add('show');
            document.getElementById('regNama').focus();
        }

        function closeRegisterModal() {
            registerModal.classList.remove('show');
        }

        // Submit register form
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            registerNewPeserta();
        });

        function registerNewPeserta() {
            const nis = document.getElementById('regNis').value;
            const nama = document.getElementById('regNama').value.trim();
            const lembaga = document.getElementById('regLembaga').value;
            const gugus = document.getElementById('regGugus').value.trim();

            if (!nama) {
                alert('⚠️ Nama lengkap harus diisi!');
                document.getElementById('regNama').focus();
                return;
            }

            const btn = document.getElementById('registerSubmitBtn');
            btn.disabled = true;
            btn.textContent = '⏳ Menyimpan...';

            fetch('/peserta/cepat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    nis: nis,
                    nama_lengkap: nama,
                    lembaga: lembaga,
                    gugus: gugus
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('✅ ' + data.message);
                    closeRegisterModal();
                    // Isi form dengan data peserta baru
                    nisInput.value = nis;
                    nameDisplay.textContent = nama;
                    nameDisplay.className = 'name-display found';
                    namaDitemukan = true;
                    pesertaData = data.data;
                    btnRegister.style.display = 'none';
                } else {
                    alert('❌ ' + data.message);
                }
            })
            .catch(error => {
                alert('⚠️ Terjadi kesalahan: ' + error.message);
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = '📝 Daftarkan & Lanjut Absen';
            });
        }

        // Enter key di modal register
        document.getElementById('regNama').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('regGugus').focus();
            }
        });
        document.getElementById('regGugus').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('registerSubmitBtn').click();
            }
        });

        // Tutup modal klik di luar
        registerModal.addEventListener('click', function(e) {
            if (e.target === this) closeRegisterModal();
        });

        // ================================================================
        // 6. SUBMIT
        // ================================================================
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const nis = nisInput.value.trim();

            if (!namaDitemukan || !pesertaData) {
                alert('❌ NIS tidak terdaftar atau tidak sesuai lembaga!');
                nisInput.focus();
                return;
            }

            if (!hasSignature) {
                alert('⚠️ Silakan isi tanda tangan terlebih dahulu!');
                return;
            }

            const ttdData = getSignatureData();

            submitBtn.disabled = true;
            submitBtn.innerHTML = `<span class="material-symbols-outlined spinner">sync</span> Memproses...`;

            fetch('{{ route("absensi.kiosk.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    nis: nis,
                    ttd: ttdData,
                    lembaga: LEMBAGA
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    playBeep();
                    successName.textContent = 'Selamat ' + pesertaData.nama_lengkap + '! Kehadiran Anda telah tercatat.';
                    successModal.classList.add('show');
                    updateCounter();

                    let countdown = 3;
                    countdownTimer.textContent = countdown;
                    const timer = setInterval(() => {
                        countdown--;
                        countdownTimer.textContent = countdown;
                        if (countdown <= 0) {
                            clearInterval(timer);
                            successModal.classList.remove('show');
                            resetForm();
                        }
                    }, 1000);
                } else {
                    alert('❌ ' + data.message);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = `<span class="material-symbols-outlined">how_to_reg</span> KONFIRMASI KEHADIRAN`;
                }
            })
            .catch(error => {
                alert('⚠️ Terjadi kesalahan: ' + error.message);
                submitBtn.disabled = false;
                submitBtn.innerHTML = `<span class="material-symbols-outlined">how_to_reg</span> KONFIRMASI KEHADIRAN`;
            });
        });

        function resetForm() {
            nisInput.value = '';
            nameDisplay.textContent = 'Masukkan NIS untuk verifikasi...';
            nameDisplay.className = 'name-display';
            clearSignature();
            namaDitemukan = false;
            pesertaData = null;
            btnRegister.style.display = 'none';
            submitBtn.disabled = false;
            submitBtn.innerHTML = `<span class="material-symbols-outlined">how_to_reg</span> KONFIRMASI KEHADIRAN`;
            nisInput.focus();
        }

        // ================================================================
        // 7. COUNTER REAL-TIME
        // ================================================================
        function updateCounter() {
            fetch('{{ route("absensi.counter") }}')
                .then(res => res.json())
                .then(data => {
                    counterMts.textContent = data.mts || 0;
                    counterMa.textContent = data.ma || 0;
                    counterTotal.textContent = data.total || 0;
                    const totalSiswa = data.total_siswa || 1;
                    const percent = Math.min(100, Math.round((data.total / totalSiswa) * 100));
                    progressFill.style.width = percent + '%';
                    progressText.textContent = percent + '%';
                })
                .catch(() => {});
        }

        updateCounter();
        setInterval(updateCounter, 5000);

        // ================================================================
        // 8. SUARA BEEP
        // ================================================================
        function playBeep() {
            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const osc1 = audioCtx.createOscillator();
                const gain1 = audioCtx.createGain();
                osc1.connect(gain1);
                gain1.connect(audioCtx.destination);
                osc1.frequency.value = 880;
                osc1.type = 'sine';
                gain1.gain.value = 0.3;
                osc1.start();
                osc1.stop(audioCtx.currentTime + 0.15);
                setTimeout(() => {
                    const osc2 = audioCtx.createOscillator();
                    const gain2 = audioCtx.createGain();
                    osc2.connect(gain2);
                    gain2.connect(audioCtx.destination);
                    osc2.frequency.value = 1100;
                    osc2.type = 'sine';
                    gain2.gain.value = 0.3;
                    osc2.start();
                    osc2.stop(audioCtx.currentTime + 0.15);
                }, 200);
            } catch (e) {}
        }

        // ================================================================
        // 9. PASSWORD MODAL
        // ================================================================
        function openPasswordModal() {
            document.getElementById('passwordModal').classList.add('show');
            document.getElementById('exitPassword').value = '';
            document.getElementById('passwordError').style.display = 'none';
            document.getElementById('exitPassword').focus();
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').classList.remove('show');
        }

        function confirmExit() {
            const password = document.getElementById('exitPassword').value;
            if (password === ADMIN_PASSWORD) {
                closePasswordModal();
                window.location.href = '{{ route("dashboard") }}';
            } else {
                document.getElementById('passwordError').style.display = 'block';
                document.getElementById('exitPassword').value = '';
                document.getElementById('exitPassword').focus();
            }
        }

        document.getElementById('exitPassword').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') confirmExit();
        });

        // ================================================================
        // 10. KEYBOARD SHORTCUTS
        // ================================================================
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (successModal.classList.contains('show')) {
                    successModal.classList.remove('show');
                    resetForm();
                }
                closePasswordModal();
                closeRegisterModal();
            }
        });

        successModal.addEventListener('click', function(e) {
            if (e.target === this) {
                successModal.classList.remove('show');
                resetForm();
            }
        });

        document.addEventListener('dblclick', function() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => {});
            } else {
                document.exitFullscreen().catch(() => {});
            }
        });

        console.log('✅ KIOSK MODE ACTIVE - Lembaga: ' + LEMBAGA);
    </script>

</body>
</html>