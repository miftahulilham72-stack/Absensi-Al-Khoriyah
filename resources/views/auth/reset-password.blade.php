@extends('layouts.guest')

@section('title', 'Reset Password')

@section('content')
<div class="w-full max-w-md bg-surface-container-lowest rounded-2xl shadow-lg p-8 border border-table-border">
    <div class="text-center mb-8">
        <h1 class="font-headline-md text-headline-md text-primary">Reset Password</h1>
        <p class="text-text-muted font-body-sm mt-1">Buat password baru untuk akun admin</p>
    </div>

    @if(session('error'))
        <div class="bg-error-container text-on-error-container p-3 rounded-lg mb-4">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="space-y-4">
            <div>
                <label class="font-body-sm font-semibold text-on-surface-variant">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" 
                    class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-white focus:ring-2 focus:ring-primary outline-none @error('email') border-error @enderror" 
                    required>
                @error('email')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label class="font-body-sm font-semibold text-on-surface-variant">Password Baru</label>
                <input type="password" name="password" 
                    class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-white focus:ring-2 focus:ring-primary outline-none @error('password') border-error @enderror" 
                    required>
                @error('password')
                    <span class="text-error text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label class="font-body-sm font-semibold text-on-surface-variant">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" 
                    class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-white focus:ring-2 focus:ring-primary outline-none" 
                    required>
            </div>
            <button type="submit" class="w-full bg-secondary text-on-secondary py-4 rounded-xl font-button-text text-button-text hover:bg-on-secondary-fixed-variant transition-all active:scale-95">
                Reset Password
            </button>
        </div>
    </form>
</div>
@endsection