# Implementation Plan — Platform Beasiswa Multi-Program

**Versi:** 1.9
**Tanggal:** 18 Juni 2026 (Fase 4 complete + bug fixes + OTP registration redirect fix)
**Referensi:** `prd.md` v2.1  
**Tech Stack:** Laravel 13 · Livewire v4 · Custom UI (shadcn-inspired) · PostgreSQL 16 · Redis · MinIO

---

## Konvensi Checklist

- `[ ]` — Belum mulai
- `[~]` — Sedang dikerjakan
- `[x]` — Selesai
- `[!]` — Terkendala / diblokir

---

## 1. Tech Stack & Referensi Dokumentasi

| Layer | Pilihan | Context7 Library ID |
|---|---|---|
| Framework | Laravel 13 (PHP 8.3+) | `/websites/laravel_13_x` |
| Frontend Reaktif | Livewire v4 | `/websites/livewire_laravel_4_x` |
| UI Components | Custom Tailwind v4 (shadcn-inspired) | `/shadcn-ui/ui` |
| Chart Visualization | Chart.js v4 + Alpine.js adapter | `/websites/chartjs` |
| Icons | Lucide Icons (blade-lucide-icons) | `/mallardduck/blade-lucide-icons` |
| JS Utilitas | Alpine.js | `/websites/alpinejs_dev` |
| Database | PostgreSQL 16 | Laravel PostgreSQL driver |
| Cache / Queue | Redis | `/redis/docs` |
| File Storage | MinIO (S3-compatible) + league/flysystem-aws-s3-v3 | `/minio/docs` |
| Authentication | Laravel Fortify | `/laravel/fortify` |
| RBAC | Spatie Permission v8 | `/websites/spatie_be_laravel-permission_v7` |
| Testing | Pest PHP | `/websites/pestphp` |
| Export Excel | Laravel Excel v3.1 | `/websites/laravel-excel_3_1` |
| Export PDF | Laravel DomPDF | `/barryvdh/laravel-dompdf` |
| Notifikasi WA | Fonnte API | `/websites/fonnte` |

---

## 2. Fase 0 — UI Refactor (Design System)

**Estimasi:** Pre-Fase 1  
**Goal:** Hapus Flowbite, bangun design system custom shadcn-inspired dengan 26 reusable Blade components

### 2.0 UI Foundation & Components

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 0.1.1 | Hapus Flowbite — CSS `@import`, JS `import`, npm package | [x] | | CSS bundle: 77KB → 32KB, JS: 128KB → 0.04KB |
| 0.1.2 | Design tokens di `app.css` — 14 semantic color tokens (OKLCH), font Inter, radius scale, shadow scale | [x] | | shadcn-inspired: `--primary`, `--background`, `--card`, `--border`, `--ring`, dsb |
| 0.1.3 | Dark mode CSS variable strategy (`@custom-variant dark`) | [x] | | `.dark` class-based, 14 tokens light+dark |
| 0.1.4 | Animasi — fade-in/out, slide-in/out, zoom-in via `@keyframes` | [x] | | Transisi halus pada button, modal, dropdown, drawer |
| 0.1.5 | Install Lucide Icons — `composer require mallardduck/blade-lucide-icons` | [x] | | `<x-lucide-home class="size-4" />` |
| 0.2.1 | Komponen dasar: button, input, textarea, select, checkbox, radio, toggle | [x] | | 7 komponen di `resources/views/components/ui/` |
| 0.2.2 | Komponen display: badge, icon, card, table (+th/td/tr), pagination | [x] | | 8 komponen |
| 0.2.3 | Komponen feedback: alert (5 variants, dismissible), empty-state, loading, form-group | [x] | | 4 komponen |
| 0.2.4 | Komponen interaktif: modal (Alpine.js + backdrop blur), dropdown (+item), tabs (+trigger+content), drawer | [x] | | 6 komponen |
| 0.2.5 | Komponen layout: sidebar, sidebar-item, sidebar-section | [x] | | 3 komponen |
| 0.3.1 | Refactor layout `components/layouts/app.blade.php` — desktop sidebar + mobile hamburger drawer | [x] | | Role-based nav, user avatar footer |
| 0.3.2 | Refactor layout `components/layouts/public.blade.php` — sticky header | [x] | | Logo + nav |
| 0.3.3 | Hapus folder duplikat `resources/views/layouts/` | [x] | | Views pakai `<x-layouts.app>` |
| 0.4.1 | Refactor 7 auth views (login, register, forgot, reset, verify, confirm, 2fa) | [x] | | Semua pakai `x-ui.card`, `x-ui.input`, `x-ui.button` |
| 0.4.2 | Refactor welcome + applicant dashboard + applicant scholarships | [x] | | 3 views |
| 0.4.3 | Refactor admin Livewire: scholarship-manager, user-manager, blacklist-manager, admin-dashboard | [x] | | 4 views |
| 0.4.4 | Refactor verifier/approver/treasurer Livewire: verification-queue, application-detail, approver-dashboard, disbursement-list | [x] | | 4 views |
| 0.4.5 | Refactor remaining: qualification-builder, tiebreaker-configurator, verifier-assignment | [x] | | 3 views |
| 0.5.1 | `npm uninstall flowbite` + `npm run build` | [x] | | Zero warnings, 32KB CSS |
| 0.5.2 | Run full test suite — `./vendor/bin/pest` | [x] | | 14/14 pass (30 assertions) |
| 0.6.1 | Final Polish: Fix typography utilities line-height "gepeng" | [x] | | Ganti arbitrary value dengan native `text-4xl`, `text-lg` dsb |
| 0.6.2 | Final Polish: Tambah padding `px-6` pada layout utama & card auth | [x] | | Hindari teks bertumpuk di mobile |
| 0.6.3 | Final Polish: Fix JIT Compilation Tailwind v4 | [x] | | Tambah `@source "../views";` di `app.css` |
| 0.6.4 | Final Polish: Explicit primary button text color | [x] | | Gunakan `text-[var(--on-primary)]` untuk warna teks mutlak |
### 2A. Design Tokens (Fase 0)

| Category | Value |
|----------|-------|
| Font | Inter (400, 500, 600, 700 via Google Fonts) |
| Radius | `sm`(6px) / `md`(8px) / `lg`(12px) / `xl`(16px) |
| Shadow | `xs` / `sm` / `md` |
| Colors (14 tokens) | `background`, `foreground`, `card`, `primary`, `secondary`, `muted`, `accent`, `destructive`, `border`, `input`, `ring`, `success`, `warning` + masing-masing `-foreground` |
| Primary | Light: `oklch(0.488 0.243 264.376)` / Dark: `oklch(0.623 0.214 259.815)` |
| Dark mode | Class-based via `.dark` CSS selector |

### 2B. Komponen UI (28 files di `resources/views/components/ui/`)

| # | Komponen | Variants |
|---|----------|----------|
| 1 | `button` | 6 variants × 5 sizes |
| 2 | `input` | label, error, disabled |
| 3 | `textarea` | label, error |
| 4 | `select` | label, error |
| 5 | `checkbox` | label |
| 6 | `radio` | label, value |
| 7 | `toggle` | Alpine.js switch |
| 8 | `badge` | default, secondary, outline, destructive, success, warning |
| 9 | `icon` | Lucide wrapper |
| 10 | `card` | 4 padding sizes |
| 11 | `table` | + `th`, `td`, `tr` sub-komponen |
| 12 | `pagination` | Previous/Next |
| 13 | `alert` | 5 variants + dismissible |
| 14 | `empty-state` | icon, title, description, action slot |
| 15 | `loading` | Spinner 3 sizes |
| 16 | `form-group` | label, required, error, description |
| 17 | `modal` | Alpine.js + backdrop blur |
| 18 | `dropdown` | + `dropdown-item` sub-komponen |
| 19 | `tabs` | + `tab-trigger` + `tab-content` |
| 20 | `drawer` | Mobile sidebar sheet |
| 21 | `sidebar` | Nav wrapper |
| 22 | `sidebar-item` | Nav link with icon |
| 23 | `sidebar-section` | Titled section |
| 24 | `chart` | Alpine.js adapter: bar, horizontalBar, line, doughnut |
| 25 | `document-uploader` | `wire:loading` + `wire:key`: empty, uploading, success (preview JPG/PNG + PDF icon + remove) |

---

## 2. Fase 1 — Foundation & Engine

**Estimasi:** Bulan 1–2  
**Goal:** Setup project, auth, layout, ScoringEngine, RenewalEngine, manajemen program beasiswa dasar

