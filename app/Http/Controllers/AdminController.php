<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Election;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use chillerlan\QRCode\{QRCode as QRCodePng, QROptions};
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AdminController extends Controller
{
    public function dashboard()
    {
        $elections = Election::withCount(['nominees', 'candidates', 'votes'])->get();
        return view('admin.dashboard', compact('elections'));
    }

    public function show(string $slug)
    {
        $election = Election::where('slug', $slug)
            ->withCount(['nominees', 'candidates', 'votes'])
            ->firstOrFail();
        $candidates = $election->candidates()->orderByDesc('votes')->get();

        return view('admin.election', compact('election', 'candidates'));
    }

    public function updatePhase(Request $request, string $slug)
    {
        $election = Election::where('slug', $slug)->firstOrFail();

        $request->validate([
            'phase' => 'required|in:nomination,voting,closed',
        ]);

        $newPhase = $request->phase;

        if ($newPhase === 'voting' && $election->candidates()->count() === 0) {
            return back()->with('error', 'Tidak boleh membuka pengundian tanpa calon. Sila tambah calon terlebih dahulu.');
        }

        $election->update(['phase' => $newPhase]);
        Cache::forget("results_{$slug}");

        $phaseLabels = [
            'nomination' => 'Pencalonan',
            'voting' => 'Pengundian',
            'closed' => 'Ditutup',
        ];

        return back()->with('success', "Fasa telah ditukar kepada: {$phaseLabels[$newPhase]}");
    }

    public function nominees(string $slug)
    {
        $election = Election::where('slug', $slug)->firstOrFail();
        $nominees = $election->nominees()
            ->selectRaw('name, COUNT(*) as count')
            ->groupBy('name')
            ->orderByDesc('count')
            ->get();
        $candidates = $election->candidates()->orderByDesc('votes')->get();

        return view('admin.nominees', compact('election', 'nominees', 'candidates'));
    }

    public function deleteNominee(Request $request, string $slug)
    {
        $election = Election::where('slug', $slug)->firstOrFail();

        $request->validate(['name' => 'required|string']);

        $election->nominees()->where('name', $request->name)->delete();

        return back()->with('success', "Pencalonan '{$request->name}' telah dipadam.");
    }

    public function syncCandidates(Request $request, string $slug)
    {
        $election = Election::where('slug', $slug)->firstOrFail();

        $request->validate([
            'names' => 'required|array|min:1',
            'names.*' => 'required|string|max:255',
        ]);

        foreach ($request->names as $name) {
            $name = trim($name);
            $nominationCount = $election->nominees()->where('name', $name)->count();

            Candidate::updateOrCreate(
                ['election_id' => $election->id, 'name' => $name],
                ['nomination_count' => $nominationCount]
            );
        }

        return back()->with('success', 'Calon telah dikemaskini.');
    }

    public function addCandidate(Request $request, string $slug)
    {
        $election = Election::where('slug', $slug)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Candidate::create([
            'election_id' => $election->id,
            'name' => trim($request->name),
            'nomination_count' => 0,
        ]);

        return back()->with('success', 'Calon berjaya ditambah.');
    }

    public function updateCandidate(Request $request, string $slug, int $id)
    {
        $election = Election::where('slug', $slug)->firstOrFail();
        $candidate = Candidate::where('id', $id)->where('election_id', $election->id)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $candidate->update(['name' => trim($request->name)]);
        Cache::forget("results_{$slug}");

        return back()->with('success', 'Nama calon dikemaskini.');
    }

    public function deleteCandidate(string $slug, int $id)
    {
        $election = Election::where('slug', $slug)->firstOrFail();
        $candidate = Candidate::where('id', $id)->where('election_id', $election->id)->firstOrFail();
        \App\Models\Vote::where('candidate_id', $candidate->id)->delete();
        $candidate->delete();
        Cache::forget("results_{$slug}");

        return back()->with('success', 'Calon telah dipadam.');
    }

    public function resetVotes(string $slug)
    {
        $election = Election::where('slug', $slug)->firstOrFail();

        $election->votes()->delete();
        $election->candidates()->update(['votes' => 0]);
        $election->update(['votes_reset_at' => now()]);
        Cache::forget("results_{$slug}");

        return back()->with('success', 'Semua undi telah direset.');
    }

    public function qrCode(string $slug)
    {
        $election = Election::where('slug', $slug)->firstOrFail();
        $url = route('election.show', $slug);
        $qrSvg = QrCode::size(300)->margin(2)->generate($url);

        return view('admin.qr', compact('election', 'qrSvg', 'url'));
    }

    public function qrPdf(string $slug)
    {
        $election = Election::where('slug', $slug)->firstOrFail();
        $url = route('election.show', $slug);

        $options = new QROptions([
            'outputType' => QRCodePng::OUTPUT_IMAGE_PNG,
            'scale' => 20,
            'imageTransparent' => false,
        ]);
        $qrBase64 = (new QRCodePng($options))->render($url);

        $pdf = Pdf::loadView('admin.qr-pdf', compact('election', 'qrBase64', 'url'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("QR-{$election->slug}.pdf");
    }
}
