@extends('layouts.admin')
@section('title', 'Pencalonan - ' . $election->title)

@section('breadcrumb')
<a href="{{ route('admin.election', $election->slug) }}" class="hover:text-white">{{ $election->title }}</a>
<span>/</span>
<span class="text-sky-100">Pencalonan</span>
@endsection

@section('admin-content')
<h1 class="text-xl sm:text-2xl font-bold text-white mb-6">Senarai Pencalonan</h1>

<div class="grid gap-6 lg:grid-cols-2">
    {{-- Nominees list --}}
    <div class="rounded-2xl bg-white/10 p-5 sm:p-6 shadow-xl backdrop-blur-md ring-1 ring-white/20">
        <h2 class="text-white font-semibold mb-4">Nama Dicalonkan ({{ $nominees->count() }} nama unik)</h2>

        @if($nominees->count())
        <form method="POST" action="{{ route('admin.candidates.sync', $election->slug) }}" x-data="{ selected: [] }">
            @csrf
            <div class="space-y-2 mb-4 max-h-96 overflow-y-auto">
                @foreach($nominees as $nominee)
                <div class="flex items-center gap-2 rounded-xl bg-white/5 px-4 py-3 ring-1 ring-white/10">
                    <label class="flex items-center gap-3 flex-1 cursor-pointer hover:bg-white/10 transition-colors rounded-lg -m-1 p-1">
                        <input type="checkbox" name="names[]" value="{{ $nominee->name }}"
                               x-model="selected"
                               class="rounded border-white/30 bg-white/10 text-sky-500 focus:ring-sky-400/50">
                        <span class="flex-1 text-white text-sm">{{ $nominee->name }}</span>
                        <span class="text-sky-200/60 text-xs bg-sky-400/10 px-2 py-0.5 rounded-full">{{ $nominee->count }}x dicalonkan</span>
                    </label>
                    <button type="button"
                            @click="if(confirm('Padam pencalonan {{ $nominee->name }}?')) {
                                fetch('{{ route('admin.nominee.delete', $election->slug) }}', {
                                    method: 'DELETE',
                                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json'},
                                    body: JSON.stringify({name: '{{ $nominee->name }}'})
                                }).then(() => location.reload())
                            }"
                            class="text-rose-400/60 hover:text-rose-300 transition-colors p-1" title="Padam">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
                @endforeach
            </div>

            <div class="flex items-center justify-between">
                <span class="text-sky-200/40 text-xs" x-text="selected.length + ' dipilih'"></span>
                <button type="submit"
                        :disabled="selected.length === 0"
                        :class="selected.length === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-emerald-500'"
                        class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm font-medium transition-colors">
                    Promosi ke Calon
                </button>
            </div>
        </form>
        @else
        <p class="text-sky-200/40 text-sm">Belum ada pencalonan diterima.</p>
        @endif
    </div>

    {{-- Current candidates --}}
    <div class="rounded-2xl bg-white/10 p-5 sm:p-6 shadow-xl backdrop-blur-md ring-1 ring-white/20">
        <h2 class="text-white font-semibold mb-4">Calon Semasa ({{ $candidates->count() }})</h2>

        @if($candidates->count())
        <div class="space-y-2">
            @foreach($candidates as $i => $candidate)
            <div class="flex items-center gap-3 rounded-xl bg-white/5 px-4 py-3 ring-1 ring-white/10">
                <span class="text-sky-200/60 text-sm font-medium w-6">{{ $i + 1 }}.</span>
                <span class="flex-1 text-white text-sm">{{ $candidate->name }}</span>
                <span class="text-sky-200/40 text-xs">{{ $candidate->nomination_count }}x</span>
                <span class="text-sky-200/60 text-xs">{{ $candidate->votes }} undi</span>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-sky-200/40 text-sm">Belum ada calon. Pilih dari senarai pencalonan di sebelah kiri.</p>
        @endif
    </div>
</div>
@endsection