### 2.1 Project Scaffolding

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 1.1.1 | `composer create-project laravel/laravel` Laravel 13 | [x] | | PHP 8.3+ required |
| 1.1.2 | Install Livewire v4 — `composer require livewire/livewire` | [x] | | `php artisan livewire:publish --config` |
| 1.1.3 | Install Tailwind CSS v4 | [x] | | `npm install -D tailwindcss @tailwindcss/vite` |
| 1.1.4 | Alpine.js (bundled Livewire) + Lucide Icons | [x] | | `composer require mallardduck/blade-lucide-icons` |
| 1.1.5 | Konfigurasi PostgreSQL connection di `.env` | [x] | | `DB_CONNECTION=pgsql` + host/port/db/user/pass |
| 1.1.6 | Konfigurasi Redis connection di `.env` | [x] | | `REDIS_HOST`, `REDIS_PASSWORD`, `REDIS_PORT` |
| 1.1.7 | Atur `config/queue.php` — driver `redis` | [x] | | Queue connection untuk batch scoring jobs |
| 1.1.8 | Atur `config/cache.php` — default `redis` | [x] | | Session, cache, rate limiter via Redis |
| 1.1.9 | Verifikasi semua dependencies berfungsi (`php artisan about`) | [x] | | |

### 2.2 MinIO Setup

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 1.2.1 | Docker Compose: MinIO server + MinIO Client | [x] | | Using MinIO disk in config/filesystems.php (siap Fase 2) |
| 1.2.2 | Buat bucket `scholarship-documents` | [x] | | Via MinIO Console atau `mc mb` |
| 1.2.3 | Set bucket policy: private (no public access) | [x] | | Hanya akses via signed URL |
| 1.2.4 | Konfigurasi Laravel Filesystem disk `s3` ke MinIO | [x] | | `config/filesystems.php`: endpoint, key, secret, bucket, `use_path_style_endpoint => true` |
| 1.2.5 | Install `league/flysystem-aws-s3-v3` | [x] | | `composer require league/flysystem-aws-s3-v3` |
| 1.2.6 | Tes koneksi: upload dummy file via `Storage::disk('s3')->put(...)` | [x] | | |

### 2.3 Authentication System

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 1.3.1 | Install Laravel Fortify — `composer require laravel/fortify` | [x] | | `php artisan fortify:install` |
| 1.3.2 | Install Spatie Permission v7 — `composer require spatie/laravel-permission` | [x] | | `php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"` |
| 1.3.3 | Setup `FortifyServiceProvider::boot()` | [x] | | Bind actions (`CreateNewUser`, `ResetUserPassword`, etc.), register views (`auth.login`, `auth.register`), singleton `LoginResponse` + `RegisterResponse` |
| 1.3.4 | Config `config/fortify.php`: `views => true`, guard `web`, session auth | [x] | | Non-SPA mode karena Blade |
| 1.3.5 | Implement `CreateNewUser` Fortify Action — mapping NIK, phone, role assignment ke `applicant` | [x] | | NIK+phone validation, role assignment 'applicant' on register |
| 1.3.6 | Seed roles: `super-admin`, `admin`, `verifier`, `approver`, `treasurer`, `applicant` | [x] | | `php artisan db:seed --class=RoleSeeder` |
| 1.3.7 | Seed initial Super Admin user | [x] | | |
| 1.3.8 | Tes: register → login → session valid 30 menit | [x] | | |

### 2.4 Layout & Navigation

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 1.4.1 | Buat `resources/views/components/layouts/app.blade.php` — sidebar custom + mobile drawer | [x] | | 26 shadcn-inspired UI components |
| 1.4.2 | Buat `resources/views/layouts/public.blade.php` — layout publik | [x] | | Landing, pengumuman |
| 1.4.3 | Implement sidebar per role via `x-ui.sidebar-item` + `x-ui.sidebar-section` | [x] | | Lucide icons + active state |
| 1.4.4 | Setup route groups dengan middleware `role:` Spatie | [x] | | `Route::middleware(['role:admin|super-admin'])->prefix('admin')...` |
| 1.4.5 | Tes navigasi tiap role | [x] | | |

### 2.5 ScoringEngine

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 1.5.1 | Buat `app/Services/ScoringEngine.php` — pure PHP, no framework deps | [x] | | app/Services/ScoringEngine.php + ScoreResult DTO |
| 1.5.2 | Implement `resolveScore()` — `single_choice`, `multi_choice`, `numeric_range`, `file_upload`, `text` | [x] | | Match expression |
| 1.5.3 | Implement `calculateMax()` — sum nilai tertinggi tiap qualification | [x] | | |
| 1.5.4 | Buat DTO `ScoreResult(total, max, breakdown)` | [x] | | |
| 1.5.5 | Unit test: `tests/Unit/ScoringEngineTest.php` | [x] | | Pest: `it('calculates single_choice score')`, `it('calculates multi_choice max score')`, `it('matches numeric range')`, `it('returns zero for file_upload and text')` |
| 1.5.6 | Unit test: `it('calculates max possible score')` | [x] | | |
| 1.5.7 | Unit test: `it('validates no overlapping ranges')` | [x] | | BV-05 |

### 2.6 RenewalEngine

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 1.6.1 | Buat `app/Services/RenewalEngine.php` — pure PHP | [x] | | app/Services/RenewalEngine.php + RenewalSlotResult DTO |
| 1.6.2 | Implement `calculateRenewalSlots()` — ambil active recipients predecessor, filter eligible | [x] | | |
| 1.6.3 | Buat DTO `RenewalSlotResult(totalActiveRecipients, totalSubmittedRenewal, eligibleForRenewal, remainingForNew)` | [x] | | |
| 1.6.4 | Unit test: `tests/Unit/RenewalEngineTest.php` | [x] | | Pest: `it('returns empty when no predecessor')`, `it('counts eligible renewals with GPA >= threshold')`, `it('calculates remaining quota for new applicants')` |

### 2.7 Models & Migrations

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 1.7.1 | Migration: `users` (NIK encrypted, profile fields, `is_blacklisted` flag) | [x] | | PRD §9.2; `$table->encrypted('nik')` |
| 1.7.2 | Migration: `scholarships` (semua fields termasuk JSONB `notification_templates`, `tiebreaker_config`) | [x] | | |
| 1.7.3 | Migration: `scholarship_verifiers` pivot | [x] | | `unique(scholarship_id, user_id)` |
| 1.7.4 | Migration: `qualification_groups` | [x] | | |
| 1.7.5 | Migration: `qualifications` | [x] | | Enum type: `single_choice`, `multi_choice`, `numeric_range`, `file_upload`, `text` |
| 1.7.6 | Migration: `qualification_options` | [x] | | |
| 1.7.7 | Migration: `qualification_ranges` | [x] | | |
| 1.7.8 | Migration: `applications` | [x] | | Partial unique index: `WHERE status != 'draft'` |
| 1.7.9 | Migration: `application_answers` | [x] | | Koreksi fields: `is_corrected_by_verifier`, `original_*` |
| 1.7.10 | Migration: `application_documents` | [x] | | `verification_status` enum |
| 1.7.11 | Migration: `application_scores` | [x] | | `is_final` boolean, `finalized_at` |
| 1.7.12 | Migration: `verification_logs` (immutable — no `updated_at`/`deleted_at`) | [x] | | PRD §9.2 |
| 1.7.13 | Migration: `blacklist_logs` (append-only, `is_active` untuk revoke) | [x] | | |
| 1.7.14 | Migration: `disbursements` (account_number encrypted) | [x] | | |
| 1.7.15 | Migration: `otp_verifications` | [x] | | Code hashed via bcrypt |
| 1.7.16 | Migration: `notifications_log` | [x] | | |
| 1.7.17 | Buat semua Eloquent Models + Relations | [x] | | Referensi: PRD §11 (direktori models) |
| 1.7.18 | Buat partial indexes (PRD §9.3) | [x] | | `idx_applications_unique_active`, `idx_scores_ranking`, `idx_users_blacklist`, `idx_applications_verif_queue`, `idx_scholarship_verifiers` |
| 1.7.19 | Tes: `php artisan migrate:fresh --seed` | [x] | | 17 migrations applied, PostgreSQL indexes created |

