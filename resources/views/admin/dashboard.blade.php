@extends('layouts.admin')
@section('title', 'Dashboard Admin')

@section('admin-content')
<h1 class="text-2xl font-bold text-white mb-6">Dashboard</h1>

<div class="grid gap-4 sm:grid-cols-2">
    @foreach($elections as $election)
    <div class="rounded-2xl bg-white/10 p-5 sm:p-6 shadow-xl backdrop-blur-md ring-1 ring-white/20">
        <h2 class="text-white font-semibold text-base sm:text-lg mb-3">{{ $election->title }}</h2>

        @php
            $phaseConfig = [
                'nomination' => ['label' => 'Pencalonan', 'bg' => 'bg-emerald-400/20', 'text' => 'text-emerald-300', 'border' => 'border-emerald-400/30'],
                'voting' => ['label' => 'Pengundian', 'bg' => 'bg-sky-400/20', 'text' => 'text-sky-300', 'border' => 'border-sky-400/30'],
                'closed' => ['label' => 'Ditutup', 'bg' => 'bg-rose-400/20', 'text' => 'text-rose-300', 'border' => 'border-rose-400/30'],
            ];
            $phase = $phaseConfig[$election->phase];
        @endphp

        <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $phase['bg'] }} {{ $phase['text'] }} border {{ $phase['border'] }} mb-4">
            {{ $phase['label'] }}
        </span>

        <div class="grid grid-cols-3 gap-3 mb-4">
            <div class="text-center">
                <div class="text-white font-bold text-lg">{{ $election->nominees_count }}</div>
                <div class="text-sky-200/60 text-xs">Pencalonan</div>
            </div>
            <div class="text-center">
                <div class="text-white font-bold text-lg">{{ $election->candidates_count }}</div>
                <div class="text-sky-200/60 text-xs">Calon</div>
            </div>
            <div class="text-center">
                <div class="text-white font-bold text-lg">{{ $election->votes_count }}</div>
                <div class="text-sky-200/60 text-xs">Undi</div>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.election', $election->slug) }}" class="inline-block px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-medium transition-colors">
                Urus
            </a>
            <a href="{{ route('admin.nominees', $election->slug) }}" class="inline-block px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-sky-100 text-xs font-medium transition-colors">
                Pencalonan
            </a>
            <a href="{{ route('admin.qr', $election->slug) }}" class="inline-block px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-sky-100 text-xs font-medium transition-colors">
                QR Code
            </a>
            <a href="{{ route('result.show', $election->slug) }}" class="inline-block px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-sky-100 text-xs font-medium transition-colors" target="_blank">
                Keputusan
            </a>
        </div>
    </div>
    @endforeach
</div>
@endsection
