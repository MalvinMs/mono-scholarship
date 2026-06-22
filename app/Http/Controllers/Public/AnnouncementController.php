<?php

namespace App\Http\Controllers\Public;

use App\Models\Application;
use App\Models\ApplicationScore;
use App\Models\Scholarship;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AnnouncementController extends Controller
{
    public function list()
    {
        $scholarships = Scholarship::where('status', 'announced')
            ->orderByDesc('updated_at')
            ->get(['id', 'name', 'slug', 'description', 'updated_at']);

        return view('public.announcement-list', ['scholarships' => $scholarships]);
    }

    public function index(Scholarship $scholarship)
    {
        if ($scholarship->status !== 'announced') {
            abort(404);
        }

        $results = ApplicationScore::with('application.user')
            ->where('scholarship_id', $scholarship->id)
            ->whereNotNull('selection_result')
            ->whereNotNull('rank')
            ->orderBy('rank')
            ->get()
            ->map(fn($score) => [
                'name' => $score->application?->user?->name ?? '-',
                'registration_number' => $score->application?->registration_number ?? '-',
                'selection_result' => $score->selection_result,
                'rank' => $score->rank,
                'total_score' => $score->total_score,
            ]);

        return view('public.announcement-index', [
            'scholarship' => $scholarship,
            'results' => $results,
        ]);
    }

    public function check(Scholarship $scholarship, Request $request)
    {
        $validated = $request->validate([
            'registration_number' => 'required|string',
        ]);

        $application = Application::with(['user', 'score'])
            ->where('scholarship_id', $scholarship->id)
            ->where('registration_number', $validated['registration_number'])
            ->first();

        if (!$application) {
            return back()->with('error', 'Nomor registrasi tidak ditemukan.');
        }

        return view('public.announcement-detail', [
            'scholarship' => $scholarship,
            'application' => $application,
            'score' => $application->score,
        ]);
    }
}