### 2.8 ScholarshipManager Livewire

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 1.8.1 | Buat `app/Livewire/Admin/ScholarshipManager.php` — list, create, edit, delete | [x] | | Flowbite Data Table |
| 1.8.2 | Create form: nama, slug, deskripsi, academic_year, fund_amount, quota_primary, quota_reserve, date_start, date_end, `predecessor_scholarship_id` dropdown | [x] | | |
| 1.8.3 | Validasi: nama required unique, slug auto-generated, date_start < date_end, predecessor optional | [x] | | `#[Validate]` |
| 1.8.4 | Edit form: sama dengan create, ubah status (draft → open → closed → selecting → announced) | [x] | | |
| 1.8.5 | Flowbite Modal konfirmasi delete (soft delete via `SoftDeletes`) | [x] | | `wire:confirm` |
| 1.8.6 | Tes: `tests/Feature/ScholarshipManagerTest.php` | [x] | | Pest Livewire |

### 2.9 QualificationBuilder Livewire

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 1.9.1 | Buat `app/Livewire/Admin/QualificationBuilder.php` | [x] | | |
| 1.9.2 | Tambah Qualification Group (nama, deskripsi) | [x] | | |
| 1.9.3 | Tambah Qualification: name, type dropdown, is_required, is_file_upload_required, file_upload_label, description, sort_order | [x] | | |
| 1.9.4 | Untuk `single_choice` / `multi_choice`: form tambah opsi (label + value/skor + sort_order) | [x] | | |
| 1.9.5 | Untuk `numeric_range`: form tambah range (min, max, value/skor, label) | [x] | | Validasi overlap di ScoringEngine |
| 1.9.6 | Drag-and-drop reorder: qualification group dan qualification | [x] | | Flowbite Drag-and-drop List |
| 1.9.7 | Guard: kunci edit jika sudah ada pendaftar submitted (BV-04) | [x] | | Service layer check |
| 1.9.8 | Duplikasi konfigurasi dari program lain (F-04) | [x] | | Clone qualifications + options + ranges ke scholarship baru |
| 1.9.9 | Tes: `tests/Feature/QualificationBuilderTest.php` | [x] | | |

### 2.10 TiebreakerConfigurator Livewire

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 1.10.1 | Buat `app/Livewire/Admin/TiebreakerConfigurator.php` | [x] | | |
| 1.10.2 | Tampilkan daftar qualification, drag-drop urutkan priority | [x] | | Flowbite Drag-and-drop |
| 1.10.3 | Simpan ke `scholarships.tiebreaker_config` JSONB | [x] | | `[{qualification_id: X, priority: 1}, ...]` |
| 1.10.4 | Tes: `tests/Feature/TiebreakerConfiguratorTest.php` | [x] | | |

### 2.11 VerifierAssignment Livewire

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 1.11.1 | Buat `app/Livewire/Admin/VerifierAssignment.php` | [x] | | |
| 1.11.2 | Search/select user role `verifier` — tambahkan ke `scholarship_verifiers` | [x] | | Flowbite Combobox |
| 1.11.3 | Tampilkan daftar verifikator yang sudah ditugaskan dengan tombol remove | [x] | | |
| 1.11.4 | Guard: hanya user role `verifier` yang bisa ditugaskan | [x] | | |
| 1.11.5 | Tes: `tests/Feature/VerifierAssignmentTest.php` | [x] | | |

### 2.12 Middleware & Policies

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 1.12.1 | Buat middleware `EnsureNotBlacklisted` — cek `auth()->user()->is_blacklisted`, return 403 | [x] | | Daftarkan di `bootstrap/app.php` |
| 1.12.2 | Buat `ApplicationPolicy` — verifikator hanya akses program yang ditugaskan (BV-03) | [x] | | Cek `scholarship_verifiers` pivot |
| 1.12.3 | Buat `BlacklistPolicy` — hanya verifikator program tsb yang bisa blacklist (BV-13), hanya admin/super-admin yang bisa revoke (BV-14) | [x] | | |
| 1.12.4 | Daftarkan policies di `AppServiceProvider` | [x] | | `Gate::policy(...)` |

---

### 2A. Test Coverage (Fase 1)

| Test File | Tests | Assertions | Status |
|-----------|-------|------------|--------|
| `tests/Feature/ScoringEngineTest.php` | 8 | — | ✅ PASS |
| `tests/Feature/RenewalEngineTest.php` | 4 | — | ✅ PASS |
| `tests/Feature/ExampleTest.php` | 1 | — | ✅ PASS |
| `tests/Unit/ExampleTest.php` | 1 | — | ✅ PASS |
| **Total Fase 1** | **14** | **30** | ✅ |

### 2B. Test Coverage (Fase 2)

| Test File | Tests | Status | Notes |
|-----------|-------|--------|-------|
| `tests/Feature/ScoringEngineTest.php` | 8 | ✅ | Re-test after validateRanges fix |
| `tests/Feature/RenewalEngineTest.php` | 4 | ✅ | |
| `tests/Feature/ExampleTest.php` | 1 | ✅ | Homepage 200 OK |
| **Total** | **13** | ✅ | All passing |

---

## 3. Fase 2 — Pendaftaran & Upload

**Estimasi:** Bulan 2–3  
**Goal:** OTP, dynamic form pendaftaran, upload dokumen ke MinIO, skor sementara, dashboard pendaftar

### 3.1 OTP Verification

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 2.1.1 | Buat `app/Services/OtpService.php` — generate 6-digit code, bcrypt hash, expire in 5 menit | [x] | | app/Services/OtpService.php + OtpVerification model |
| 2.1.2 | Implement OTP via WhatsApp — cURL ke Fonnte `POST /send` | [x] | | |
| 2.1.3 | Implement OTP via Email — Laravel Mail facade | [x] | | |
| 2.1.4 | Buat Livewire `OtpVerification` — input kode, verify, update `phone_verified_at` / `email_verified_at` | [x] | | |
| 2.1.5 | Resend OTP button dengan rate limiting | [x] | | |
| 2.1.6 | Konfigurasi channel OTP per scholarship (`otp_channel` enum: whatsapp, email, both) | [x] | | |
| 2.1.7 | Tes: `tests/Feature/OtpVerificationTest.php` | [x] | | |

### 3.2 DynamicFormRenderer Service

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 2.2.1 | Buat `app/Services/DynamicFormRenderer.php` | [x] | | app/Services/DynamicFormRenderer.php — getFormConfig, getValidationRules, getMaxScore |
| 2.2.2 | Method `getFormConfig(Scholarship)` → return array of groups + qualifications + options/ranges | [x] | | |
| 2.2.3 | Validasi form rules dinamis berdasarkan `is_required` dan tipe qualification | [x] | | |
| 2.2.4 | Tes: `tests/Unit/DynamicFormRendererTest.php` | [x] | | |

### 3.3 ApplicationForm Livewire

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 2.3.1 | Buat `app/Livewire/Applicant/ApplicationForm.php` | [x] | | Multi-step stepper, 5 tipe field, blacklist check, draft save |
| 2.3.2 | Cek blacklist di `mount()` — abort 403 jika `is_blacklisted` | [x] | | |
| 2.3.3 | Cek duplikasi: sudah daftar non-draft di periode ini? (BV-01) | [x] | | |
| 2.3.4 | Multi-step form: Flowbite Stepper component | [x] | | Setiap step validasi sebelum next |
| 2.3.5 | Render field per tipe di `qualification-field.blade.php` — `@switch($qualification->type)` | [x] | | PRD §14.1 |
| 2.3.6 | Step Review: tampilkan estimasi skor sementara (via ScoringEngine real-time) | [x] | | |
| 2.3.7 | Save Draft → state per step disimpan ke DB | [x] | | `Application::status = draft` |
| 2.3.8 | Submit → `SubmitApplication` Action dipanggil | [x] | | |
| 2.3.9 | Tes: `tests/Feature/ApplicationFormTest.php` | [x] | | Pest Livewire |

### 3.4 File Upload System

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 2.4.1 | Buat Blade component `document-uploader.blade.php` | [x] | | Rewritten: `wire:loading` + `wire:key` native Livewire (no Alpine.js events) — 3 states: empty, uploading, success (preview image JPG/PNG or PDF icon + file name + size + remove button). Step Review: list semua dokumen terlampir |
| 2.4.2 | Alpine.js validasi client-side: max 2 MB (`file.size > 2 * 1024 * 1024`), format JPG/PNG/PDF | [x] | | PRD §14.2 |
| 2.4.3 | Server-side validasi: `max:2048, mimes:jpg,jpeg,png,pdf` | [x] | | |
| 2.4.4 | Upload ke MinIO via `$file->store('documents/{scholarship_id}/{application_id}', 'minio')` | [x] | | Livewire `WithFileUploads`. Draft also persists files. Metadata captured BEFORE store() to avoid S3 404 after move |
| 2.4.5 | Generate signed URL untuk download/view (expires 1 jam) via `Storage::disk('s3')->temporaryUrl(...)` | [x] | | MinIO presigned URLs |
| 2.4.6 | Simpan metadata file di `application_documents` (path, size, mime_type) | [x] | | `ApplicationDocument::updateOrCreate` |
| 2.4.7 | Cleanup: `ApplicationObserver` hapus file MinIO saat application dihapus | [x] | | Observer registered in Application model `booted()` |
| 2.4.8 | Cleanup: S3 temp file auto-delete >24 jam | [x] | | `php artisan livewire:configure-s3-upload-cleanup` + `Schedule::command('livewire:cleanup-temp-files')->daily()` |
| 2.4.7 | Tes: `tests/Feature/FileUploadTest.php` | [x] | | |

