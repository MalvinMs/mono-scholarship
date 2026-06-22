<?php

use App\Http\Controllers\Public\AnnouncementController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ProgramController;
use Illuminate\Support\Facades\Route;

// Public routes (no auth)
Route::get('/', HomeController::class)->name('home');

// Public programs
Route::get('/program', ProgramController::class)->name('program.list');

// Public announcement
Route::get('/pengumuman', [AnnouncementController::class, 'list'])->name('announcement.list');
Route::get('/pengumuman/{scholarship:slug}', [AnnouncementController::class, 'index'])->name('announcement');
Route::get('/pengumuman/{scholarship:slug}/cek', [AnnouncementController::class, 'check'])->name('announcement.check');

Route::middleware('guest')->group(function () {
    Route::view('/daftar', 'auth.register');
    Route::view('/login', 'auth.login')->name('login');
});

// OTP verification page (authenticated but not yet verified)
Route::middleware('auth')->group(function () {
    Route::view('/email/verify', 'auth.verify-email')->name('verification.notice');
});

// All authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Profile
    Route::view('/profil', 'profile.edit')->name('profile.edit');

    // Applicant routes
    Route::middleware('role:applicant')->group(function () {
        Route::view('/dashboard', 'applicant.dashboard')->name('applicant.dashboard');
        Route::view('/beasiswa', 'applicant.scholarships')->name('applicant.scholarships');
        Route::livewire('/beasiswa/{scholarship:slug}/daftar', 'applicant.application-form')->name('application.form');
        Route::livewire('/pendaftaran/{application}/status', 'applicant.application-status')->name('application.status');
        Route::livewire('/pendaftaran/{application}/revisi', 'applicant.document-revision')->name('application.revision');
        Route::livewire('/pendaftaran/{application}/rekening', 'applicant.bank-account-form')->name('application.bank');
        Route::livewire('/renewal/{application}', 'applicant.semester-renewal')->name('application.renewal');
    });

    // Verifier routes
    Route::middleware('role:verifier')->group(function () {
        Route::livewire('/verifikasi', 'verifier.verification-queue')->name('verification.queue');
        Route::livewire('/verifikasi/{application}', 'verifier.application-detail')->name('verification.detail');
    });

    // Admin routes
    Route::middleware('role:admin|super-admin')->prefix('admin')->group(function () {
        Route::livewire('/dashboard', 'dashboard.admin-dashboard')->name('admin.dashboard');
        Route::livewire('/beasiswa', 'admin.scholarship-manager')->name('admin.scholarships');
        Route::livewire('/beasiswa/{scholarship}/qualification', 'admin.qualification-builder')->name('admin.qualifications');
        Route::livewire('/beasiswa/{scholarship}/tiebreaker', 'admin.tiebreaker-configurator')->name('admin.tiebreaker');
        Route::livewire('/beasiswa/{scholarship}/verifikator', 'admin.verifier-assignment')->name('admin.verifiers');
        Route::livewire('/pengguna', 'admin.user-manager')->name('admin.users');
        Route::livewire('/blacklist', 'admin.blacklist-manager')->name('admin.blacklist');
        Route::livewire('/seleksi', 'admin.selection-result')->name('admin.selection');
        Route::livewire('/seleksi/batch', 'admin.batch-selection-runner')->name('admin.batch');
        Route::livewire('/notifikasi', 'admin.notification-configurator')->name('admin.notifications');
        Route::get('/export/penerima/{scholarshipId}', \App\Http\Controllers\Export\ExportApplicantsController::class)->name('admin.export.applicants');
        Route::get('/pdf/penerima/{scholarship}', [\App\Http\Controllers\Export\PdfReportController::class, 'recipients'])->name('admin.pdf.recipients');
        Route::get('/pdf/audit-log/{scholarship}', [\App\Http\Controllers\Export\PdfReportController::class, 'auditLog'])->name('admin.pdf.audit-log');
        Route::livewire('/audit-log', 'admin.audit-log-viewer')->name('admin.audit-log');
    });

    // Approver routes
    Route::middleware('role:approver')->prefix('approver')->group(function () {
        Route::livewire('/dashboard', 'approver.approver-dashboard')->name('approver.dashboard');
        Route::livewire('/penetapan', 'approver.recipient-approval')->name('approver.approval');
    });

    // Treasurer routes
    Route::middleware('role:treasurer')->prefix('keuangan')->group(function () {
        Route::livewire('/pencairan', 'treasurer.disbursement-list')->name('treasurer.disbursements');
        Route::get('/export/{scholarshipId}', \App\Http\Controllers\Export\ExportDisbursementController::class)->name('treasurer.export');
        Route::get('/pdf/{scholarship}', [\App\Http\Controllers\Export\PdfReportController::class, 'disbursement'])->name('treasurer.pdf.disbursement');
    });
});
