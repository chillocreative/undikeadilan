@extends('layouts.app')
@section('title', 'UNDIKEADILAN - Sistem Pengundian')

@section('content')
<div class="text-center mb-8 sm:mb-12">
    <img src="/logo-keadilan.png" alt="Logo Keadilan" class="mx-auto w-24 sm:w-32 object-contain drop-shadow-2xl mb-4">
    <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">Sistem Pengundian</h1>
    <p class="text-sky-200/60 text-sm sm:text-base">Parti Keadilan Rakyat</p>
</div>

<div class="grid gap-4 sm:gap-6 sm:grid-cols-2 max-w-3xl mx-auto">
    @foreach($elections as $election)
    <a href="{{ route('election.show', $election->slug) }}"
       class="block rounded-2xl bg-white/10 p-5 sm:p-6 shadow-xl backdrop-blur-md ring-1 ring-white/20 hover:bg-white/15 hover:ring-white/30 transition-all group">
        <h2 class="text-white font-semibold text-base sm:text-lg mb-2 group-hover:text-sky-200 transition-colors">
            {{ $election->title }}
        </h2>
        <p class="text-sky-200/60 text-sm mb-4">{{ $election->description }}</p>

        @php
            $phaseConfig = [
                'nomination' => ['label' => 'Pencalonan Dibuka', 'bg' => 'bg-emerald-400/20', 'text' => 'text-emerald-300', 'border' => 'border-emerald-400/30'],
                'voting' => ['label' => 'Pengundian Dibuka', 'bg' => 'bg-sky-400/20', 'text' => 'text-sky-300', 'border' => 'border-sky-400/30'],
                'closed' => ['label' => 'Ditutup', 'bg' => 'bg-rose-400/20', 'text' => 'text-rose-300', 'border' => 'border-rose-400/30'],
            ];
            $phase = $phaseConfig[$election->phase];
        @endphp

        <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $phase['bg'] }} {{ $phase['text'] }} border {{ $phase['border'] }}">
            {{ $phase['label'] }}
        </span>
    </a>
    @endforeach
</div>
@endsection