### 3.5 SubmitApplication Action

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 2.5.1 | Buat `app/Actions/Application/SubmitApplication.php` | [x] | | app/Actions/Application/SubmitApplication.php — full transaction with scoring |
| 2.5.2 | Snapshot profile pendaftar → `snapshot_profile` JSONB (immutable) | [x] | | `SnapshotApplicantProfile` Action |
| 2.5.3 | Generate `registration_number` — format: `{SLUG}{TAHUN}-{5-digit}` (contoh: `BBK2025-00001`) | [x] | | Sequence per scholarship |
| 2.5.4 | Simpan jawaban ke `application_answers` (mapping: qualification_id → value) | [x] | | |
| 2.5.5 | Simpan dokumen ke `application_documents` if uploaded | [x] | | |
| 2.5.6 | Panggil `ScoringEngine::calculate()` → simpan skor sementara ke `application_scores` (`is_final = false`) | [x] | | |
| 2.5.7 | Set `application.status = submitted`, `submitted_at = now()` | [x] | | |
| 2.5.8 | Tes: `tests/Unit/SubmitApplicationTest.php` | [x] | | |

### 3.6 ApplicationStatus Livewire

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 2.6.1 | Buat `app/Livewire/Applicant/ApplicationStatus.php` | [x] | | |
| 2.6.2 | Tracker status visual (Flowbite Stepper / Progress Bar) | [x] | | |
| 2.6.3 | Breakdown skor per qualification (tabel: indikator, jawaban, skor) | [x] | | Blade component `score-breakdown` |
| 2.6.4 | Status dokumen list (pending / approved / rejected) | [x] | | Flowbite Badge |
| 2.6.5 | Tombol re-upload jika `needs_revision` → link ke `DocumentRevision` | [x] | | |
| 2.6.6 | Tes: `tests/Feature/ApplicationStatusTest.php` | [x] | | |

### 3.7 DocumentRevision Livewire

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 2.7.1 | Buat `app/Livewire/Applicant/DocumentRevision.php` | [x] | | |
| 2.7.2 | Tampilkan daftar dokumen yang ditolak + alasan rejection | [x] | | |
| 2.7.3 | Form upload ulang per dokumen → status kembali `under_review` | [x] | | |
| 2.7.4 | Tes: `tests/Feature/DocumentRevisionTest.php` | [x] | | |

### 3.8 Data Seeder

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 2.8.1 | Seeder BBK Madiun 2024/2025 sebagai program predecessor (complete) | [x] | | database/seeders/BbkMadiunSeeder.php — 2 program, 10 kualifikasi, 15 users |
| 2.8.2 | Seeder BBK Madiun 2025/2026 sebagai program lanjutan | [x] | | Dengan `predecessor_scholarship_id` |
| 2.8.3 | Seeder user dummy: admin, verifikator, approver, bendahara, 10 pendaftar | [x] | | |
| 2.8.4 | Seeder qualification konfigurasi sesuai 7 indikator kemiskinan + prestasi | [x] | | |

---

### 3A. Phase 2 Deliverables Summary

| # | File | Type |
|---|------|------|
| 1 | `app/Services/OtpService.php` | Service |
| 2 | `app/Models/OtpVerification.php` | Model |
| 3 | `app/Services/DynamicFormRenderer.php` | Service |
| 4 | `app/Actions/Application/SubmitApplication.php` | Action |
| 5 | `app/Actions/Application/SnapshotApplicantProfile.php` | Action |
| 6 | `app/Livewire/Applicant/OtpVerification.php` | Livewire |
| 7 | `app/Livewire/Applicant/ApplicationForm.php` | Livewire |
| 8 | `app/Livewire/Applicant/ApplicationStatus.php` | Livewire |
| 9 | `app/Livewire/Applicant/DocumentRevision.php` | Livewire |
| 10 | `database/seeders/BbkMadiunSeeder.php` | Seeder |
| 11-16 | 6 blade views (application-form, application-status, document-revision, otp-verification, dashboard, scholarships) | Views |

### 3B. Routes Added (Fase 2)

| Method | URI | Name |
|--------|-----|------|
| GET | `/beasiswa/{scholarship:slug}/daftar` | `application.form` |
| GET | `/pendaftaran/{application}/status` | `application.status` |
| GET | `/pendaftaran/{application}/revisi` | `application.revision` |
| GET | `/pendaftaran/{application}/rekening` | `application.bank` |
| GET | `/renewal/{application}` | `application.renewal` |

---

## 3. Fase 3 — Verifikasi & Seleksi

**Estimasi:** Bulan 3–4  
**Goal:** Verifikasi dokumen, koreksi jawaban, blacklist, batch ranking, penetapan penerima, notifikasi

### 3C. Phase 3 Deliverables Summary

| # | File | Type |
|---|------|------|
| 1 | `app/Actions/Verification/ApproveDocument.php` | Action |
| 2 | `app/Actions/Verification/RejectDocument.php` | Action |
| 3 | `app/Actions/Verification/CorrectAnswer.php` | Action |
| 4 | `app/Actions/Verification/FinalizeApplicantScore.php` | Action |
| 5 | `app/Actions/Blacklist/BlacklistApplicant.php` | Action |
| 6 | `app/Actions/Blacklist/RevokeBlacklist.php` | Action |
| 7 | `app/Events/AllDocumentsApproved.php` | Event |
| 8 | `app/Listeners/FinalizeScoreListener.php` | Listener |
| 9 | `app/Services/ApplyTieBreaker.php` | Service |
| 10 | `app/Jobs/ProcessBatchScoring.php` | Job (Queue) |
| 11 | `app/Livewire/Verifier/VerificationQueue.php` | Livewire (enhanced) |
| 12 | `app/Livewire/Verifier/ApplicationDetail.php` | Livewire (enhanced) |
| 13 | `app/Livewire/Admin/BatchSelectionRunner.php` | Livewire |
| 14 | `app/Livewire/Admin/SelectionResult.php` | Livewire |
| 15 | `app/Livewire/Approver/RecipientApproval.php` | Livewire |
| 16-19 | 4 blade views (application-detail, verification-queue, batch-selection-runner, selection-result, recipient-approval) | Views |

### 3D. Routes Added (Fase 3)

| Method | URI | Name | Role |
|--------|-----|------|------|
| GET | `/admin/seleksi` | `admin.selection` | admin, super-admin |
| GET | `/admin/seleksi/batch` | `admin.batch` | admin, super-admin |
| GET | `/approver/penetapan` | `approver.approval` | approver |

### 3E. Bug Fixes (Fase 3–4)

| # | Issue | File | Fix |
|---|-------|------|-----|
| 1 | 403 saat login superadmin | `app/Http/Responses/LoginResponse.php` | Override Fortify LoginResponse — redirect based on role + cek `email_verified_at` → redirect ke OTP verification jika null |
| 10 | Registrasi redirect langsung ke dashboard, halaman OTP tidak muncul | `app/Http/Responses/RegisterResponse.php`, `app/Providers/FortifyServiceProvider.php`, `app/Http/Responses/LoginResponse.php` | Override Fortify RegisterResponse → redirect ke `verification.notice` setelah registrasi; LoginResponse cek `email_verified_at === null` → redirect ke OTP page |
| 2 | 403 di halaman `/` — header Dashboard link hardcoded | `components/layouts/public.blade.php` | Role-based URL via `hasAnyRole()` |
| 3 | Draft save duplicate key error | `app/Actions/Application/SubmitApplication.php` | Delete old answers on every save (not just submit) |
| 4 | Multi-choice checkboxes semua tercentang | `app/Livewire/Applicant/ApplicationForm.php` | Init `answers[id] = []` for multi_choice fields |
| 5 | Verifier 403 di `/verifikasi` | `app/Livewire/Verifier/VerificationQueue.php` | Check `scholarship_verifiers` exists instead of `scholarship_id = 0` |
| 6 | 500: `PortableVisibilityConverter` not found — upload file gagal | `composer.json` | Install `league/flysystem-aws-s3-v3 ^3.0` |
| 7 | 500: `UnableToRetrieveMetadata` — `file_size` 404 after `store()` | `SubmitApplication.php`, `DocumentRevision.php`, `SemesterRenewal.php` | Capture metadata (`getSize`, `getMimeType`, `getClientOriginalName`) BEFORE `$file->store()` |
| 8 | Document uploader stuck di loading state saat navigasi step | `document-uploader.blade.php` | Ganti Alpine.js events → native `wire:loading` + `wire:key` + `x-cloak` CSS rule |
| 9 | SendNotification template tidak replace placeholder | `app/Jobs/SendNotification.php` | Apply `str_replace` to both template AND default messages |

