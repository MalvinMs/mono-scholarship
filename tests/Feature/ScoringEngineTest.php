<?php

use App\Models\Application;
use App\Models\ApplicationAnswer;
use App\Models\Qualification;
use App\Models\QualificationOption;
use App\Models\QualificationRange;
use App\Models\Scholarship;
use App\Models\User;
use App\Services\ScoringEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->engine = new ScoringEngine();
});

it('calculates single_choice score correctly', function () {
    $scholarship = Scholarship::factory()->create(['quota_primary' => 10]);
    $user = User::factory()->create();

    $q = Qualification::factory()->create([
        'scholarship_id' => $scholarship->id,
        'type' => 'single_choice',
        'name' => 'Kepemilikan Rumah',
    ]);
    $option = QualificationOption::factory()->create([
        'qualification_id' => $q->id,
        'label' => 'Milik Sendiri',
        'value' => 30,
    ]);

    $application = Application::factory()->create([
        'scholarship_id' => $scholarship->id,
        'user_id' => $user->id,
        'registration_number' => 'TST-00001',
        'status' => 'submitted',
    ]);

    ApplicationAnswer::factory()->create([
        'application_id' => $application->id,
        'qualification_id' => $q->id,
        'selected_option_id' => $option->id,
    ]);

    $application->load(['scholarship.qualifications.options', 'answers.selectedOption']);
    $result = $this->engine->calculate($application);

    expect($result->total)->toBe(30);
    expect($result->breakdown[$q->id]['score'])->toBe(30);
    expect($result->breakdown[$q->id]['answer_label'])->toBe('Milik Sendiri');
});

it('matches numeric range correctly', function () {
    $scholarship = Scholarship::factory()->create(['quota_primary' => 10]);
    $user = User::factory()->create();

    $q = Qualification::factory()->create([
        'scholarship_id' => $scholarship->id,
        'type' => 'numeric_range',
        'name' => 'IPK',
    ]);
    QualificationRange::factory()->create(['qualification_id' => $q->id, 'range_min' => 0, 'range_max' => 2.99, 'value' => 0]);
    QualificationRange::factory()->create(['qualification_id' => $q->id, 'range_min' => 3.00, 'range_max' => 3.50, 'value' => 20]);
    QualificationRange::factory()->create(['qualification_id' => $q->id, 'range_min' => 3.51, 'range_max' => 4.00, 'value' => 40]);

    $application = Application::factory()->create([
        'scholarship_id' => $scholarship->id,
        'user_id' => $user->id,
        'registration_number' => 'TST-00002',
        'status' => 'submitted',
    ]);

    ApplicationAnswer::factory()->create([
        'application_id' => $application->id,
        'qualification_id' => $q->id,
        'numeric_value' => 3.75,
    ]);

    $application->load(['scholarship.qualifications.ranges', 'answers']);
    $result = $this->engine->calculate($application);

    expect($result->total)->toBe(40);
});

it('returns zero for file_upload and text types', function () {
    $scholarship = Scholarship::factory()->create(['quota_primary' => 10]);
    $user = User::factory()->create();

    $q1 = Qualification::factory()->create([
        'scholarship_id' => $scholarship->id,
        'type' => 'file_upload',
        'name' => 'Dokumen',
    ]);
    $q2 = Qualification::factory()->create([
        'scholarship_id' => $scholarship->id,
        'type' => 'text',
        'name' => 'Motivasi',
    ]);

    $application = Application::factory()->create([
        'scholarship_id' => $scholarship->id,
        'user_id' => $user->id,
        'registration_number' => 'TST-00003',
        'status' => 'submitted',
    ]);

    ApplicationAnswer::factory()->create(['application_id' => $application->id, 'qualification_id' => $q1->id]);
    ApplicationAnswer::factory()->create([
        'application_id' => $application->id,
        'qualification_id' => $q2->id,
        'text_value' => 'Saya ingin belajar...',
    ]);

    $application->load(['scholarship.qualifications', 'answers']);
    $result = $this->engine->calculate($application);

    expect($result->total)->toBe(0);
});

