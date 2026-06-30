# Platform Beasiswa

Sistem manajemen beasiswa berbasis web untuk mengelola siklus penuh program beasiswa — dari pendaftaran, verifikasi dokumen, penilaian, seleksi, pencairan dana, hingga pengumuman. Dibangun dengan **Laravel 13** dan **Livewire 4**.

Aplikasi ini awalnya dikembangkan untuk program **BBK (Bantuan Beasiswa Kuliah)**.

## Fitur

### Manajemen Program Beasiswa
- CRUD program beasiswa dengan status: `draft` → `open` → `renewal_open` → `renewal_closed` → `closed` → `selecting` → `announced`
- Pelacakan tahun akademik, jumlah dana, kuota utama & cadangan
- Program berbasis perpanjangan (renewal) dengan tautan ke program sebelumnya
- Pengubahan status otomatis via jadwal harian

### Dynamic Qualification Builder
- Admin membuat formulir kualifikasi dinamis per program
- Tipe kualifikasi: pilihan tunggal, pilihan ganda, rentang numerik, teks, unggah berkas
- Formulir multi-langkah (per grup kualifikasi)
- Setiap kualifikasi dapat diskor dengan bobot nilai

### Pendaftaran & Aplikasi
- Wizard pendaftaran multi-langkah dengan penyimpanan draf
- Snapshot profil pelamar saat pengajuan
- Nomor registrasi otomatis (prefix + tahun + 8 karakter acak)
- Pemblokiran pelamar yang masuk daftar hitam

### Verifikasi Dokumen
- Penugasan verifikator ke program beasiswa
- Antrean verifikasi dengan filter per status
- Verifikasi tingkat dokumen (setuju/tolak dengan alasan)
- Koreksi jawaban oleh verifikator
- Log verifikasi immutable (tidak bisa dihapus)
- Finalisasi otomatis saat semua dokumen disetujui

### Mesin Penilaian & Seleksi
- Perhitungan skor per jawaban dan total
- Hasil skor dengan rincian JSONB dan log tiebreaker
- Seleksi batch otomatis: pemrosesan perpanjangan → pemeringkatan → penetapan utama/cadangan
- Konfigurasi tiebreaker

### Persetujuan Penerima
- Approver meninjau dan menyetujui daftar penerima
- Persetujuan penuh mengubah status program menjadi `announced`

### Pencairan Dana
- Manajemen pencairan oleh bendahara
- Informasi rekening bank (tersandi/encrypted)
- Status pencairan: `waiting`, `disbursed`, `failed`
- Ekspor PDF laporan pencairan

### Notifikasi
- Konfigurasi saluran notifikasi per program (WhatsApp via Fonnte, Email)
- Verifikasi OTP untuk email dan WhatsApp
- Log notifikasi untuk audit trail

### Laporan & Ekspor
- PDF: daftar penerima, rekap pencairan, log audit
- CSV: ekspor data pelamar dan pencairan

### Keamanan
- Laravel Fortify: registrasi, login, reset password
- Otentikasi dua faktor (TOTP)
- Passkeys (WebAuthn) untuk login tanpa sandi
- Pembatasan kecepatan: 5 percobaan login/menit
- Bidang tersandi (encrypted): NIK, nomor rekening
- Verifikasi email/telepon via OTP
- Manajemen sesi via Redis

## Role Pengguna

| Role | Deskripsi |
|------|-----------|
| `super-admin` | Super Administrator — akses penuh sistem |
| `admin` | Administrator Program — mengelola beasiswa, kualifikasi, pengguna, seleksi |
| `verifier` | Verifikator — memverifikasi aplikasi dan dokumen |
| `approver` | Approver / Kepala — menyetujui daftar penerima akhir |
| `treasurer` | Bendahara — mengelola pencairan dana |
| `applicant` | Pendaftar — mendaftar, unggah dokumen, perpanjangan |

## Tech Stack

### Backend
| Teknologi | Versi |
|-----------|-------|
| **Laravel** | ^13.8 (PHP ^8.4) |
| **Livewire** | ^4.3 |
| **Laravel Fortify** | ^1.37 |
| **Spatie Laravel Permission** | ^8.0 |
| **Predis** | ^3.5 (Redis client) |
| **Laravel DomPDF** | ^3.1 |
| **League Flysystem AWS S3 v3** | MinIO storage driver |
| **Pest** | ^4.7 (testing) |
| **Database** | PostgreSQL |

### Frontend
| Teknologi | Keterangan |
|-----------|------------|
| **Tailwind CSS** | v4 (via Vite plugin) |
| **Vite** | ^8.0 |
| **ApexCharts** | ^5.15 (dashboard charts) |
| **Blade Lucide Icons** | ^1.26 |
| **Blade + Livewire** | No JS framework — semua interaktivitas via Livewire |

## Persyaratan Sistem

- PHP ^8.4
- Composer
- Node.js & npm (untuk frontend build)
- PostgreSQL
- Docker Desktop (untuk Redis, MinIO, Mailpit)
- Laragon (opsional, untuk development lokal)

## Memulai

### 1. Clone & Install Dependencies

```bash
git clone <repository-url>
cd mono-scholarship
composer install
npm install
```

### 2. Konfigurasi Lingkungan

```bash
cp .env.example .env
php artisan key:generate
```