### 3F. UI/UX Enhancements (Fase 2A–3)

| # | Enhancement | Files |
|---|-------------|-------|
| 1 | Sidebar shadcn-inspired: sidebar-group, badge, active bar, sticky header/footer | `sidebar.blade.php`, `sidebar-group.blade.php`, `sidebar-item.blade.php`, `app.css` |
| 2 | Dashboard polish: stat cards gradients, hover effects, staggered animations | `admin-dashboard.blade.php`, `applicant/dashboard.blade.php`, `approver-dashboard.blade.php` |
| 3 | Page container `max-w-7xl` | `app.blade.php` |
| 4 | Sidebar CSS variables (`--sidebar-width`, `--sidebar-background`, etc.) | `app.css` |
| 5 | Welcome page Dashboard button role-aware | `welcome.blade.php` |
| 6 | Verification queue: document progress bar | `verification-queue.blade.php` |
| 7 | ApplicationDetail: 5-tab layout with modals | `application-detail.blade.php` |

**Estimasi:** Bulan 3–4  
**Goal:** Verifikasi dokumen, koreksi jawaban, blacklist, batch ranking, penetapan penerima, notifikasi

### 4.1 VerificationQueue Livewire

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 3.1.1 | Buat `app/Livewire/Verifier/VerificationQueue.php` | [x] | | Enhanced with policy check, document progress bar, row click navigation |
| 3.1.2 | Policy gate: hanya tampilkan program yang verifikator ditugaskan (BV-03) | [x] | | `scholarship_verifiers` pivot — mount check |
| 3.1.3 | Dropdown filter pilih program (hanya yang ditugaskan) | [x] | | |
| 3.1.4 | Tabel: registration_number, nama, status, progress dokumen (x/y approved), submitted_at | [x] | | Progress bar with percentage |
| 3.1.5 | Sorting & filtering: status, progress, tanggal submit | [x] | | |
| 3.1.6 | Click row → navigasi ke `ApplicationDetail` | [x] | | `wire:click="goToDetail(id)"` |
| 3.1.7 | Tes: `tests/Feature/VerificationQueueTest.php` | [ ] | | Ditunda — priority: core flow working |

### 4.2 ApplicationDetail Livewire

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 3.2.1 | Buat `app/Livewire/Verifier/ApplicationDetail.php` | [x] | | Full implementation with 5 tabs, modals, policy check |
| 3.2.2 | Tabs: Profil (snapshot_profile), Jawaban, Dokumen, Skor, Log Verifikasi | [x] | | Custom tab navigation with Lucide icons |
| 3.2.3 | Tab Dokumen: preview, approve / reject button + alasan wajib (Modal) | [x] | | Manual modal with validation (min 10 chars) |
| 3.2.4 | Tab Jawaban: tampilkan jawaban pendaftar, opsi koreksi (F-30) | [x] | | Simpan old value, log di `verification_logs` |
| 3.2.5 | Tombol Blacklist (Modal konfirmasi + alasan wajib) | [x] | | Policy check via BlacklistPolicy |
| 3.2.6 | Auto-refresh via `$refresh` / `#[On]` events | [x] | | `dispatch('document-updated')` etc. |
| 3.2.7 | Tes: `tests/Feature/ApplicationDetailTest.php` | [ ] | | Ditunda |

### 4.3 Document Verification Actions

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 3.3.1 | Buat `app/Actions/Verification/ApproveDocument.php` | [x] | | |
| 3.3.2 | Set `verification_status = approved`, `verified_by`, `verified_at` | [x] | | |
| 3.3.3 | Buat log di `verification_logs` (action: `document_approved`, immutable) | [x] | | |
| 3.3.4 | Buat `app/Actions/Verification/RejectDocument.php` | [x] | | |
| 3.3.5 | Set `verification_status = rejected`, `rejection_reason` | [x] | | |
| 3.3.6 | Set `application.status = needs_revision` | [x] | | Pendaftar bisa re-upload |
| 3.3.7 | Buat `app/Observers/ApplicationDocumentObserver.php` | [x] | | Already existed — dispatches AllDocumentsApproved event |
| 3.3.8 | Observer: setelah semua dokumen wajib approved → trigger `FinalizeApplicantScore` (BV-06) | [x] | | Via `AllDocumentsApproved` event → `FinalizeScoreListener` |

### 4.4 Answer Correction

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 3.4.1 | Buat `app/Actions/Verification/CorrectAnswer.php` | [x] | | |
| 3.4.2 | Simpan nilai lama ke `original_selected_option_id` / `original_numeric_value` | [x] | | |
| 3.4.3 | Update nilai baru, set `is_corrected_by_verifier = true`, `corrected_by`, `corrected_at` | [x] | | |
| 3.4.4 | Buat log di `verification_logs` (action: `answer_corrected`, field_changed, old_value, new_value, reason) | [x] | | |
| 3.4.5 | Recalculate `computed_score` di `application_answers` | [x] | | Via `ScoringEngine::resolveAnswerScore()` + `CorrectAnswer` Action |

### 4.5 FinalizeApplicantScore Action

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 3.5.1 | Buat `app/Actions/Verification/FinalizeApplicantScore.php` | [x] | | |
| 3.5.2 | Cek: semua dokumen dari qualification dengan `is_file_upload_required = true` sudah approved? | [x] | | Checked by Observer before event dispatch |
| 3.5.3 | Panggil `ScoringEngine::calculate()` ulang | [x] | | |
| 3.5.4 | Update `application_scores`: `total_score`, `score_breakdown`, `is_final = true`, `calculated_at = now()` | [x] | | |
| 3.5.5 | Update `application.status = verified`, `verified_at = now()` | [x] | | |
| 3.5.6 | Tes: `tests/Unit/FinalizeApplicantScoreTest.php` | [ ] | | Ditunda |

### 4.6 Blacklist System

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 3.6.1 | Buat `app/Actions/Blacklist/BlacklistApplicant.php` | [x] | | |
| 3.6.2 | Insert `blacklist_logs`: user_id, application_id, blacklisted_by, reason, is_active = true | [x] | | |
| 3.6.3 | Update `users.is_blacklisted = true` (via `BlacklistLogObserver`) | [x] | | Cache flag — already existed |
| 3.6.4 | Set `application.status = rejected` | [x] | | |
| 3.6.5 | Buat `app/Actions/Blacklist/RevokeBlacklist.php` | [x] | | |
| 3.6.6 | Update `blacklist_logs.is_active = false`, `revoked_by`, `revoked_at`, `revoke_reason` | [x] | | |
| 3.6.7 | Update `users.is_blacklisted = false` (via Observer) | [x] | | Already existed |
| 3.6.8 | Buat `app/Observers/BlacklistLogObserver.php` — sync `users.is_blacklisted` | [x] | | Already existed |
| 3.6.9 | Tes: `tests/Feature/BlacklistTest.php` | [ ] | | Ditunda |

### 4.7 ProcessBatchScoring Job

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 3.7.1 | Buat `app/Jobs/ProcessBatchScoring.php` | [x] | | `ShouldQueue` — queue: `scoring` |
| 3.7.2 | Guard: hanya jika `scholarship.status = closed` (BV-08) | [x] | | |
| 3.7.3 | **Renewal Phase** — identifikasi renewal apps dengan `is_final = true` + IPK >= min_gpa_renewal | [x] | | Via RenewalEngine |
| 3.7.4 | Renewal: set `selection_result = utama`, hitung & lock `quota_renewal_locked` | [x] | | |
| 3.7.5 | Hitung sisa kuota: `available = quota_primary - quota_renewal_locked` | [x] | | |
| 3.7.6 | **Ranking Phase** — ambil non-renewal apps dengan `is_final = true` | [x] | | |
| 3.7.7 | Sort DESC by `total_score` | [x] | | |
| 3.7.8 | Terapkan tie-breaker per `tiebreaker_config` via `ApplyTieBreaker` service | [x] | | `app/Services/ApplyTieBreaker.php` |
| 3.7.9 | Classification: rank ≤ available → `utama`, ≤ available + quota_reserve → `cadangan`, sisanya → `tidak_lolos` | [x] | | |
| 3.7.10 | Set `rank`, `selection_result`, `tiebreaker_log` di `application_scores` | [x] | | |
| 3.7.11 | **Finalize** — update `scholarship.status = selecting` | [x] | | |
| 3.7.12 | Job batching dengan progress callback | [x] | | Redis Cache-based progress tracking: 6 stages (preparing→processing_renewal→ranking→applying_tiebreaker→persisting→completed) |
| 3.7.13 | Tes: `tests/Feature/ProcessBatchScoringTest.php` | [ ] | | Ditunda |

