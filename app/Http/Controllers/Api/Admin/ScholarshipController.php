<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Models\Scholarship;
use Illuminate\Http\Request;

class ScholarshipController extends BaseController
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = Scholarship::query();

        if ($request->filled('filter.status')) {
            $query->where('status', $request->input('filter.status'));
        }

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('academic_year', 'like', "%{$q}%");
            });
        }

        return $this->success(
            $query->orderByDesc('created_at')
                ->paginate($request->input('per_page', 15))
                ->through(fn ($s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                    'slug' => $s->slug,
                    'academic_year' => $s->academic_year,
                    'fund_amount' => $s->fund_amount,
                    'quota_primary' => $s->quota_primary,
                    'quota_reserve' => $s->quota_reserve,
                    'date_start' => $s->date_start?->format('Y-m-d'),
                    'date_end' => $s->date_end?->format('Y-m-d'),
                    'status' => $s->status,
                    'applications_count' => $s->applications()->count(),
                    'created_at' => $s->created_at?->format('Y-m-d H:i:s'),
                ])
        );
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:scholarships,slug'],
            'predecessor_scholarship_id' => ['nullable', 'exists:scholarships,id'],
            'description' => ['nullable', 'string'],
            'academic_year' => ['nullable', 'string', 'max:20'],
            'fund_amount' => ['nullable', 'integer', 'min:0'],
            'quota_primary' => ['required', 'integer', 'min:1'],
            'quota_reserve' => ['sometimes', 'integer', 'min:0'],
            'date_start' => ['nullable', 'date'],
            'date_end' => ['nullable', 'date', 'after_or_equal:date_start'],
            'status' => ['sometimes', 'in:draft,open,closed,renewal_open,announced'],
            'min_gpa_renewal' => ['nullable', 'numeric', 'min:0', 'max:4.0'],
        ]);

        $scholarship = Scholarship::create($validated + ['created_by' => $request->user()->id]);

        return $this->success($scholarship, 'Program beasiswa berhasil dibuat.', 201);
    }

    public function show(Scholarship $scholarship): \Illuminate\Http\JsonResponse
    {
        $scholarship->load([
            'qualificationGroups.qualifications.options',
            'qualificationGroups.qualifications.ranges',
            'verifiers.user:id,name,email',
            'predecessor:id,name,slug',
        ]);

        return $this->success($scholarship->toArray());
    }

    public function update(Scholarship $scholarship, Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', 'unique:scholarships,slug,' . $scholarship->id],
            'description' => ['nullable', 'string'],
            'academic_year' => ['nullable', 'string', 'max:20'],
            'fund_amount' => ['nullable', 'integer', 'min:0'],
            'quota_primary' => ['sometimes', 'integer', 'min:1'],
            'quota_reserve' => ['sometimes', 'integer', 'min:0'],
            'date_start' => ['nullable', 'date'],
            'date_end' => ['nullable', 'date', 'after_or_equal:date_start'],
            'status' => ['sometimes', 'in:draft,open,closed,renewal_open,announced'],
            'min_gpa_renewal' => ['nullable', 'numeric', 'min:0', 'max:4.0'],
        ]);

        $scholarship->update($validated);

        return $this->success($scholarship, 'Program beasiswa berhasil diperbarui.');
    }

    public function destroy(Scholarship $scholarship): \Illuminate\Http\JsonResponse
    {
        $scholarship->delete();
        return $this->success(null, 'Program beasiswa berhasil dihapus.');
    }
}
