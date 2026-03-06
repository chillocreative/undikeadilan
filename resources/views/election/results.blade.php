@extends('layouts.app')
@section('title', 'Keputusan - ' . $election->title)

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="max-w-3xl mx-auto" x-data="liveResults()" x-init="startPolling()">
    <div class="text-center mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-white mb-2">{{ $election->title }}</h1>
        <div class="flex items-center justify-center gap-2">
            @if($election->phase === 'voting')
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-400/20 text-emerald-300 border border-emerald-400/30">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Pengundian Sedang Berjalan
            </span>
            @elseif($election->phase === 'closed')
            <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-rose-400/20 text-rose-300 border border-rose-400/30">
                Keputusan Rasmi
            </span>
            @endif
        </div>
    </div>

    {{-- Total votes --}}
    <div class="text-center mb-6">
        <span class="text-sky-200/60 text-sm">Jumlah Undi:</span>
        <span class="text-white font-bold text-2xl ml-2" x-text="totalVotes">{{ $totalVotes }}</span>
    </div>

    {{-- Chart --}}
    <div class="rounded-2xl bg-white/10 p-4 sm:p-6 shadow-xl backdrop-blur-md ring-1 ring-white/20 mb-6">
        <canvas id="resultsChart" height="250"></canvas>
    </div>

    {{-- Results table --}}
    <div class="rounded-2xl bg-white/10 shadow-xl backdrop-blur-md ring-1 ring-white/20 overflow-hidden">
        <template x-for="(candidate, index) in candidates" :key="candidate.id">
            <div class="flex items-center gap-3 sm:gap-4 px-4 sm:px-6 py-3 sm:py-4 border-b border-white/10 last:border-0">
                {{-- Rank --}}
                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold"
                     :class="index === 0 ? 'bg-amber-400/20 text-amber-300' : (index === 1 ? 'bg-slate-300/20 text-slate-300' : (index === 2 ? 'bg-orange-400/20 text-orange-300' : 'bg-white/10 text-sky-200/60'))">
                    <span x-text="index + 1"></span>
                </div>

                {{-- Name and label --}}
                <div class="flex-1 min-w-0">
                    <div class="text-white font-medium text-sm sm:text-base truncate" x-text="candidate.name"></div>
                    <div x-show="candidate.label" class="text-xs font-medium mt-0.5" :class="index === 0 ? 'text-amber-300' : 'text-sky-300'" x-text="candidate.label"></div>
                </div>

                {{-- Vote count and percentage --}}
                <div class="flex-shrink-0 text-right">
                    <div class="text-white font-semibold text-sm sm:text-base" x-text="candidate.votes + ' undi'"></div>
                    <div class="text-sky-200/60 text-xs" x-text="candidate.percentage + '%'"></div>
                </div>
            </div>
        </template>

        <div x-show="candidates.length === 0" class="px-6 py-8 text-center text-sky-200/40 text-sm">
            Belum ada keputusan.
        </div>
    </div>

    {{-- Links --}}
    <div class="mt-6 text-center flex flex-wrap justify-center gap-4">
        @if($election->phase === 'voting')
        <a href="{{ route('election.show', $election->slug) }}" class="text-sky-300/60 hover:text-sky-200 text-sm">Kembali ke Pengundian</a>
        @endif
        <a href="{{ route('home') }}" class="text-sky-300/60 hover:text-sky-200 text-sm">Halaman Utama</a>
    </div>
</div>

@push('scripts')
<script>
function liveResults() {
    return {
        candidates: @json($candidatesJson),
        totalVotes: {{ $totalVotes }},
        chart: null,
        polling: null,

        startPolling() {
            this.initChart();
            @if($election->phase !== 'closed')
            this.polling = setInterval(() => this.fetchResults(), 3000);
            @endif
        },

        initChart() {
            const ctx = document.getElementById('resultsChart').getContext('2d');
            this.chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: this.candidates.map(c => c.name),
                    datasets: [{
                        label: 'Undi',
                        data: this.candidates.map(c => c.votes),
                        backgroundColor: this.candidates.map((_, i) => {
                            const colors = [
                                'rgba(251, 191, 36, 0.6)', 'rgba(148, 163, 184, 0.6)',
                                'rgba(251, 146, 60, 0.6)', 'rgba(56, 189, 248, 0.4)',
                                'rgba(129, 140, 248, 0.4)', 'rgba(52, 211, 153, 0.4)',
                                'rgba(251, 113, 133, 0.4)', 'rgba(167, 139, 250, 0.4)',
                            ];
                            return colors[i % colors.length];
                        }),
                        borderColor: this.candidates.map((_, i) => {
                            const colors = [
                                'rgba(251, 191, 36, 1)', 'rgba(148, 163, 184, 1)',
                                'rgba(251, 146, 60, 1)', 'rgba(56, 189, 248, 0.8)',
                                'rgba(129, 140, 248, 0.8)', 'rgba(52, 211, 153, 0.8)',
                                'rgba(251, 113, 133, 0.8)', 'rgba(167, 139, 250, 0.8)',
                            ];
                            return colors[i % colors.length];
                        }),
                        borderWidth: 1,
                        borderRadius: 6,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { color: 'rgba(186, 230, 253, 0.6)', stepSize: 1 },
                            grid: { color: 'rgba(255,255,255,0.05)' },
                        },
                        y: {
                            ticks: { color: 'rgba(255,255,255,0.8)', font: { size: 13 } },
                            grid: { display: false },
                        },
                    },
                },
            });
        },

        async fetchResults() {
            try {
                const res = await fetch('{{ route("result.data", $election->slug) }}');
                const data = await res.json();
                this.candidates = data.candidates;
                this.totalVotes = data.total_votes;
                this.updateChart();

                if (data.phase === 'closed' && this.polling) {
                    clearInterval(this.polling);
                }
            } catch (e) {
                console.error('Polling error:', e);
            }
        },

        updateChart() {
            if (!this.chart) return;
            this.chart.data.labels = this.candidates.map(c => c.name);
            this.chart.data.datasets[0].data = this.candidates.map(c => c.votes);
            this.chart.update('none');
        }
    }
}
</script>
@endpush
@endsection