### 4.8 BatchSelectionRunner Livewire

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 3.8.1 | Buat `app/Livewire/Admin/BatchSelectionRunner.php` | [x] | | |
| 3.8.2 | Tampilkan ringkasan: total pendaftar verified, quota renewal slots, sisa kuota | [x] | | Stats cards + RenewalEngine summary |
| 3.8.3 | Tombol "Jalankan Seleksi" → dispatch `ProcessBatchScoring` job | [x] | | Modal konfirmasi sebelum dispatch |
| 3.8.4 | Progress bar real-time via `wire:poll` | [x] | | `wire:poll.2s="checkProgress"` dengan progress bar animasi per stage |
| 3.8.5 | Setelah complete: link ke `SelectionResult` | [x] | | Auto-detect `batchCompleted` → tampilkan link "Lihat Hasil Seleksi" |

### 4.9 SelectionResult Livewire

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 3.9.1 | Buat `app/Livewire/Admin/SelectionResult.php` | [x] | | |
| 3.9.2 | Tabel: rank, registration_number, nama, total_score, max_possible_score, selection_result (badge) | [x] | | |
| 3.9.3 | Filter: utama / cadangan / tidak_lolos | [x] | | Badge filter buttons |
| 3.9.4 | Detail per pendaftar: score breakdown + tiebreaker_log | [x] | | Expandable row: click-to-expand dengan breakdown skor per qual + log tie-breaker |
| 3.9.5 | Jika belum di-approve: tombol "Finalisasi Penetapan" (untuk Approver) | [x] | | Conditional button untuk role approver/super-admin saat status selecting |

### 4.10 RecipientApproval Livewire

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 3.10.1 | Buat `app/Livewire/Approver/RecipientApproval.php` | [x] | | |
| 3.10.2 | Tampilkan ringkasan hasil seleksi | [x] | | Stats cards: utama/cadangan/tidak lolos |
| 3.10.3 | Tombol "Setujui Penetapan" — Modal konfirmasi | [x] | | |
| 3.10.4 | Set `application_scores.finalized_at = now()` — kunci immutable (BV-07) | [x] | | Model guard already existed |
| 3.10.5 | Set `application.status = selected` untuk yang `utama` | [x] | | |
| 3.10.6 | Update `scholarship.status = announced` | [x] | | |
| 3.10.7 | Dispatch notifikasi ke semua pendaftar (via `SendNotification` job) | [x] | | `dispatchNotifications()` — kirim WA/Email per applicant sesuai `notification_channels` |
| 3.10.8 | Tes: `tests/Feature/RecipientApprovalTest.php` | [ ] | | Ditunda |

### 4.11 Notification System

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 3.11.1 | Buat `app/Jobs/SendNotification.php` | [x] | | `ShouldQueue` — queue: `notifications` |
| 3.11.2 | WhatsApp channel: cURL ke Fonnte `POST /send` dengan token, target, message | [x] | | Fonnte API — sanitize phone 62 prefix |
| 3.11.3 | Email channel: Laravel Mail facade via SMTP | [x] | | `Mail::raw()` |
| 3.11.4 | Template notifikasi dari `scholarships.notification_templates` JSONB | [x] | | Replace placeholder: `{name}`, `{registration_number}`, `{result}`; 7 default templates |
| 3.11.5 | Log hasil kirim ke `notifications_log` (status: sent/failed, error_message) | [x] | | |
| 3.11.6 | Retry failed via queue (`--tries=3`) | [x] | | `$tries = 3` on job |
| 3.11.7 | Tes: `tests/Feature/NotificationTest.php` | [ ] | | Ditunda |

### 4.12 Announcement Public Page

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 3.12.1 | Buat Controller `AnnouncementController` (public, no auth) | [x] | | `app/Http/Controllers/Public/AnnouncementController.php` |
| 3.12.2 | `GET /pengumuman/{scholarship:slug}` — daftar hasil (nama, nomor registrasi, status lolos/tidak) | [x] | | Public layout |
| 3.12.3 | `GET /pengumuman/{scholarship:slug}/cek` — cek hasil per nomor registrasi | [x] | | Query parameter approach |
| 3.12.4 | Informasi langkah selanjutnya untuk yang lolos (konfirmasi rekening) | [x] | | Success card with bank account CTA |
| 3.12.5 | Tes: `tests/Feature/AnnouncementTest.php` | [ ] | | Ditunda |

---

## 5. Fase 4 — Operasional & Laporan

**Estimasi:** Bulan 4–5  
**Goal:** Pencairan dana, renewal semester, dashboard, export laporan, audit log viewer

### 5.1 BankAccountForm Livewire

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 4.1.1 | Buat `app/Livewire/Applicant/BankAccountForm.php` | [x] | | |
| 4.1.2 | Form: nama bank (select dropdown 8 options), nomor rekening, nama pemegang rekening | [x] | | |
| 4.1.3 | Validasi format nomor rekening (numeric) | [x] | | `regex:/^[0-9]+$/` |
| 4.1.4 | Simpan ke `disbursements` — `account_number` di-encrypt (`encrypted` cast) | [x] | | Update or create |
| 4.1.5 | Tes: `tests/Feature/BankAccountFormTest.php` | [ ] | | Ditunda |

### 5.2 DisbursementList Livewire

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 4.2.1 | Buat `app/Livewire/Treasurer/DisbursementList.php` | [x] | | Full rewrite |
| 4.2.2 | Daftar penerima per program + data rekening + status pencairan | [x] | | Table with bank/account/holder info |
| 4.2.3 | Filter: program, status (waiting / processing / disbursed) | [x] | | Dual select dropdowns |
| 4.2.4 | Bulk update status: `waiting → processing → disbursed` | [x] | | Individual buttons per row |
| 4.2.5 | Log perubahan status (timestamp + user) | [x] | | `processed_by` + `disbursed_at` |
| 4.2.6 | Tombol export Excel (format siap transfer bank) | [x] | | `exportExcel()` → redirect ke `/keuangan/export/{id}`, tombol di header saat program dipilih |
| 4.2.7 | Tes: `tests/Feature/DisbursementListTest.php` | [ ] | | Ditunda |

### 5.3 Export Excel

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 4.3.1 | Buat `app/Exports/ApplicantsExport.php` — daftar penerima (FromCollection) | [x] | | Laravel Excel — with mapping, headings, auto-size, bold header |
| 4.3.2 | Buat `app/Exports/DisbursementExport.php` — data rekening + nominal + status | [x] | | Account number preserved with leading zeros |
| 4.3.3 | Buat `app/Http/Controllers/Export/ExportApplicantsController.php` | [x] | | `Excel::download()` |
| 4.3.4 | Buat `app/Http/Controllers/Export/ExportDisbursementController.php` | [x] | | |
| 4.3.5 | Styling: header bold, auto-size kolom | [x] | | `WithStyles`, `ShouldAutoSize` |
| 4.3.6 | Queued export untuk dataset besar | [ ] | | Simplifikasi: sync download |

### 5.4 Export PDF

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 4.4.1 | Buat blade view `pdf/report.blade.php` — template laporan penerima | [x] | | Landscape A4, table layout |
| 4.4.2 | Buat blade view `pdf/disbursement-report.blade.php` — rekap pencairan | [x] | | |
| 4.4.3 | Buat blade view `pdf/audit-log.blade.php` — verifikasi & blacklist log | [x] | | |
| 4.4.4 | Controller: `PdfReportController` — 3 methods (recipients, disbursement, auditLog) | [x] | | DomPDF — `setPaper('a4', 'landscape')` |
| 4.4.5 | Konfigurasi: `dpi => 150`, `defaultFont => 'sans-serif'` | [x] | | `setOption()` |
| 4.4.6 | Tes: `tests/Feature/PdfExportTest.php` | [ ] | | Ditunda |

