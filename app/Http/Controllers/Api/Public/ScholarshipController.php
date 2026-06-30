<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Api\BaseController;
use App\Models\Scholarship;
use App\Services\DynamicFormRenderer;
use Illuminate\Http\Request;

class ScholarshipController extends BaseController
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $scholarships = Scholarship::query()
            ->whereIn('status', ['open', 'closed', 'announced'])
            ->orderByDesc('date_start');

        return $this->success(
            $scholarships->paginate($request->input('per_page', 15))
                ->through(fn ($s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                    'slug' => $s->slug,
                    'description' => $s->description,
                    'academic_year' => $s->academic_year,
                    'fund_amount' => $s->fund_amount,
                    'quota_primary' => $s->quota_primary,
                    'quota_reserve' => $s->quota_reserve,
                    'date_start' => $s->date_start?->format('Y-m-d'),
                    'date_end' => $s->date_end?->format('Y-m-d'),
                    'status' => $s->status,
                    'applications_count' => $s->applications_count ?? $s->applications()->count(),
                ])
        );
    }

    public function show(Scholarship $scholarship): \Illuminate\Http\JsonResponse
    {
        if (!in_array($scholarship->status, ['open', 'closed', 'announced'])) {
            return $this->error('Program tidak ditemukan.', 404);
        }

        $scholarship->load([
            'qualificationGroups.qualifications.options',
            'qualificationGroups.qualifications.ranges',
            'qualifications.options',
            'qualifications.ranges',
        ]);

        return $this->success([
            'id' => $scholarship->id,
            'name' => $scholarship->name,
            'slug' => $scholarship->slug,
            'description' => $scholarship->description,
            'academic_year' => $scholarship->academic_year,
            'fund_amount' => $scholarship->fund_amount,
            'quota_primary' => $scholarship->quota_primary,
            'quota_reserve' => $scholarship->quota_reserve,
            'min_gpa_renewal' => $scholarship->min_gpa_renewal,
            'date_start' => $scholarship->date_start?->format('Y-m-d'),
            'date_end' => $scholarship->date_end?->format('Y-m-d'),
            'status' => $scholarship->status,
        ]);
    }

    public function formConfig(Scholarship $scholarship): \Illuminate\Http\JsonResponse
    {
        if (!in_array($scholarship->status, ['open', 'closed', 'announced'])) {
            return $this->error('Program tidak ditemukan.', 404);
        }

        $renderer = app(DynamicFormRenderer::class);

        return $this->success([
            'groups' => $renderer->getFormConfig($scholarship),
            'max_score' => $renderer->getMaxScore($scholarship),
        ]);
    }
}