it('calculates max possible score across all qualification types', function () {
    $scholarship = Scholarship::factory()->create(['quota_primary' => 10]);
    $user = User::factory()->create();

    $q1 = Qualification::factory()->create(['scholarship_id' => $scholarship->id, 'type' => 'single_choice']);
    $q2 = Qualification::factory()->create(['scholarship_id' => $scholarship->id, 'type' => 'numeric_range']);
    $q3 = Qualification::factory()->create(['scholarship_id' => $scholarship->id, 'type' => 'multi_choice']);

    QualificationOption::factory()->create(['qualification_id' => $q1->id, 'value' => 10]);
    QualificationOption::factory()->create(['qualification_id' => $q1->id, 'value' => 50]);
    QualificationOption::factory()->create(['qualification_id' => $q1->id, 'value' => 30]);

    QualificationRange::factory()->create(['qualification_id' => $q2->id, 'range_min' => 0, 'range_max' => 4.00, 'value' => 40]);

    QualificationOption::factory()->create(['qualification_id' => $q3->id, 'value' => 20]);
    QualificationOption::factory()->create(['qualification_id' => $q3->id, 'value' => 35]);

    $application = Application::factory()->create([
        'scholarship_id' => $scholarship->id,
        'user_id' => $user->id,
        'registration_number' => 'TST-00004',
        'status' => 'submitted',
    ]);

    ApplicationAnswer::factory()->create(['application_id' => $application->id, 'qualification_id' => $q1->id]);
    ApplicationAnswer::factory()->create(['application_id' => $application->id, 'qualification_id' => $q2->id]);
    ApplicationAnswer::factory()->create(['application_id' => $application->id, 'qualification_id' => $q3->id]);

    $application->load(['scholarship.qualifications.options', 'scholarship.qualifications.ranges', 'answers']);
    $result = $this->engine->calculate($application);

    expect($result->max)->toBe(50 + 40 + 35); // 125
});

it('returns zero for null or missing answer', function () {
    $scholarship = Scholarship::factory()->create(['quota_primary' => 10]);
    $user = User::factory()->create();

    $q = Qualification::factory()->create([
        'scholarship_id' => $scholarship->id,
        'type' => 'single_choice',
        'name' => 'Indikator',
    ]);
    QualificationOption::factory()->create(['qualification_id' => $q->id, 'value' => 30, 'label' => 'A']);

    $application = Application::factory()->create([
        'scholarship_id' => $scholarship->id,
        'user_id' => $user->id,
        'registration_number' => 'TST-00005',
        'status' => 'submitted',
    ]);
    // No answer created

    $application->load(['scholarship.qualifications.options', 'answers']);
    $result = $this->engine->calculate($application);

    expect($result->total)->toBe(0);
    expect($result->breakdown[$q->id]['score'])->toBe(0);
});

it('validates no overlapping ranges when they dont overlap', function () {
    $scholarship = Scholarship::factory()->create(['quota_primary' => 10]);
    $q = Qualification::factory()->create(['scholarship_id' => $scholarship->id, 'type' => 'numeric_range']);

    QualificationRange::factory()->create(['qualification_id' => $q->id, 'range_min' => 0, 'range_max' => 3.00]);
    QualificationRange::factory()->create(['qualification_id' => $q->id, 'range_min' => 3.01, 'range_max' => 4.00]);

    expect($this->engine->validateRanges($q))->toBeTrue();
});

it('detects overlapping ranges', function () {
    $scholarship = Scholarship::factory()->create(['quota_primary' => 10]);
    $q = Qualification::factory()->create(['scholarship_id' => $scholarship->id, 'type' => 'numeric_range']);

    QualificationRange::factory()->create(['qualification_id' => $q->id, 'range_min' => 0, 'range_max' => 3.00]);
    QualificationRange::factory()->create(['qualification_id' => $q->id, 'range_min' => 2.50, 'range_max' => 4.00]);

    expect($this->engine->validateRanges($q))->toBeFalse();
});

it('calculates score for multiple qualifications simultaneously', function () {
    $scholarship = Scholarship::factory()->create(['quota_primary' => 10]);
    $user = User::factory()->create();

    $q1 = Qualification::factory()->create(['scholarship_id' => $scholarship->id, 'type' => 'single_choice', 'name' => 'Q1']);
    $q2 = Qualification::factory()->create(['scholarship_id' => $scholarship->id, 'type' => 'numeric_range', 'name' => 'Q2']);

    $opt = QualificationOption::factory()->create(['qualification_id' => $q1->id, 'value' => 25, 'label' => 'Pilihan A']);
    QualificationRange::factory()->create(['qualification_id' => $q2->id, 'range_min' => 0, 'range_max' => 100, 'value' => 15]);

    $application = Application::factory()->create([
        'scholarship_id' => $scholarship->id,
        'user_id' => $user->id,
        'registration_number' => 'TST-00006',
        'status' => 'submitted',
    ]);

    ApplicationAnswer::factory()->create(['application_id' => $application->id, 'qualification_id' => $q1->id, 'selected_option_id' => $opt->id]);
    ApplicationAnswer::factory()->create(['application_id' => $application->id, 'qualification_id' => $q2->id, 'numeric_value' => 50]);

    $application->load(['scholarship.qualifications.options', 'scholarship.qualifications.ranges', 'answers.selectedOption']);
    $result = $this->engine->calculate($application);

    expect($result->total)->toBe(40); // 25 + 15
    expect($result->max)->toBe(40);
    expect(count($result->breakdown))->toBe(2);
});
