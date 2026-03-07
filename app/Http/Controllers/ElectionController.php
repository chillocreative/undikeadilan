<?php

namespace App\Http\Controllers;

use App\Models\Election;
use Illuminate\Http\Request;

class ElectionController extends Controller
{
    public function home()
    {
        $elections = Election::all();
        return view('home', compact('elections'));
    }

    public function show(string $slug, Request $request)
    {
        $election = Election::where('slug', $slug)->firstOrFail();

        if ($election->phase === 'nomination') {
            $hasNominated = false;
            $cookieKey = "nomination_{$slug}_token";
            $token = $request->cookie($cookieKey);

            if ($token) {
                $hasNominated = $election->nominationLogs()
                    ->where('session_token', $token)
                    ->exists();
            }

            return view('election.nominate', compact('election', 'hasNominated'));
        }

        if ($election->phase === 'voting') {
            $hasVoted = false;
            $candidates = $election->candidates()->orderBy('sort_order')->get();
            return view('election.vote', compact('election', 'candidates', 'hasVoted'));
        }

        return redirect()->route('result.show', $slug);
    }
}
