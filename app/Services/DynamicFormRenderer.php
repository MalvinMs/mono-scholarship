<?php

namespace App\Services;

use App\Models\Scholarship;

final class DynamicFormRenderer
{
    /**
     * Generate form configuration for a scholarship's qualifications.
     */
    public function getFormConfig(Scholarship $scholarship): array
    {
        $scholarship->load([
            'qualificationGroups.qualifications.options',
            'qualificationGroups.qualifications.ranges',
            'qualifications.options',
            'qualifications.ranges',
        ]);

        $groups = [];
        $ungroupedQualifications = [];

        foreach ($scholarship->qualificationGroups as $group) {
            $qualifications = $group->qualifications->map(fn($q) => $this->mapQualification($q))->toArray();
            $groups[] = [
                'id' => $group->id,
                'name' => $group->name,
                'description' => $group->description,
                'qualifications' => $qualifications,
            ];
        }

        // Qualifications without a group
        $allGroupedIds = $scholarship->qualificationGroups
            ->flatMap(fn($g) => $g->qualifications->pluck('id'))
            ->toArray();

        foreach ($scholarship->qualifications as $q) {
            if (!in_array($q->id, $allGroupedIds)) {
                $ungroupedQualifications[] = $this->mapQualification($q);
            }
        }

        if (!empty($ungroupedQualifications)) {
            $groups[] = [
                'id' => 0,
                'name' => 'Umum',
                'description' => null,
                'qualifications' => $ungroupedQualifications,
            ];
        }

        return $groups;
    }

    private function mapQualification($q): array
    {
        $data = [
            'id' => $q->id,
            'name' => $q->name,
            'description' => $q->description,
            'type' => $q->type,
            'is_required' => $q->is_required,
            'is_file_upload_required' => $q->is_file_upload_required,
            'file_upload_label' => $q->file_upload_label,
            'file_upload_description' => $q->file_upload_description,
        ];

        if (in_array($q->type, ['single_choice', 'multi_choice'])) {
            $data['options'] = $q->options->map(fn($o) => [
                'id' => $o->id,
                'label' => $o->label,
                'value' => $o->value,
                'description' => $o->description,
            ])->toArray();
        }

        if ($q->type === 'numeric_range') {
            $data['ranges'] = $q->ranges->map(fn($r) => [
                'id' => $r->id,
                'range_min' => $r->range_min,
                'range_max' => $r->range_max,
                'value' => $r->value,
                'label' => $r->label,
            ])->toArray();
        }

        return $data;
    }

    /**
     * Generate validation rules for a scholarship's qualifications.
     */
    public function getValidationRules(Scholarship $scholarship): array
    {
        $rules = [];
        $scholarship->load('qualifications');

        foreach ($scholarship->qualifications as $q) {
            $field = "answers.{$q->id}";
            $rule = [];

            if ($q->is_required) {
                $rule[] = 'required';
            } else {
                $rule[] = 'nullable';
            }

            match ($q->type) {
                'single_choice' => $rule[] = 'exists:qualification_options,id',
                'multi_choice' => $rule[] = 'array',
                'numeric_range' => $rule[] = 'numeric',
                'file_upload' => $q->is_required ? $rule = ['required', 'file', 'max:2048', 'mimes:jpg,jpeg,png,pdf'] : null,
                'text' => $rule[] = 'string',
                default => null,
            };

            if ($q->type === 'file_upload') {
                $rules["files.{$q->id}"] = $q->is_required
                    ? ['required', 'file', 'max:2048', 'mimes:jpg,jpeg,png,pdf']
                    : ['nullable', 'file', 'max:2048', 'mimes:jpg,jpeg,png,pdf'];
            } else {
                $rules[$field] = $rule;
            }

            if ($q->is_file_upload_required && $q->type !== 'file_upload') {
                $rules["files.{$q->id}"] = $q->is_required
                    ? ['required', 'file', 'max:2048', 'mimes:jpg,jpeg,png,pdf']
                    : ['nullable', 'file', 'max:2048', 'mimes:jpg,jpeg,png,pdf'];
            }
        }

        return $rules;
    }

    /**
     * Calculate maximum possible score for a scholarship.
     */
    public function getMaxScore(Scholarship $scholarship): int
    {
        $scholarship->load('qualifications.options', 'qualifications.ranges');

        return $scholarship->qualifications->sum(function ($q) {
            return match ($q->type) {
                'single_choice' => $q->options->max('value') ?? 0,
                'multi_choice' => $q->options->max('value') ?? 0,
                'numeric_range' => $q->ranges->max('value') ?? 0,
                default => 0,
            };
        });
    }
}
