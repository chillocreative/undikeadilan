@extends('layouts.admin')
@section('title', 'Urus - ' . $election->title)

@section('breadcrumb')
<span class="text-sky-100">{{ $election->title }}</span>
@endsection

@section('admin-content')
<h1 class="text-xl sm:text-2xl font-bold text-white mb-6">{{ $election->title }}</h1>

{{-- Phase Control --}}
<div class="rounded-2xl bg-white/10 p-5 sm:p-6 shadow-xl backdrop-blur-md ring-1 ring-white/20 mb-6">
    <h2 class="text-white font-semibold mb-4">Kawalan Fasa</h2>
    <div class="flex flex-wrap gap-3">
        @foreach(['nomination' => 'Pencalonan', 'voting' => 'Pengundian', 'closed' => 'Tutup'] as $phase => $label)
        <form method="POST" action="{{ route('admin.phase', $election->slug) }}" class="inline">
            @csrf
            <input type="hidden" name="phase" value="{{ $phase }}">
            <button type="submit"
                    @if($election->phase === $phase) disabled @endif
                    class="px-5 py-2.5 rounded-xl text-sm font-medium transition-all
                    {{ $election->phase === $phase
                        ? 'bg-sky-500 text-white cursor-default'
                        : 'bg-white/10 text-sky-100 hover:bg-white/20' }}">
                {{ $label }}
            </button>
        </form>
        @endforeach
    </div>
    <p class="text-sky-200/40 text-xs mt-3">
        Fasa semasa: <strong class="text-sky-200">{{ ['nomination' => 'Pencalonan', 'voting' => 'Pengundian', 'closed' => 'Ditutup'][$election->phase] }}</strong>
    </p>
</div>

{{-- Candidates --}}
<div class="rounded-2xl bg-white/10 p-5 sm:p-6 shadow-xl backdrop-blur-md ring-1 ring-white/20 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-white font-semibold">Senarai Calon ({{ $candidates->count() }})</h2>
        <a href="{{ route('admin.nominees', $election->slug) }}" class="text-sky-300 hover:text-sky-200 text-xs">Lihat Pencalonan</a>
    </div>

    @if($candidates->count())
    <div class="space-y-2 mb-4">
        @foreach($candidates as $i => $candidate)
        <div class="flex items-center gap-3 rounded-xl bg-white/5 px-4 py-3 ring-1 ring-white/10" x-data="{ editing: false }">
            <span class="text-sky-200/60 text-sm font-medium w-6">{{ $i + 1 }}.</span>
            <div class="flex-1 min-w-0">
                <span x-show="!editing" class="text-white text-sm">{{ $candidate->name }}</span>
                <form x-show="editing" method="POST" action="{{ route('admin.candidate.update', [$election->slug, $candidate->id]) }}" class="flex gap-2" style="display:none;">
                    @csrf
                    @method('PUT')
                    <input type="text" name="name" value="{{ $candidate->name }}" class="flex-1 rounded-lg bg-white/10 border border-white/15 px-3 py-1.5 text-white text-sm focus:outline-none focus:ring-2 focus:ring-sky-400/50">
                    <button type="submit" class="text-emerald-300 hover:text-emerald-200 text-xs">Simpan</button>
                    <button type="button" @click="editing = false" class="text-sky-300/60 hover:text-sky-200 text-xs">Batal</button>
                </form>
            </div>
            <span class="text-sky-200/60 text-xs">{{ $candidate->votes }} undi</span>
            <button @click="editing = !editing" x-show="!editing" class="text-sky-300/60 hover:text-sky-200 text-xs">Edit</button>
            <form method="POST" action="{{ route('admin.candidate.delete', [$election->slug, $candidate->id]) }}" onsubmit="return confirm('Padam calon ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-rose-300/60 hover:text-rose-300 text-xs">Padam</button>
            </form>
        </div>
        @endforeach
    </div>
    @else
    <p class="text-sky-200/40 text-sm mb-4">Belum ada calon. Pergi ke <a href="{{ route('admin.nominees', $election->slug) }}" class="text-sky-300 underline">halaman pencalonan</a> untuk menambah calon.</p>
    @endif

    {{-- Add candidate manually --}}
    <form method="POST" action="{{ route('admin.candidate.add', $election->slug) }}" class="flex gap-2">
        @csrf
        <input type="text" name="name" placeholder="Tambah calon secara manual..." required
               class="flex-1 rounded-xl bg-white/10 border border-white/15 px-4 py-2.5 text-white placeholder-sky-300/30 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400/50">
        <button type="submit" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-medium transition-colors">
            Tambah
        </button>
    </form>
</div>

{{-- Reset Votes --}}
<div class="rounded-2xl bg-white/10 p-5 sm:p-6 shadow-xl backdrop-blur-md ring-1 ring-white/20">
    <h2 class="text-white font-semibold mb-3">Reset Undi</h2>
    <p class="text-sky-200/60 text-sm mb-4">Ini akan memadam semua undi dan menetapkan semula kiraan undi kepada sifar.</p>
    <form method="POST" action="{{ route('admin.reset', $election->slug) }}" onsubmit="return confirm('AMARAN: Semua undi akan dipadam. Tindakan ini tidak boleh dibatalkan. Teruskan?')">
        @csrf
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-sm font-medium transition-colors">
            Reset Semua Undi
        </button>
    </form>
</div>

{{-- Quick Links --}}
<div class="mt-6 flex flex-wrap gap-3">
    <a href="{{ route('admin.qr', $election->slug) }}" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-sky-100 text-sm transition-colors">QR Code</a>
    <a href="{{ route('result.show', $election->slug) }}" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-sky-100 text-sm transition-colors" target="_blank">Lihat Keputusan</a>
    <a href="{{ route('election.show', $election->slug) }}" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-sky-100 text-sm transition-colors" target="_blank">Lihat Borang</a>
</div>
@endsection
