@extends('layouts.guest')

@section('title', 'Lupa Password')

@section('content')
<div class="w-full max-w-md bg-surface-container-lowest rounded-2xl shadow-lg p-8 border border-table-border">
    <div class="text-center mb-8">
        <div class="w-20 h-20 mx-auto mb-4 bg-primary/10 rounded-full flex items-center justify-center">
            <span class="material-symbols-outlined text-4xl text-primary">lock_reset</span>
        </div>
        <h1 class="font-headline-md text-headline-md text-primary">Lupa Password</h1>
        <p class="text-text-muted font-body-sm mt-1">Masukkan email admin untuk reset password</p>
    </div>

    @if(session('status'))
        <div class="bg-status-success/10 text-status-success p-3 rounded-lg mb-4">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div>
            <label class="font-body-sm font-semibold text-on-surface-variant">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" 
                class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-white focus:ring-2 focus:ring-primary outline-none @error('email') border-error @enderror" 
                placeholder="admin@alkhoeriyah.sch.id" required>
            @error('email')
                <span class="text-error text-sm">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit" class="w-full mt-4 bg-primary text-on-primary py-4 rounded-xl font-button-text text-button-text hover:bg-primary/90 transition-all active:scale-95">
            Kirim Link Reset
        </button>
    </form>

    <div class="text-center mt-6">
        <a href="{{ route('login') }}" class="text-text-muted text-sm hover:text-primary transition-colors">
            ← Kembali ke Login
        </a>
    </div>
</div>
@endsection