<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VoteController extends Controller
{
    public function store(Request $request, string $slug)
    {
        $election = Election::where('slug', $slug)->where('phase', 'voting')->firstOrFail();

        // Check if already voted via cookie
        $cookieKey = "vote_{$slug}_token";
        $existingToken = $request->cookie($cookieKey);
        if ($existingToken) {
            $existingVote = $election->votes()->where('voter_token', $existingToken)->first();
            if ($existingVote && (!$election->votes_reset_at || $existingVote->created_at->gt($election->votes_reset_at))) {
                return response()->json(['error' => 'Anda telah mengundi.'], 422);
            }
        }

        $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
        ]);

        $candidate = Candidate::where('id', $request->candidate_id)
            ->where('election_id', $election->id)
            ->firstOrFail();

        $token = Str::random(64);

        DB::transaction(function () use ($election, $candidate, $request, $token) {
            Vote::create([
                'election_id' => $election->id,
                'candidate_id' => $candidate->id,
                'voter_ip' => $request->ip(),
                'voter_token' => $token,
            ]);

            $candidate->increment('votes');
        });

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih! Undi anda telah direkodkan.',
        ])->withCookie(cookie($cookieKey, $token, 60 * 24 * 30));
    }
}
