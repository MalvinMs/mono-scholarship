<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| RESTful API untuk Platform Beasiswa.
| Semua endpoint diawali dengan /api/ prefix (otomatis oleh Laravel).
|
*/

Route::middleware('throttle:api')->group(function () {

    // ==================== PUBLIC (no auth) ====================
    Route::post('/auth/register', [App\Http\Controllers\Api\Auth\AuthController::class, 'register'])
        ->middleware('throttle:auth');
    Route::post('/auth/login', [App\Http\Controllers\Api\Auth\AuthController::class, 'login'])
        ->middleware('throttle:auth');
    Route::post('/auth/forgot-password', [App\Http\Controllers\Api\Auth\AuthController::class, 'forgotPassword'])
        ->middleware('throttle:auth');
    Route::post('/auth/reset-password', [App\Http\Controllers\Api\Auth\AuthController::class, 'resetPassword'])
        ->middleware('throttle:auth');

    Route::get('/scholarships', [App\Http\Controllers\Api\Public\ScholarshipController::class, 'index']);
    Route::get('/scholarships/{scholarship}', [App\Http\Controllers\Api\Public\ScholarshipController::class, 'show']);
    Route::get('/scholarships/{scholarship}/form-config', [App\Http\Controllers\Api\Public\ScholarshipController::class, 'formConfig']);
    Route::get('/announcements', [App\Http\Controllers\Api\Public\AnnouncementController::class, 'index']);
    Route::get('/announcements/{scholarship}', [App\Http\Controllers\Api\Public\AnnouncementController::class, 'show']);
    Route::post('/announcements/{scholarship}/check', [App\Http\Controllers\Api\Public\AnnouncementController::class, 'check']);

    // ==================== AUTHENTICATED ====================
    Route::middleware('auth:sanctum')->group(function () {

        // Auth management
        Route::post('/auth/logout', [App\Http\Controllers\Api\Auth\AuthController::class, 'logout']);
        Route::get('/auth/user', [App\Http\Controllers\Api\Auth\AuthController::class, 'user']);
        Route::get('/auth/tokens', [App\Http\Controllers\Api\Auth\AuthController::class, 'tokens']);
        Route::delete('/auth/tokens/{tokenId}', [App\Http\Controllers\Api\Auth\AuthController::class, 'revokeToken']);

        // OTP
        Route::post('/auth/otp/send', [App\Http\Controllers\Api\Auth\OtpController::class, 'send'])
            ->middleware('throttle:otp');
        Route::post('/auth/otp/verify', [App\Http\Controllers\Api\Auth\OtpController::class, 'verify'])
            ->middleware('throttle:otp');

        // Profile
        Route::get('/profile', [App\Http\Controllers\Api\ProfileController::class, 'show']);
        Route::put('/profile', [App\Http\Controllers\Api\ProfileController::class, 'update']);
        Route::put('/profile/password', [App\Http\Controllers\Api\ProfileController::class, 'updatePassword']);

        // ==================== APPLICANT ====================
        Route::middleware('role:applicant')->prefix('applicant')->group(function () {
            Route::get('/dashboard', [App\Http\Controllers\Api\Applicant\DashboardController::class, 'index']);
            Route::get('/applications', [App\Http\Controllers\Api\Applicant\ApplicationController::class, 'index']);
            Route::post('/applications', [App\Http\Controllers\Api\Applicant\ApplicationController::class, 'store'])
                ->middleware('throttle:uploads');
            Route::get('/applications/{application}', [App\Http\Controllers\Api\Applicant\ApplicationController::class, 'show']);
            Route::post('/applications/{application}/files', [App\Http\Controllers\Api\Applicant\ApplicationController::class, 'uploadFile'])
                ->middleware('throttle:uploads');
            Route::delete('/applications/{application}/files/{qualification}', [App\Http\Controllers\Api\Applicant\ApplicationController::class, 'deleteFile']);
            Route::get('/applications/{application}/documents/{document}/download', [App\Http\Controllers\Api\Applicant\ApplicationController::class, 'downloadDocument']);
            Route::put('/applications/{application}/bank', [App\Http\Controllers\Api\Applicant\BankAccountController::class, 'update']);
            Route::post('/applications/{application}/renewal', [App\Http\Controllers\Api\Applicant\RenewalController::class, 'store']);
        });

        // ==================== VERIFIER ====================
        Route::middleware('role:verifier')->prefix('verifier')->group(function () {
            Route::get('/applications', [App\Http\Controllers\Api\Verifier\VerificationController::class, 'index']);
            Route::get('/applications/{application}', [App\Http\Controllers\Api\Verifier\VerificationController::class, 'show']);
            Route::post('/applications/{application}/documents/{document}/approve', [App\Http\Controllers\Api\Verifier\DocumentActionController::class, 'approve']);
            Route::post('/applications/{application}/documents/{document}/reject', [App\Http\Controllers\Api\Verifier\DocumentActionController::class, 'reject']);
            Route::post('/applications/{application}/answers/{answer}/approve', [App\Http\Controllers\Api\Verifier\AnswerActionController::class, 'approve']);
            Route::post('/applications/{application}/answers/{answer}/correct', [App\Http\Controllers\Api\Verifier\AnswerActionController::class, 'correct']);
            Route::post('/applications/{application}/finalize', [App\Http\Controllers\Api\Verifier\VerificationController::class, 'finalize']);
            Route::post('/applications/{application}/blacklist', [App\Http\Controllers\Api\Verifier\BlacklistActionController::class, 'store']);
        });

        // ==================== ADMIN ====================
        Route::middleware('role:admin|super-admin')->prefix('admin')->group(function () {
            Route::get('/dashboard', [App\Http\Controllers\Api\Admin\DashboardController::class, 'index']);

            // Scholarships
            Route::get('/scholarships', [App\Http\Controllers\Api\Admin\ScholarshipController::class, 'index']);
            Route::post('/scholarships', [App\Http\Controllers\Api\Admin\ScholarshipController::class, 'store']);
            Route::get('/scholarships/{scholarship}', [App\Http\Controllers\Api\Admin\ScholarshipController::class, 'show']);
            Route::put('/scholarships/{scholarship}', [App\Http\Controllers\Api\Admin\ScholarshipController::class, 'update']);
            Route::delete('/scholarships/{scholarship}', [App\Http\Controllers\Api\Admin\ScholarshipController::class, 'destroy']);

            // Qualification Groups
            Route::get('/scholarships/{scholarship}/qualification-groups', [App\Http\Controllers\Api\Admin\QualificationGroupController::class, 'index']);
            Route::post('/scholarships/{scholarship}/qualification-groups', [App\Http\Controllers\Api\Admin\QualificationGroupController::class, 'store']);
            Route::put('/scholarships/{scholarship}/qualification-groups/{group}', [App\Http\Controllers\Api\Admin\QualificationGroupController::class, 'update']);
            Route::delete('/scholarships/{scholarship}/qualification-groups/{group}', [App\Http\Controllers\Api\Admin\QualificationGroupController::class, 'destroy']);

            // Qualifications
            Route::get('/scholarships/{scholarship}/qualifications', [App\Http\Controllers\Api\Admin\QualificationController::class, 'index']);
            Route::post('/scholarships/{scholarship}/qualifications', [App\Http\Controllers\Api\Admin\QualificationController::class, 'store']);
            Route::put('/qualifications/{qualification}', [App\Http\Controllers\Api\Admin\QualificationController::class, 'update']);
            Route::delete('/qualifications/{qualification}', [App\Http\Controllers\Api\Admin\QualificationController::class, 'destroy']);

            // Options (nested under qualification)
            Route::post('/qualifications/{qualification}/options', [App\Http\Controllers\Api\Admin\QualificationOptionController::class, 'store']);
            Route::put('/qualifications/{qualification}/options/{option}', [App\Http\Controllers\Api\Admin\QualificationOptionController::class, 'update']);
            Route::delete('/qualifications/{qualification}/options/{option}', [App\Http\Controllers\Api\Admin\QualificationOptionController::class, 'destroy']);

            // Ranges (nested under qualification)
            Route::post('/qualifications/{qualification}/ranges', [App\Http\Controllers\Api\Admin\QualificationRangeController::class, 'store']);
            Route::put('/qualifications/{qualification}/ranges/{range}', [App\Http\Controllers\Api\Admin\QualificationRangeController::class, 'update']);
            Route::delete('/qualifications/{qualification}/ranges/{range}', [App\Http\Controllers\Api\Admin\QualificationRangeController::class, 'destroy']);

            // Verifier assignment
            Route::get('/scholarships/{scholarship}/verifiers', [App\Http\Controllers\Api\Admin\VerifierAssignmentController::class, 'index']);
            Route::post('/scholarships/{scholarship}/verifiers', [App\Http\Controllers\Api\Admin\VerifierAssignmentController::class, 'store']);
            Route::delete('/scholarships/{scholarship}/verifiers/{user}', [App\Http\Controllers\Api\Admin\VerifierAssignmentController::class, 'destroy']);

            // Tiebreaker
            Route::get('/scholarships/{scholarship}/tiebreaker', [App\Http\Controllers\Api\Admin\TiebreakerController::class, 'show']);
            Route::put('/scholarships/{scholarship}/tiebreaker', [App\Http\Controllers\Api\Admin\TiebreakerController::class, 'update']);

            // Users
            Route::get('/users', [App\Http\Controllers\Api\Admin\UserController::class, 'index']);
            Route::post('/users', [App\Http\Controllers\Api\Admin\UserController::class, 'store']);
            Route::get('/users/{user}', [App\Http\Controllers\Api\Admin\UserController::class, 'show']);
            Route::put('/users/{user}', [App\Http\Controllers\Api\Admin\UserController::class, 'update']);
            Route::delete('/users/{user}', [App\Http\Controllers\Api\Admin\UserController::class, 'destroy']);

            // Blacklist
            Route::get('/blacklist', [App\Http\Controllers\Api\Admin\BlacklistController::class, 'index']);
            Route::post('/blacklist/{blacklist_log}/revoke', [App\Http\Controllers\Api\Admin\BlacklistController::class, 'revoke']);

            // Batch scoring
            Route::get('/scholarships/{scholarship}/renewal-summary', [App\Http\Controllers\Api\Admin\BatchScoringController::class, 'renewalSummary']);
            Route::post('/scholarships/{scholarship}/batch-scoring', [App\Http\Controllers\Api\Admin\BatchScoringController::class, 'runBatch']);
            Route::get('/scholarships/{scholarship}/batch-scoring/progress', [App\Http\Controllers\Api\Admin\BatchScoringController::class, 'progress']);
            Route::get('/scholarships/{scholarship}/selection-results', [App\Http\Controllers\Api\Admin\BatchScoringController::class, 'results']);
            Route::get('/scholarships/{scholarship}/selection-results/{score}', [App\Http\Controllers\Api\Admin\BatchScoringController::class, 'showScore']);

            // Notifications
            Route::get('/scholarships/{scholarship}/notifications', [App\Http\Controllers\Api\Admin\NotificationController::class, 'show']);
            Route::put('/scholarships/{scholarship}/notifications', [App\Http\Controllers\Api\Admin\NotificationController::class, 'update']);
            Route::post('/scholarships/{scholarship}/notifications/test', [App\Http\Controllers\Api\Admin\NotificationController::class, 'test']);

            // Audit logs
            Route::get('/audit-logs/verification', [App\Http\Controllers\Api\Admin\AuditLogController::class, 'verification']);
            Route::get('/audit-logs/blacklist', [App\Http\Controllers\Api\Admin\AuditLogController::class, 'blacklist']);

            // Exports
            Route::get('/scholarships/{scholarship}/export/applicants', [App\Http\Controllers\Api\Admin\ExportController::class, 'applicants'])
                ->middleware('throttle:exports');
            Route::get('/scholarships/{scholarship}/export/pdf/recipients', [App\Http\Controllers\Api\Admin\ExportController::class, 'recipientsPdf'])
                ->middleware('throttle:exports');
            Route::get('/scholarships/{scholarship}/export/pdf/audit-log', [App\Http\Controllers\Api\Admin\ExportController::class, 'auditLogPdf'])
                ->middleware('throttle:exports');
        });

        // ==================== APPROVER ====================
        Route::middleware('role:approver')->prefix('approver')->group(function () {
            Route::get('/dashboard', [App\Http\Controllers\Api\Approver\DashboardController::class, 'index']);
            Route::get('/scholarships/{scholarship}/candidates', [App\Http\Controllers\Api\Approver\RecipientApprovalController::class, 'candidates']);
            Route::post('/scholarships/{scholarship}/approve', [App\Http\Controllers\Api\Approver\RecipientApprovalController::class, 'approve']);
        });

        // ==================== TREASURER ====================
        Route::middleware('role:treasurer')->prefix('treasurer')->group(function () {
            Route::get('/disbursements', [App\Http\Controllers\Api\Treasurer\DisbursementController::class, 'index']);
            Route::get('/disbursements/{disbursement}', [App\Http\Controllers\Api\Treasurer\DisbursementController::class, 'show']);
            Route::put('/disbursements/{disbursement}', [App\Http\Controllers\Api\Treasurer\DisbursementController::class, 'update']);
            Route::get('/scholarships/{scholarship}/export', [App\Http\Controllers\Api\Treasurer\ExportController::class, 'excel'])
                ->middleware('throttle:exports');
            Route::get('/scholarships/{scholarship}/export/pdf', [App\Http\Controllers\Api\Treasurer\ExportController::class, 'pdf'])
                ->middleware('throttle:exports');
        });

    });
});
