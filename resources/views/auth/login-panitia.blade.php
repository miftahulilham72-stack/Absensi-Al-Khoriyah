@extends('layouts.guest')

@section('title', 'Login Panitia')

@section('content')
<div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8 border border-[#E2E8F0]">
    <div class="text-center mb-8">
        <div class="w-20 h-20 mx-auto mb-4 bg-[#10B981]/10 rounded-full flex items-center justify-center">
            <span class="material-symbols-outlined text-4xl text-[#10B981]">badge</span>
        </div>
        <h1 class="text-2xl font-bold text-[#00236f]">Login Panitia</h1>
        <p class="text-[#64748B] text-sm">Akses khusus untuk absensi peserta</p>
    </div>

    @if(session('error'))
        <div class="bg-[#ffdad6] text-[#93000a] p-3 rounded-lg mb-4">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.panitia') }}">
        @csrf
        <div class="space-y-4">
            <div>
                <label class="text-sm font-semibold text-[#444651]">Username / Email</label>
                <input type="text" name="username" value="{{ old('username') }}" 
                       class="w-full px-4 py-3 rounded-xl border border-[#c5c5d3] bg-white focus:ring-2 focus:ring-[#00236f] outline-none transition-all"
                       placeholder="Masukkan username atau email" required>
                @error('username')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label class="text-sm font-semibold text-[#444651]">Password</label>
                <div class="relative">
                    <input type="password" name="password" id="password-field"
                           class="w-full px-4 py-3 rounded-xl border border-[#c5c5d3] bg-white focus:ring-2 focus:ring-[#00236f] outline-none transition-all"
                           placeholder="Masukkan password" required>
                    <button type="button" id="toggle-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#64748B] hover:text-[#00236f] transition-colors">
                        <span class="material-symbols-outlined" id="password-icon" style="font-size:20px;">visibility_off</span>
                    </button>
                </div>
                @error('password')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="w-full bg-[#10B981] text-white py-4 rounded-xl font-semibold hover:bg-[#059669] transition-all active:scale-95">
                🚪 MASUK KIOSK
            </button>
        </div>
    </form>

    <div class="text-center mt-6">
        <p class="text-xs text-[#64748B]">
            🔒 Hanya untuk panitia yang bertugas di lapangan
        </p>
        <p class="text-xs text-[#64748B] mt-1">
            <a href="{{ route('login') }}" class="text-[#00236f] hover:underline">Login Admin →</a>
        </p>
    </div>
</div>

@push('scripts')
<script>
    // Toggle Password Visibility
    document.getElementById('toggle-password').addEventListener('click', function() {
        const passwordField = document.getElementById('password-field');
        const icon = document.getElementById('password-icon');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.textContent = 'visibility';
        } else {
            passwordField.type = 'password';
            icon.textContent = 'visibility_off';
        }
    });
</script>
@endpush
@endsection