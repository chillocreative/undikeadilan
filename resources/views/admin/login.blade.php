@extends('layouts.app')
@section('title', 'Log Masuk Admin')

@section('content')
<div class="max-w-sm mx-auto mt-10 sm:mt-20">
    <div class="text-center mb-6">
        <img src="/logo-keadilan.png" alt="Logo" class="mx-auto w-20 sm:w-24 object-contain drop-shadow-2xl mb-4">
        <h1 class="text-xl font-bold text-white">Log Masuk Admin</h1>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="rounded-2xl bg-white/10 p-5 sm:p-6 shadow-xl backdrop-blur-md ring-1 ring-white/20">
            <div class="space-y-4">
                <div>
                    <label class="block text-sky-100 text-sm font-medium mb-1.5">E-mel</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 text-white placeholder-sky-300/30 focus:outline-none focus:ring-2 focus:ring-sky-400/50 text-sm">
                </div>
                <div>
                    <label class="block text-sky-100 text-sm font-medium mb-1.5">Kata Laluan</label>
                    <input type="password" name="password" required
                           class="w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 text-white placeholder-sky-300/30 focus:outline-none focus:ring-2 focus:ring-sky-400/50 text-sm">
                </div>
            </div>

            @if($errors->any())
            <div class="mt-3 text-rose-300 text-sm">{{ $errors->first() }}</div>
            @endif

            <button type="submit" class="mt-5 w-full rounded-xl bg-sky-600 hover:bg-sky-500 px-6 py-3 text-white font-semibold text-sm shadow-lg transition-all active:scale-[0.98]">
                Log Masuk
            </button>
        </div>
    </form>
</div>
@endsection
