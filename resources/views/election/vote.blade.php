@extends('layouts.app')
@section('title', 'Undi - ' . $election->title)

@section('content')
<div class="max-w-2xl mx-auto" x-data="votingForm()">
    <div class="text-center mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-white mb-2">{{ $election->title }}</h1>
        <p class="text-sky-200/60 text-sm">Pilih <strong class="text-sky-200">1</strong> calon pilihan anda</p>
    </div>

    @if($hasVoted)
        <div class="rounded-2xl bg-sky-500/20 border border-sky-400/30 p-6 text-center">
            <svg class="mx-auto h-12 w-12 text-sky-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sky-200 font-medium mb-3">Anda telah mengundi.</p>
            <a href="{{ route('result.show', $election->slug) }}" class="inline-block px-5 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-sm font-medium transition-colors">
                Lihat Keputusan
            </a>
            <a href="{{ route('home') }}" class="mt-4 w-full inline-block text-center rounded-xl bg-sky-600 hover:bg-sky-500 px-6 py-3.5 text-white font-semibold shadow-lg transition-all">Kembali ke halaman utama</a>
        </div>
    @else
        {{-- Candidate cards --}}
        <div class="grid gap-3 sm:grid-cols-2">
            @foreach($candidates as $candidate)
            <button type="button"
                    @click="selected = {{ $candidate->id }}"
                    :class="selected === {{ $candidate->id }} ? 'ring-2 ring-sky-400 bg-sky-500/20' : 'bg-white/10 hover:bg-white/15'"
                    class="rounded-2xl p-4 sm:p-5 shadow-lg backdrop-blur-md ring-1 ring-white/20 transition-all text-left cursor-pointer active:scale-[0.98]">
                <div class="flex items-center gap-3">
                    <div :class="selected === {{ $candidate->id }} ? 'bg-sky-400 border-sky-400' : 'border-white/30'"
                         class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all flex-shrink-0">
                        <div x-show="selected === {{ $candidate->id }}" class="w-2 h-2 rounded-full bg-white"></div>
                    </div>
                    <span class="text-white font-medium text-sm sm:text-base">{{ $candidate->name }}</span>
                </div>
            </button>
            @endforeach
        </div>

        {{-- Submit --}}
        <div class="mt-6">
            <button @click="submitVote()"
                    :disabled="!selected || submitting"
                    :class="!selected || submitting ? 'opacity-50 cursor-not-allowed' : 'hover:bg-sky-500 active:scale-[0.98]'"
                    class="w-full rounded-xl bg-sky-600 px-6 py-3.5 text-white font-semibold text-sm sm:text-base shadow-lg transition-all">
                <span x-show="!submitting">Hantar Undi</span>
                <span x-show="submitting">Menghantar...</span>
            </button>
        </div>

        {{-- Success message (shown after voting) --}}
        <div x-show="voted" x-transition class="mt-4 rounded-2xl bg-emerald-500/20 border border-emerald-400/30 p-6 text-center" style="display: none;">
            <svg class="mx-auto h-12 w-12 text-emerald-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <p class="text-emerald-200 font-medium mb-3" x-text="successMessage"></p>
            <a href="{{ route('result.show', $election->slug) }}" class="inline-block px-5 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-sm font-medium transition-colors">
                Lihat Keputusan
            </a>
            <a href="{{ route('home') }}" class="mt-4 w-full inline-block text-center rounded-xl bg-sky-600 hover:bg-sky-500 px-6 py-3.5 text-white font-semibold shadow-lg transition-all">Kembali ke halaman utama</a>
        </div>

        {{-- Error message --}}
        <div x-show="errorMessage" x-transition class="mt-4 rounded-xl bg-rose-500/20 border border-rose-400/30 px-4 py-3 text-rose-200 text-sm" style="display: none;">
            <span x-text="errorMessage"></span>
        </div>
    @endif

    <div class="mt-6 text-center flex flex-wrap justify-center gap-4">
        <a href="{{ route('result.show', $election->slug) }}" class="text-sky-300/60 hover:text-sky-200 text-sm">Lihat Keputusan</a>
        <a href="{{ route('home') }}" class="text-sky-300/60 hover:text-sky-200 text-sm">Halaman Utama</a>
    </div>
</div>

@push('scripts')
<script>
function votingForm() {
    return {
        selected: null,
        submitting: false,
        voted: false,
        successMessage: '',
        errorMessage: '',

        async submitVote() {
            if (!this.selected || this.submitting) return;

            this.submitting = true;
            this.errorMessage = '';

            try {
                const res = await fetch('{{ route("vote.store", $election->slug) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ candidate_id: this.selected }),
                });

                const data = await res.json();

                if (res.ok) {
                    this.voted = true;
                    this.successMessage = data.message;
                    // Reset form after 2 seconds so user can vote again
                    setTimeout(() => {
                        this.voted = false;
                        this.selected = null;
                        this.successMessage = '';
                    }, 2000);
                } else {
                    this.errorMessage = data.error || 'Ralat berlaku. Sila cuba lagi.';
                }
            } catch (e) {
                this.errorMessage = 'Ralat sambungan. Sila cuba lagi.';
            } finally {
                this.submitting = false;
            }
        }
    }
}
</script>
@endpush
@endsection