Sesuaikan `.env` dengan lingkungan Anda. Lihat [Konfigurasi Lingkungan](#konfigurasi-lingkungan) untuk detail.

### 3. Jalankan Services Pendukung (Docker)

```bash
docker compose up -d
```

Services yang berjalan:
| Service | Port | Fungsi |
|---------|------|--------|
| **Redis 7** | 6379 | Cache / Queue / Session |
| **MinIO** | 9000 (API), 9001 (Console) | S3-compatible storage |
| **Mailpit** | 1025 (SMTP), 8025 (Web UI) | Email testing |

### 4. Setup Awal

```bash
# Setup cepat (migrate + seed + build)
composer setup

# Atau manual:
php artisan migrate
php artisan db:seed --class=RoleSeeder
php artisan storage:link
npm run build
```

### 5. Jalankan Aplikasi

```bash
# Development (app server + queue + Vite HMR)
composer dev

# Atau manual:
php artisan serve
php artisan queue:listen
npm run dev
```

Akses aplikasi di **http://localhost:8000**

### 6. Akun Demo

Setelah menjalankan seeder:

| Role | Email | Password |
|------|-------|----------|
| Super Admin | `superadmin@beasiswa.test` | `password` |
| Admin | `admin@beasiswa.test` | `password` |
| Verifier | `verifier1@beasiswa.test` | `password` |
| Approver | `approver@beasiswa.test` | `password` |
| Treasurer | `treasurer@beasiswa.test` | `password` |
| Applicant | `applicant1@beasiswa.test` s.d. `applicant10@beasiswa.test` | `password` |

Seeder demo (`BbkMadiunSeeder`) juga membuat 2 program beasiswa contoh dengan kualifikasi lengkap.

## Konfigurasi Lingkungan

### Variabel .env Utama

```env
# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=mono_scholarship
DB_USERNAME=postgres
DB_PASSWORD=secret

# Redis (queue, cache, session)
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
QUEUE_CONNECTION=redis
CACHE_STORE=redis
SESSION_DRIVER=redis

# MinIO (S3-compatible storage)
FILESYSTEM_DISK=minio
MINIO_ENDPOINT=http://127.0.0.1:9000
MINIO_ACCESS_KEY=minioadmin
MINIO_SECRET_KEY=minioadmin
MINIO_BUCKET=scholarship-documents

# Mailpit (SMTP testing)
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025

# Fonnte (WhatsApp API)
FONNTE_TOKEN=
```

### Skrip NPM

| Perintah | Keterangan |
|----------|------------|
| `npm run dev` | Jalankan Vite HMR untuk development |
| `npm run build` | Build production assets |

## Docker

### Hanya Services Pendukung (Development Lokal)

Gunakan `docker-compose.yml` untuk menjalankan Redis, MinIO, dan Mailpit — aplikasi Laravel berjalan di Laragon:

```bash
docker compose up -d
```

### Full Docker Development (Opsional)

Gunakan `docker-compose.dev.yml` untuk menjalankan seluruh stack dalam container, termasuk aplikasi Laravel dengan PHP-FPM + Nginx + Queue Worker + Scheduler:

```bash
docker compose -f docker-compose.dev.yml up -d
```

Akses di **http://localhost:8080**

## Arsitektur Aplikasi

### Direktori Utama

```
app/
  Actions/            # Single-responsibility action classes
  Console/            # Console commands
  Events/             # Event classes
  Http/
    Controllers/      # Public & export controllers
    Middleware/        # Custom middleware
  Jobs/               # Queue jobs (batch selection, notifications, etc.)
  Livewire/           # 22 Livewire components
  Mail/               # Mail classes
  Models/             # 16 Eloquent models
  Notifications/      # Notification classes
  Observers/          # Model observers
  Providers/          # Service providers
  Services/           # Business logic services
database/
  migrations/         # 24 migration files
  seeders/            # RoleSeeder, BbkMadiunSeeder
routes/
  web.php             # Semua route aplikasi
  console.php         # Scheduled tasks
resources/
  views/              # Blade templates (layouts, components, Livewire views)
docker/               # Dockerfile, Nginx config, PHP ini, Supervisor config
```

### Livewire Components (22)

| Grup | Komponen |
|------|----------|
| **Applicant** (6) | `ApplicationForm`, `ApplicationStatus`, `DocumentRevision`, `BankAccountForm`, `SemesterRenewal`, `OtpVerification` |
| **Verifier** (2) | `VerificationQueue`, `ApplicationDetail` |
| **Admin** (11) | `AdminDashboard`, `ScholarshipManager`, `QualificationBuilder`, `TiebreakerConfigurator`, `VerifierAssignment`, `UserManager`, `BlacklistManager`, `SelectionResult`, `BatchSelectionRunner`, `NotificationConfigurator`, `AuditLogViewer` |
| **Approver** (2) | `ApproverDashboard`, `RecipientApproval` |
| **Treasurer** (1) | `DisbursementList` |

### Alur Seleksi

1. Admin membuat program beasiswa dengan kualifikasi dinamis
2. Pelamar mendaftar melalui wizard multi-langkah
3. Verifikator memeriksa dan menyetujui/menolak dokumen
4. Skor otomatis difinalisasi saat semua dokumen disetujui
5. Admin menjalankan seleksi batch → sistem memproses perpanjangan + pemeringkatan
6. Approver menyetujui daftar penerima akhir
7. Bendahara mengelola pencairan dana

## Testing

```bash
php artisan test
# atau
./vendor/bin/pest
```

## Lisensi

Hak cipta © 2024-2026 Pemerintah Kabupaten Madiun. Seluruh hak cipta dilindungi.
