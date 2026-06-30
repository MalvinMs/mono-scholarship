<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Api\BaseController;
use App\Models\Application;
use App\Models\ApplicationScore;
use App\Models\Scholarship;
use Illuminate\Http\Request;

class AnnouncementController extends BaseController
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $scholarships = Scholarship::where('status', 'announced')
            ->orderByDesc('updated_at')
            ->get(['id', 'name', 'slug', 'description', 'updated_at']);

        return $this->success($scholarships);
    }

    public function show(Scholarship $scholarship): \Illuminate\Http\JsonResponse
    {
        if ($scholarship->status !== 'announced') {
            return $this->error('Pengumuman belum tersedia.', 404);
        }

        $results = ApplicationScore::with('application.user')
            ->where('scholarship_id', $scholarship->id)
            ->whereNotNull('selection_result')
            ->whereNotNull('rank')
            ->orderBy('rank')
            ->get()
            ->map(fn ($score) => [
                'name' => $score->application?->user?->name ?? '-',
                'registration_number' => $score->application?->registration_number ?? '-',
                'selection_result' => $score->selection_result,
                'rank' => $score->rank,
                'total_score' => $score->total_score,
            ]);

        return $this->success([
            'scholarship' => [
                'id' => $scholarship->id,
                'name' => $scholarship->name,
                'slug' => $scholarship->slug,
                'academic_year' => $scholarship->academic_year,
            ],
            'results' => $results,
        ]);
    }

    public function check(Scholarship $scholarship, Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'registration_number' => ['required', 'string'],
        ]);

        $application = Application::with(['user', 'score'])
            ->where('scholarship_id', $scholarship->id)
            ->where('registration_number', $request->registration_number)
            ->first();

        if (!$application) {
            return $this->error('Nomor registrasi tidak ditemukan.', 404);
        }

        return $this->success([
            'scholarship_name' => $scholarship->name,
            'name' => $application->user?->name,
            'registration_number' => $application->registration_number,
            'status' => $application->status,
            'selection_result' => $application->score?->selection_result,
            'rank' => $application->score?->rank,
            'total_score' => $application->score?->total_score,
        ]);
    }
}
