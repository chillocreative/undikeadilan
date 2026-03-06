<?php

namespace App\Http\Controllers;

use App\Models\Election;
use Illuminate\Support\Facades\Cache;

class ResultController extends Controller
{
    public function show(string $slug)
    {
        $election = Election::where('slug', $slug)->firstOrFail();
        $candidates = $election->candidates()->orderByDesc('votes')->get();
        $totalVotes = $candidates->sum('votes');

        $candidatesJson = $candidates->map(function ($c, $i) use ($totalVotes, $election) {
            $percentage = $totalVotes > 0 ? round(($c->votes / $totalVotes) * 100, 1) : 0;
            $label = null;
            if ($election->phase === 'closed' && $i < $election->max_winners && $election->winner_labels) {
                $label = $election->winner_labels[$i] ?? null;
            }
            return [
                'id' => $c->id,
                'name' => $c->name,
                'votes' => $c->votes,
                'percentage' => $percentage,
                'label' => $label,
            ];
        })->values();

        return view('election.results', compact('election', 'candidates', 'totalVotes', 'candidatesJson'));
    }

    public function data(string $slug)
    {
        return Cache::remember("results_{$slug}", 2, function () use ($slug) {
            $election = Election::where('slug', $slug)->firstOrFail();
            $candidates = $election->candidates()->orderByDesc('votes')->get();
            $totalVotes = $candidates->sum('votes');

            return response()->json([
                'phase' => $election->phase,
                'total_votes' => $totalVotes,
                'candidates' => $candidates->map(function ($c, $index) use ($totalVotes, $election) {
                    $percentage = $totalVotes > 0 ? round(($c->votes / $totalVotes) * 100, 1) : 0;
                    $label = null;
                    if ($election->phase === 'closed' && $index < $election->max_winners && $election->winner_labels) {
                        $label = $election->winner_labels[$index] ?? null;
                    }

                    return [
                        'id' => $c->id,
                        'name' => $c->name,
                        'votes' => $c->votes,
                        'percentage' => $percentage,
                        'label' => $label,
                    ];
                }),
            ]);
        });
    }
}
