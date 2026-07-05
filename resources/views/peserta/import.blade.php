@extends('layouts.app')

@section('title', 'Import Data Peserta')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-[#E2E8F0] p-8">
        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-[#00236f]/10 rounded-full flex items-center justify-center">
                <span class="material-symbols-outlined text-3xl text-[#00236f]">upload_file</span>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-[#00236f]">Import Data Peserta</h1>
                <p class="text-[#64748B] text-sm">Upload file Excel berisi data peserta untuk kegiatan Matsama & Permata</p>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="bg-[#10B981]/10 border border-[#10B981]/30 text-[#10B981] p-4 rounded-xl mb-4 flex items-start gap-3">
                <span class="material-symbols-outlined">check_circle</span>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-[#ffdad6] border border-[#ba1a1a]/30 text-[#93000a] p-4 rounded-xl mb-4 flex items-start gap-3">
                <span class="material-symbols-outlined">error</span>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @if(session('errors') && count(session('errors')) > 0)
            <div class="bg-[#ffdad6]/20 border border-[#ba1a1a]/30 text-[#93000a] p-4 rounded-xl mb-4">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined">warning</span>
                    <div>
                        <p class="font-bold">Terjadi {{ count(session('errors')) }} kesalahan:</p>
                        <ul class="list-disc pl-5 mt-2 text-sm max-h-40 overflow-y-auto">
                            @foreach(session('errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @if(session('duplicates') && count(session('duplicates')) > 0)
            <div class="bg-[#F59E0B]/10 border border-[#F59E0B]/30 text-[#F59E0B] p-4 rounded-xl mb-4">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined">info</span>
                    <div>
                        <p class="font-bold">{{ count(session('duplicates')) }} data duplikat dilewati:</p>
                        <ul class="list-disc pl-5 mt-2 text-sm max-h-40 overflow-y-auto">
                            @foreach(session('duplicates') as $dup)
                                <li>{{ $dup }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form Import -->
        <form method="POST" action="{{ route('peserta.import') }}" enctype="multipart/form-data" class="space-y-6" id="import-form">
            @csrf

            <!-- Drop Zone -->
            <div id="drop-zone" class="border-2 border-dashed border-[#c5c5d3] rounded-xl p-12 text-center hover:border-[#00236f] transition-all cursor-pointer hover:bg-[#f7f9fb]">
                <span class="material-symbols-outlined text-6xl text-[#c5c5d3]">cloud_upload</span>
                <p class="text-[#64748B] mt-3 font-medium">Drag & drop file Excel, atau klik untuk memilih</p>
                <p class="text-sm text-[#64748B] mt-1">Format: .xlsx, .xls, .csv (max 5MB)</p>
                <input type="file" name="file" accept=".xlsx,.xls,.csv" class="hidden" id="file-input" required>
                <button type="button" id="choose-file-btn" class="mt-4 bg-[#00236f] text-white px-6 py-3 rounded-xl font-semibold hover:bg-[#00236f]/90 transition-all active:scale-95">
                    <span class="material-symbols-outlined text-sm">folder_open</span>
                    Pilih File
                </button>
                <div id="file-info" class="mt-3 hidden">
                    <div class="inline-flex items-center gap-3 bg-[#00236f]/10 text-[#00236f] px-4 py-2 rounded-lg">
                        <span class="material-symbols-outlined">description</span>
                        <span id="file-name" class="font-semibold"></span>
                        <span id="file-size" class="text-sm text-[#64748B]"></span>
                        <button type="button" id="remove-file" class="text-[#ba1a1a] hover:text-[#93000a]">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </button>
                    </div>
                </div>
            </div>

            @error('file')
                <div class="text-[#ba1a1a] text-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">error</span>
                    {{ $message }}
                </div>
            @enderror

            <!-- Buttons -->
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('peserta.export.template') }}" class="flex items-center gap-2 px-6 py-3 border-2 border-[#00236f] text-[#00236f] rounded-xl font-semibold hover:bg-[#00236f] hover:text-white transition-all">
                    <span class="material-symbols-outlined">download</span>
                    Download Template
                </a>
                <button type="submit" id="submit-btn" class="flex-1 bg-[#a53936] text-white py-3 rounded-xl font-semibold hover:bg-[#852221] transition-all active:scale-95 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span class="material-symbols-outlined">upload</span>
                    Upload & Import
                </button>
            </div>
        </form>

        <!-- Format Guide -->
        <div class="mt-8 p-4 bg-[#f2f4f6] rounded-xl">
            <h4 class="font-semibold text-sm mb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#00236f] text-sm">info</span>
                Format File Excel:
            </h4>
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="bg-[#00236f] text-white">
                            <th class="px-4 py-2 text-left rounded-tl-lg">Kolom A</th>
                            <th class="px-4 py-2 text-left">Kolom B</th>
                            <th class="px-4 py-2 text-left">Kolom C</th>
                            <th class="px-4 py-2 text-left rounded-tr-lg">Kolom D</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E2E8F0] bg-white">
                        <tr>
                            <td class="px-4 py-2 font-mono font-bold text-[#00236f]">NIS</td>
                            <td class="px-4 py-2 font-bold">Nama Lengkap</td>
                            <td class="px-4 py-2 font-bold text-[#a53936]">Lembaga (MTs/MA)</td>
                            <td class="px-4 py-2 font-bold">Gugus (opsional)</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-mono text-[#00236f]">2510614001</td>
                            <td class="px-4 py-2">Ahmad Fauzi Rahman</td>
                            <td class="px-4 py-2 text-[#a53936]">MA</td>
                            <td class="px-4 py-2">Kelompok Al-Fatih</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-mono text-[#00236f]">2510614002</td>
                            <td class="px-4 py-2">Siti Aminah Zahra</td>
                            <td class="px-4 py-2 text-[#a53936]">MTs</td>
                            <td class="px-4 py-2">Kelompok Salahuddin</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-mono text-[#00236f]">2510614003</td>
                            <td class="px-4 py-2">Budi Setiawan</td>
                            <td class="px-4 py-2 text-[#a53936]">MA</td>
                            <td class="px-4 py-2">Kelompok Khalid bin Walid</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="text-xs text-[#64748B] mt-3">
                ⚠️ <span class="font-semibold">Penting:</span> Kolom C harus diisi dengan <span class="font-bold text-[#a53936]">MTs</span> atau <span class="font-bold text-[#00236f]">MA</span> (case sensitive!)
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('file-input');
        const chooseBtn = document.getElementById('choose-file-btn');
        const dropZone = document.getElementById('drop-zone');
        const fileInfo = document.getElementById('file-info');
        const fileName = document.getElementById('file-name');
        const fileSize = document.getElementById('file-size');
        const removeBtn = document.getElementById('remove-file');
        const submitBtn = document.getElementById('submit-btn');

        // Klik tombol pilih file
        chooseBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            fileInput.click();
        });

        // Klik drop zone
        dropZone.addEventListener('click', function() {
            fileInput.click();
        });

        // Drag & Drop
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-[#00236f]', 'bg-[#f7f9fb]');
        });

        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('border-[#00236f]', 'bg-[#f7f9fb]');
        });

        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-[#00236f]', 'bg-[#f7f9fb]');
            
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                updateFileInfo(fileInput.files[0]);
            }
        });

        // File selected
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                updateFileInfo(this.files[0]);
            }
        });

        // Update file info
        function updateFileInfo(file) {
            const validTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
                               'application/vnd.ms-excel', 
                               'text/csv'];
            const validExtensions = ['.xlsx', '.xls', '.csv'];
            const ext = '.' + file.name.split('.').pop().toLowerCase();
            
            if (!validExtensions.includes(ext)) {
                alert('❌ Format file tidak didukung! Gunakan .xlsx, .xls, atau .csv');
                fileInput.value = '';
                fileInfo.classList.add('hidden');
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                alert('❌ Ukuran file terlalu besar! Maksimal 5MB');
                fileInput.value = '';
                fileInfo.classList.add('hidden');
                return;
            }

            fileName.textContent = file.name;
            fileSize.textContent = '(' + (file.size / 1024).toFixed(2) + ' KB)';
            fileInfo.classList.remove('hidden');
            submitBtn.disabled = false;
        }

        // Remove file
        removeBtn.addEventListener('click', function() {
            fileInput.value = '';
            fileInfo.classList.add('hidden');
            submitBtn.disabled = true;
        });

        // Submit form loading state
        document.getElementById('import-form').addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <span class="material-symbols-outlined animate-spin">sync</span>
                Memproses...
            `;
        });
    });
</script>
@endpush
@endsection