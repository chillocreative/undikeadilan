@extends('layouts.app')
@section('title', $election->title)

@section('content')
<div class="max-w-lg mx-auto">
    <div class="text-center mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-white mb-2">{{ $election->title }}</h1>
        <p class="text-sky-200/60 text-sm">Sila masukkan nama calon yang anda ingin calonkan</p>
    </div>

    @if(session('success'))
        <div class="rounded-2xl bg-emerald-500/20 border border-emerald-400/30 p-6 text-center">
            <svg class="mx-auto h-12 w-12 text-emerald-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <p class="text-emerald-200 font-medium">{{ session('success') }}</p>
            <a href="{{ route('home') }}" class="mt-4 w-full inline-block text-center rounded-xl bg-sky-600 hover:bg-sky-500 px-6 py-3.5 text-white font-semibold shadow-lg transition-all">Kembali ke halaman utama</a>
        </div>
    @elseif($hasNominated)
        <div class="rounded-2xl bg-sky-500/20 border border-sky-400/30 p-6 text-center">
            <svg class="mx-auto h-12 w-12 text-sky-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sky-200 font-medium">Anda telah menghantar pencalonan.</p>
            <a href="{{ route('home') }}" class="mt-4 w-full inline-block text-center rounded-xl bg-sky-600 hover:bg-sky-500 px-6 py-3.5 text-white font-semibold shadow-lg transition-all">Kembali ke halaman utama</a>
        </div>
    @else
        <form method="POST" action="{{ route('nomination.store', $election->slug) }}"
              x-data="{ names: Array({{ $election->max_nominations }}).fill(''), submitting: false }"
              @submit.prevent="submitting = true; $el.submit()">
            @csrf

            <div class="rounded-2xl bg-white/10 p-5 sm:p-6 shadow-xl backdrop-blur-md ring-1 ring-white/20">
                <div class="space-y-4">
                    @for($i = 0; $i < $election->max_nominations; $i++)
                    <div>
                        <label class="block text-sky-100 text-sm font-medium mb-1.5">
                            Nama Calon {{ $i + 1 }}
                        </label>
                        <input type="text"
                               name="names[]"
                               x-model="names[{{ $i }}]"
                               placeholder="Masukkan nama penuh"
                               class="w-full rounded-xl bg-white/10 border border-white/15 px-4 py-3 text-white placeholder-sky-300/30 focus:outline-none focus:ring-2 focus:ring-sky-400/50 focus:border-transparent text-sm sm:text-base">
                    </div>
                    @endfor
                </div>

                @if($errors->any())
                <div class="mt-4 text-rose-300 text-sm">
                    {{ $errors->first() }}
                </div>
                @endif

                <button type="submit"
                        :disabled="submitting || names.every(n => !n.trim())"
                        :class="submitting || names.every(n => !n.trim()) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-sky-500 active:scale-[0.98]'"
                        class="mt-6 w-full rounded-xl bg-sky-600 px-6 py-3.5 text-white font-semibold text-sm sm:text-base shadow-lg transition-all">
                    <span x-show="!submitting">Hantar Pencalonan</span>
                    <span x-show="submitting">Menghantar...</span>
                </button>
            </div>
        </form>
    @endif

    <div class="mt-6 text-center">
        <a href="{{ route('home') }}" class="text-sky-300/60 hover:text-sky-200 text-sm">Kembali ke halaman utama</a>
    </div>
</div>
@endsection