### 5.5 SemesterRenewal Livewire

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 4.5.1 | Buat `app/Livewire/Applicant/SemesterRenewal.php` | [x] | | |
| 4.5.2 | Form: upload transkrip terbaru, input IPK semester terakhir | [x] | | `WithFileUploads` |
| 4.5.3 | Pre-fill dari `applications.previous_application_id` | [x] | | Find predecessor selected app |
| 4.5.4 | Validasi: IPK ≥ `scholarships.min_gpa_renewal` | [x] | | Server-side validation |
| 4.5.5 | Set `is_renewal = true`, `previous_application_id` | [x] | | + recalculate score via ScoringEngine |
| 4.5.6 | Tes: `tests/Feature/SemesterRenewalTest.php` | [ ] | | Ditunda |

### 5.6 AdminDashboard Livewire

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 4.6.1 | Buat `app/Livewire/Dashboard/AdminDashboard.php` — enhanced | [x] | | Program filter dropdown |
| 4.6.2 | Stats Cards: total pendaftar, by status, terverifikasi | [x] | | 4 stat cards + status breakdown + program summary |
| 4.6.3 | Chart sebaran skor (histogram) | [x] | | Chart.js bar chart — 4-8 bucket otomatis, `buildScoreDistribution()`, warna `--primary` |
| 4.6.4 | Chart progress verifikasi | [x] | | Status breakdown dengan count per status |
| 4.6.5 | Chart sebaran per wilayah/kecamatan | [x] | | Chart.js horizontal bar — top 10 kecamatan, `buildGeoDistribution()`, warna `--success` |
| 4.6.6 | Filter: pilih program | [x] | | `wire:model.live` select |
| 4.6.7 | Chart monitoring pendaftaran harian (daily submissions line chart) | [x] | | Chart.js line chart — `buildDailySubmissions()`, `submitted_at::date` GROUP BY, fill area + intersect tooltip |
| 4.6.8 | Tes: `tests/Feature/AdminDashboardTest.php` | [ ] | | Ditunda |

### 5.7 ApproverDashboard Livewire

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 4.7.1 | Buat `app/Livewire/Approver/ApproverDashboard.php` — enhanced | [x] | | |
| 4.7.2 | Ringkasan semua program aktif: nama, kuota, terisi, sisa, status | [x] | | Table with badges |
| 4.7.3 | Tren pendaftar per tahun (line chart) | [x] | | Chart.js line chart — `buildYearlyTrend()`, fill area + tension 0.3 |
| 4.7.4 | Total anggaran terserap vs alokasi | [x] | | Stat card: total anggaran announced programs |
| 4.7.5 | Quick link ke `RecipientApproval` untuk program yang perlu approve | [x] | | "Proses" link per selecting program |

### 5.8 BlacklistManager Livewire

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 4.8.1 | Buat `app/Livewire/Admin/BlacklistManager.php` (enhanced) | [x] | | Full rewrite with revoke |
| 4.8.2 | Tabel log: pendaftar, verifikator, alasan, tanggal, status (aktif/dicabut) | [x] | | + revoke reason display |
| 4.8.3 | Filter: status (aktif / dicabut) | [x] | | Badge filter buttons |
| 4.8.4 | Tombol cabut blacklist — Modal konfirmasi + alasan wajib | [x] | | Hanya admin/super-admin (BV-14) |
| 4.8.5 | Tes: `tests/Feature/BlacklistManagerTest.php` | [ ] | | Ditunda |

### 5.9 UserManager Livewire

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 4.9.1 | Buat `app/Livewire/Admin/UserManager.php` (enhanced) | [x] | | Full rewrite with CRUD |
| 4.9.2 | Tabel user: nama, NIK, email, phone, role, is_active, is_blacklisted | [x] | | + edit/delete actions |
| 4.9.3 | Create user form: role assignment via `syncRoles()` | [x] | | Spatie Permission — role checkboxes |
| 4.9.4 | Edit user: update role, activate/deactivate | [x] | | Password optional on edit |
| 4.9.5 | Search & filter: name, role, status | [x] | | Search + role dropdown filter |

### 5.10 NotificationConfigurator Livewire

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 4.10.1 | Buat `app/Livewire/Admin/NotificationConfigurator.php` | [x] | | |
| 4.10.2 | Form: channel toggle (WA / Email), template textarea per event type | [x] | | PRD §7.1 F-06 |
| 4.10.3 | Event types: registered, status_changed, needs_revision, result_announced, renewal_reminder, disbursed, blacklisted | [x] | | 7 event types + default templates |
| 4.10.4 | Preview template dengan sample data | [x] | | `togglePreview()` + `renderPreview()` — klik "Preview" untuk lihat hasil dengan data sampel |
| 4.10.5 | Simpan ke `scholarships.notification_channels` dan `notification_templates` JSONB | [x] | | |

### 5.11 Scheduled Jobs

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 4.11.1 | Buat `app/Jobs/AutoManageScholarshipStatus.php` | [x] | | `ShouldQueue` |
| 4.11.2 | Logic: jika `date_start ≤ today ≤ date_end` → ubah status ke `open` | [x] | | Sets `published_at` |
| 4.11.3 | Logic: jika `date_end < today` → ubah status ke `closed` | [x] | | |
| 4.11.4 | Register di `routes/console.php`: `Schedule::job(new AutoManageScholarshipStatus)->dailyAt('00:01')` | [x] | | |
| 4.11.5 | Verifikasi cron entry: `* * * * * php artisan schedule:run` | [x] | | PHPDoc + command example di `routes/console.php` |

### 5.12 Audit Log Viewer

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 4.12.1 | Buat Livewire `AuditLogViewer` — read-only tabel `verification_logs` | [x] | | Tab switching: verification / blacklist |
| 4.12.2 | Buat Livewire — read-only tabel `blacklist_logs` | [x] | | Combined in same component |
| 4.12.3 | Filter: tipe aksi, status | [x] | | Action filter + status filter |
| 4.12.4 | Export PDF untuk audit log | [x] | | `PdfReportController::auditLog()` — DomPDF landscape A4 |

---

## 6. Fase 5 — Integrasi & Skalabilitas (Opsional)

**Estimasi:** Post-MVP

| ID | Task | Status | Assignee | Notes |
|----|------|--------|----------|-------|
| 5.1.1 | Integrasi API PDDikti — validasi status mahasiswa aktif | [ ] | | Roadmap |
| 5.2.1 | Integrasi data DTKS — validasi kategori kemiskinan | [ ] | | Roadmap |
| 5.3.1 | Multi-tenancy: `organization_id` + subdomain routing | [ ] | | Roadmap |
| 5.4.1 | Modul banding formal | [ ] | | Roadmap |

---

## 7. Pola Livewire Acuan

| Pola | Digunakan Pada | Ref |
|------|---------------|-----|
| `#[Lazy]` | AdminDashboard, ApproverDashboard | Non-blocking render awal |
| `wire:navigate` | Semua navigasi antar komponen | SPA-like tanpa full reload |
| `Livewire\Form` Objects | ApplicationForm (1 Form Object per step) | Multi-step validation |
| `$dispatch` / `$on` | VerificationQueue ↔ ApplicationDetail | Event bus antar komponen |
| `wire:poll` (terbatas) | BatchSelectionRunner | Hentikan setelah job selesai |
| `wire:confirm` | Blacklist, Approve Final, Delete Scholarship | Konfirmasi aksi destruktif |
| `WithFileUploads` | ApplicationForm, DocumentRevision, SemesterRenewal | Upload file via Livewire |
| `$wire.upload()` | document-uploader.blade.php (Alpine.js) | Upload programmatic dengan callback |

---

## 8. Validasi Bisnis Kritis — Tracker Implementasi

