<?php

use App\Models\Application;
use App\Models\ApplicationScore;
use App\Models\Scholarship;
use App\Models\User;
use App\Services\RenewalEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->engine = new RenewalEngine();
});

it('returns empty result when no predecessor scholarship', function () {
    $scholarship = Scholarship::factory()->create([
        'predecessor_scholarship_id' => null,
        'quota_primary' => 100,
    ]);

    $result = $this->engine->calculateRenewalSlots($scholarship);

    expect($result->totalActiveRecipients)->toBe(0);
    expect($result->totalSubmittedRenewal)->toBe(0);
    expect($result->eligibleForRenewal)->toBe(0);
    expect($result->remainingForNew)->toBe(0);
});

it('returns empty slots when predecessor has no selected recipients', function () {
    $predecessor = Scholarship::factory()->create(['quota_primary' => 50]);
    $scholarship = Scholarship::factory()->create([
        'predecessor_scholarship_id' => $predecessor->id,
        'quota_primary' => 100,
        'min_gpa_renewal' => 3.50,
    ]);

    $result = $this->engine->calculateRenewalSlots($scholarship);

    expect($result->totalActiveRecipients)->toBe(0);
    expect($result->eligibleForRenewal)->toBe(0);
    expect($result->remainingForNew)->toBe(100);
});

it('calculates eligible renewal slots with valid GPA threshold', function () {
    $predecessor = Scholarship::factory()->create(['quota_primary' => 50, 'status' => 'announced']);
    $scholarship = Scholarship::factory()->create([
        'predecessor_scholarship_id' => $predecessor->id,
        'quota_primary' => 100,
        'min_gpa_renewal' => 3.50,
        'status' => 'draft',
    ]);

    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $user3 = User::factory()->create();

    // Create 3 selected recipients from predecessor
    $app1 = Application::factory()->create([
        'scholarship_id' => $predecessor->id,
        'user_id' => $user1->id,
        'registration_number' => 'BBK24-00001',
        'status' => 'selected',
    ]);
    ApplicationScore::factory()->create([
        'application_id' => $app1->id,
        'scholarship_id' => $predecessor->id,
        'selection_result' => 'utama',
        'total_score' => 85,
        'max_possible_score' => 100,
        'is_final' => true,
    ]);

    $app2 = Application::factory()->create([
        'scholarship_id' => $predecessor->id,
        'user_id' => $user2->id,
        'registration_number' => 'BBK24-00002',
        'status' => 'selected',
    ]);
    ApplicationScore::factory()->create([
        'application_id' => $app2->id,
        'scholarship_id' => $predecessor->id,
        'selection_result' => 'utama',
        'total_score' => 80,
        'max_possible_score' => 100,
        'is_final' => true,
    ]);

    $app3 = Application::factory()->create([
        'scholarship_id' => $predecessor->id,
        'user_id' => $user3->id,
        'registration_number' => 'BBK24-00003',
        'status' => 'selected',
    ]);
    ApplicationScore::factory()->create([
        'application_id' => $app3->id,
        'scholarship_id' => $predecessor->id,
        'selection_result' => 'utama',
        'total_score' => 75,
        'max_possible_score' => 100,
        'is_final' => true,
    ]);

    // 2 of them submit renewal (user1 = GPA 3.80 eligible, user2 = GPA 3.20 NOT eligible)
    $renewal1 = Application::factory()->create([
        'scholarship_id' => $scholarship->id,
        'user_id' => $user1->id,
        'registration_number' => 'BBK25-00001',
        'status' => 'submitted',
        'is_renewal' => true,
        'previous_application_id' => $app1->id,
        'snapshot_profile' => ['gpa' => 3.80],
    ]);
    ApplicationScore::factory()->create([
        'application_id' => $renewal1->id,
        'scholarship_id' => $scholarship->id,
        'is_final' => true,
        'total_score' => 85,
        'max_possible_score' => 100,
    ]);

    $renewal2 = Application::factory()->create([
        'scholarship_id' => $scholarship->id,
        'user_id' => $user2->id,
        'registration_number' => 'BBK25-00002',
        'status' => 'submitted',
        'is_renewal' => true,
        'previous_application_id' => $app2->id,
        'snapshot_profile' => ['gpa' => 3.20],
    ]);
    ApplicationScore::factory()->create([
        'application_id' => $renewal2->id,
        'scholarship_id' => $scholarship->id,
        'is_final' => true,
        'total_score' => 80,
        'max_possible_score' => 100,
    ]);

    // User3 does NOT submit renewal

    $result = $this->engine->calculateRenewalSlots($scholarship);

    expect($result->totalActiveRecipients)->toBe(3);
    expect($result->totalSubmittedRenewal)->toBe(2);
    expect($result->eligibleForRenewal)->toBe(1); // Only user1 with GPA 3.80
    expect($result->remainingForNew)->toBe(99); // 100 - 1
});

it('all remaining slots go to new applicants when no renewals', function () {
    $predecessor = Scholarship::factory()->create(['quota_primary' => 30, 'status' => 'announced']);
    $scholarship = Scholarship::factory()->create([
        'predecessor_scholarship_id' => $predecessor->id,
        'quota_primary' => 60,
        'min_gpa_renewal' => 3.00,
    ]);

    $user = User::factory()->create();
    $app = Application::factory()->create([
        'scholarship_id' => $predecessor->id,
        'user_id' => $user->id,
        'registration_number' => 'BBK24-00010',
        'status' => 'selected',
    ]);
    ApplicationScore::factory()->create([
        'application_id' => $app->id,
        'scholarship_id' => $predecessor->id,
        'selection_result' => 'utama',
        'total_score' => 90,
        'max_possible_score' => 100,
        'is_final' => true,
    ]);

    // Recipient did not submit renewal

    $result = $this->engine->calculateRenewalSlots($scholarship);

    expect($result->totalActiveRecipients)->toBe(1);
    expect($result->totalSubmittedRenewal)->toBe(0);
    expect($result->eligibleForRenewal)->toBe(0);
    expect($result->remainingForNew)->toBe(60); // Full quota available
});
