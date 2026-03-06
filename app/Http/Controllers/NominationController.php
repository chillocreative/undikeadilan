<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Models\NominationLog;
use App\Models\Nominee;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NominationController extends Controller
{
    public function store(Request $request, string $slug)
    {
        $election = Election::where('slug', $slug)->where('phase', 'nomination')->firstOrFail();

        // Check if already nominated via cookie
        $cookieKey = "nomination_{$slug}_token";
        $existingToken = $request->cookie($cookieKey);
        if ($existingToken) {
            $alreadyNominated = $election->nominationLogs()
                ->where('session_token', $existingToken)
                ->exists();
            if ($alreadyNominated) {
                return back()->with('error', 'Anda telah menghantar pencalonan.');
            }
        }

        $request->validate([
            'names' => 'required|array|min:1|max:' . $election->max_nominations,
            'names.*' => 'nullable|string|max:255',
        ]);

        $names = collect($request->input('names'))
            ->filter(fn($name) => !empty(trim($name)))
            ->map(fn($name) => Str::title(trim($name)));

        if ($names->isEmpty()) {
            return back()->withErrors(['names' => 'Sila masukkan sekurang-kurangnya satu nama calon.']);
        }

        $token = Str::random(64);
        $ip = $request->ip();

        foreach ($names as $name) {
            Nominee::create([
                'election_id' => $election->id,
                'name' => $name,
                'submitted_by_ip' => $ip,
                'session_token' => $token,
            ]);
        }

        NominationLog::create([
            'election_id' => $election->id,
            'ip_address' => $ip,
            'session_token' => $token,
        ]);

        return back()
            ->with('success', 'Terima kasih! Pencalonan anda telah dihantar.')
            ->withCookie(cookie($cookieKey, $token, 60 * 24 * 30)); // 30 days
    }
}