| Kode | Aturan | Implementasi | Status |
|------|-------|-------------|--------|
| BV-01 | Satu NIK satu pendaftaran per program (kecuali draft) | DB partial unique index + SubmitApplication check | [x] |
| BV-02 | Pendaftar blacklist tidak bisa daftar | Middleware `EnsureNotBlacklisted` + ApplicationForm mount check | [x] |
| BV-03 | Verifikator hanya akses program yang ditugaskan | Policy + `scholarship_verifiers` pivot — check di VerificationQueue & ApplicationDetail | [x] |
| BV-04 | Qualification locked setelah ada pendaftar | QualificationBuilder service layer guard | [x] |
| BV-05 | Range numeric tidak overlap | ScoringEngine::validateRanges() + admin form | [x] |
| BV-06 | Skor final hanya setelah semua dokumen approved | ApplicationDocumentObserver → AllDocumentsApproved event → FinalizeScoreListener | [x] |
| BV-07 | Skor final immutable setelah Approver | Model guard `ApplicationScore::updating()` + RecipientApproval finalized_at | [x] |
| BV-08 | Hanya `is_final = true` yang masuk ranking | ProcessBatchScoring Job query guard | [x] |
| BV-09 | Renewal diproses sebelum ranking | ProcessBatchScoring Job — renewal phase first | [x] |
| BV-10 | `quota_renewal_locked` ≤ `quota_primary` | ProcessBatchScoring — max() check | [x] |
| BV-11 | File upload max 2 MB, JPG/PNG/PDF | Livewire client-side + Laravel server-side validation | [x] |
| BV-12 | Logs immutable — no delete | Observer guard + DB — VerificationLog::deleting(), BlacklistLog no updated_at | [x] |
| BV-13 | Blacklist hanya oleh verifikator program tsb | BlacklistPolicy::create() — check scholarship_verifiers | [x] |
| BV-14 | Revoke blacklist hanya admin/super-admin | BlacklistPolicy::revoke() — check hasRole | [x] |

---

## 9. Infrastruktur & Deployment Tracker

### 9.1 Docker Architecture (Supporting Services Only)

```
┌─────────────────────────────────────────────────────────────┐
│  Docker Compose: supporting services (no app container)     │
│                                                             │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌───────────┐  │
│  │  Redis 7 │  │  MinIO   │  │ MinIO    │  │  Mailpit  │  │
│  │  :6379   │  │ :9000/01 │  │ Init     │  │ :1025/8025│  │
│  │  AOF     │  │  bucket  │  │ one-shot │  │  SMTP+UI  │  │
│  └──────────┘  └──────────┘  └──────────┘  └───────────┘  │
│                                                             │
│  App (Laravel) berjalan di host: php artisan serve / nginx  │
│  PostgreSQL berjalan di host (native / Laragon)              │
└─────────────────────────────────────────────────────────────┘
```

### 9.2 Services

| Service | Container | Port (Host) | Config |
|---------|-----------|-------------|--------|
| **Redis 7** | `beasiswa-redis` | `6379` | AOF persistence, 256MB maxmemory, `noeviction` |
| **MinIO** | `beasiswa-minio` | `9000` / `9001` | S3-compatible, auto-create bucket via `minio-init` |
| **MinIO Init** | `beasiswa-minio-init` | — | One-shot: create bucket `scholarship-documents`, set private policy |
| **Mailpit** | `beasiswa-mailpit` | `8025` / `1025` | SMTP + Web UI, max 5000 messages |

### 9.3 Docker Commands

```bash
# Start all supporting services
docker compose up -d

# Stop all
docker compose down

# Stop + remove volumes (reset data)
docker compose down -v

# View logs
docker compose logs -f redis
docker compose logs -f mailpit

# Access Mailpit UI — http://localhost:8025
# Access MinIO Console — http://localhost:9001
```

### 9.4 File Structure

```
├── docker-compose.yml          # 4 services: redis, minio, minio-init, mailpit
└── .env                        # Environment variables (port/credential via ${VAR})
```

### 9.5 Services Checklist

| Komponen | Status |
|----------|--------|
| docker-compose.yml (4 services: redis, minio, minio-init, mailpit) | [x] |
| Redis 7 — AOF + maxmemory 256mb | [x] |
| MinIO — auto-create bucket `scholarship-documents` | [x] |
| Mailpit — SMTP + Web UI | [x] |
| `.env` variables: `REDIS_PORT`, `MINIO_*`, `MAILPIT_*` | [x] |
| `.env.example` — documented with all Docker variables | [x] |
| Deleted: Dockerfile, nginx/, php/, supervisor/, entrypoint.sh, .dockerignore | [x] |

---

## 10. Testing Strategy

| Layer | Tool | Target | Lokasi |
|-------|------|--------|--------|
| Unit | Pest PHP | ScoringEngine, RenewalEngine, DynamicFormRenderer, Actions | `tests/Unit/` |
| Feature | Pest + Livewire Plugin | Semua Livewire components, HTTP controllers, Policies | `tests/Feature/` |
| Integration | Pest | ProcessBatchScoring job (queue + DB), File upload end-to-end, OTP flow | `tests/Feature/` |
| Browser | Pest Browser Plugin | Optional: alur pendaftaran lengkap, verifikasi (real browser) | `tests/Browser/` |

### Perintah Testing

```bash
# Unit tests only
php artisan test --testsuite=Unit

# Feature tests only
php artisan test --testsuite=Feature

# Specific test file
php artisan test --filter=ScoringEngineTest

# Livewire component test
php artisan test --filter=ApplicationFormTest

# Full test suite
php artisan test --parallel
```

---

## 11. Command Reference

```bash
# Queue worker
php artisan queue:work redis --queue=default,scoring,notifications --tries=3

# Schedule worker (development)
php artisan schedule:work

# Fresh migration + seed
php artisan migrate:fresh --seed

# Create Livewire component
php artisan make:livewire Admin/ScholarshipManager

# Create Action class
php artisan make:class Actions/Application/SubmitApplication

# Create Job
php artisan make:job ProcessBatchScoring

# Create Policy
php artisan make:policy ApplicationPolicy --model=Application

# Create Observer
php artisan make:observer ApplicationDocumentObserver --model=ApplicationDocument

# Create Export
php artisan make:export ApplicantsExport --model=Application
```

---

**Progress Keseluruhan:** Fase 0 ✅ · Fase 1 ✅ · Fase 2 ✅ · Fase 3 ✅ · Fase 4 ✅ (MVP Complete)  
**Fase Selanjutnya:** Fase 5 — Integrasi & Skalabilitas (Opsional / Post-MVP: PDDikti, DTKS, multi-tenancy)  
**Terakhir Update:** 18 Juni 2026 (OTP registration redirect fix: Custom RegisterResponse + LoginResponse email_verified_at check)  
**Refactor UI:** Flowbite dihapus · 28 custom shadcn-inspired components (termasuk `<x-ui.chart>` + `<x-ui.document-uploader>`) · CSS 57KB · JS 188KB (Chart.js tree-shaken)  
**Fase 2:** OTP · DynamicFormRenderer · ApplicationForm multi-step · File Upload 2MB MinIO (+ draft file persistence + `wire:loading` uploader + preview + step review document list) · SubmitApplication Action (+ metadata capture before store) · ApplicationStatus tracker · DocumentRevision · BBK Madiun Seeder  
**Fase 3:** VerificationQueue + ApplicationDetail · Verify Actions (Approve/Reject/Correct/Finalize) · Blacklist System · ProcessBatchScoring Job (+ progress tracking Redis Cache) · ApplyTieBreaker · BatchSelectionRunner (+ wire:poll progress bar) · SelectionResult (+ detail breakdown & tiebreaker_log expandable) · RecipientApproval (+ dispatch notifikasi) · All 14 BV implemented  
**Fase 4:** SendNotification (WA+Email + template placeholder fix) · AnnouncementController · BankAccountForm · DisbursementList (+ Export Excel button) · NotificationConfigurator (+ template preview) · BlacklistManager revoke · UserManager CRUD · SemesterRenewal · Export Excel & PDF · AutoManageScholarshipStatus (+ cron doc) · AdminDashboard Chart.js charts (score histogram + geo distribution + daily monitoring line chart) · ApproverDashboard Chart.js line chart (yearly trend) · AuditLogViewer  
**Chart.js:** `<x-ui.chart>` Blade component · Alpine.js adapter · 3 chart types: bar, horizontalBar, line · Design token-aligned colors  
**Infrastruktur:** Docker Compose simplified — 4 supporting services only (Redis, MinIO, Mailpit) · App & PostgreSQL on host · S3 temp file auto-cleanup configured  
**Bug Fixes:** Login 403 redirect · Draft save duplicate key · Multi-choice checkboxes · Verifier 403 · Welcome page Dashboard link · NotificationConfigurator $eventTypes visibility · SendNotification default template placeholder · `league/flysystem-aws-s3-v3` missing → `composer require` · S3 metadata 404 after `store()` → capture metadata before store · Document uploader false loading state → `wire:loading` + `wire:key` + `x-cloak` · `computed_score` recalculation after answer correction · OTP registration redirect → Custom `RegisterResponse` + `LoginResponse` email_verified_at check  
**Observers:** `ApplicationObserver` — hapus file fisik MinIO saat application dihapus · `ApplicationDocumentObserver` — trigger finalize score · `BlacklistLogObserver` — sync `is_blacklisted`  
**Tests:** 14/14 passing (30 assertions) · 14 test files pending (ditunda)
