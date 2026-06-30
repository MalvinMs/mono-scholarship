# Platform Beasiswa

Sistem manajemen beasiswa berbasis web untuk mengelola siklus penuh program beasiswa — dari pendaftaran, verifikasi dokumen, penilaian, seleksi, pencairan dana, hingga pengumuman. Dibangun dengan **Laravel 13 + Livewire 4 + FrankenPHP + Octane**.

---

## Daftar Isi

- [Fitur](#fitur)
- [Tech Stack](#tech-stack)
- [Arsitektur](#arsitektur)
- [Struktur Project](#struktur-project)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Cara Menjalankan Development](#cara-menjalankan-development)
- [Cara Menjalankan Staging](#cara-menjalankan-staging)
- [Cara Deployment Production](#cara-deployment-production)
- [Cara Rollback](#cara-rollback)
- [Akun Demo](#akun-demo)
- [Environment Variables](#environment-variables)
- [API Documentation](#api-documentation)
- [Optimasi & Cache](#optimasi--cache)
- [Backup](#backup)
- [Scaling](#scaling)
- [Hal yang Perlu Dikonfigurasi Manual](#hal-yang-perlu-dikonfigurasi-manual)

---

## Fitur

### Manajemen Program Beasiswa
- CRUD program beasiswa dengan status: `draft` → `open` → `renewal_open` → `renewal_closed` → `closed` → `selecting` → `announced`
- Pelacakan tahun akademik, jumlah dana, kuota utama & cadangan
- Program berbasis perpanjangan (renewal) dengan tautan ke program sebelumnya
- Pengubahan status otomatis via jadwal harian (`AutoManageScholarshipStatus` job)

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
- Konfigurasi tiebreaker multi-level

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

### REST API
- 89 endpoint REST API dengan Sanctum token authentication
- Cocok untuk frontend React/Vite SPA
- Dokumentasi lengkap: [API.md](./API.md)

### Keamanan
- Laravel Fortify: registrasi, login, reset password
- Otentikasi dua faktor (TOTP)
- Passkeys (WebAuthn) untuk login tanpa sandi
- Pembatasan kecepatan: 5 percobaan login/menit
- Bidang tersandi (encrypted): NIK, nomor rekening
- Verifikasi email/telepon via OTP
- Manajemen sesi via Redis

---

## Role Pengguna

| Role | Deskripsi |
|------|-----------|
| `super-admin` | Super Administrator — akses penuh sistem |
| `admin` | Administrator Program — mengelola beasiswa, kualifikasi, pengguna, seleksi |
| `verifier` | Verifikator — memverifikasi aplikasi dan dokumen |
| `approver` | Approver / Kepala — menyetujui daftar penerima akhir |
| `treasurer` | Bendahara — mengelola pencairan dana |
| `applicant` | Pendaftar — mendaftar, unggah dokumen, perpanjangan |

---

## Tech Stack

### Backend
| Teknologi | Versi | Fungsi |
|-----------|-------|--------|
| **PHP** | ^8.4 | Runtime |
| **Laravel** | ^13.8 | Framework |
| **Livewire** | ^4.3 | Reactive UI (tanpa JS framework) |
| **Laravel Fortify** | ^1.37 | Authentication backend |
| **Laravel Octane** | ^2.17 | High-performance app server (FrankenPHP driver) |
| **FrankenPHP** | latest | PHP app server + Caddy web server |
| **Spatie Laravel Permission** | ^8.0 | RBAC (6 roles) |
| **Laravel Sanctum** | ^4.3 | API token authentication |
| **Predis** | ^3.5 | Redis client |
| **Laravel DomPDF** | ^3.1 | PDF reports |
| **Pest** | ^4.7 | Testing framework |

### Database & Storage
| Teknologi | Fungsi |
|-----------|--------|
| **PostgreSQL 16** | Database utama |
| **Redis 7** | Cache (DB1), Queue (DB2), Session (DB3) |
| **MinIO** | S3-compatible object storage (dokumen, file upload) |

### Frontend
| Teknologi | Keterangan |
|-----------|------------|
| **Tailwind CSS** | v4 (via Vite plugin) |
| **Vite** | ^8.0 |
| **ApexCharts** | ^5.15 (dashboard charts) |
| **Blade Lucide Icons** | ^1.26 |
| **Blade + Livewire** | No JS framework |

### DevOps & Infrastructure
| Tools | Fungsi |
|-------|--------|
| **Docker Compose** | Multi-environment container orchestration |
| **Caddy** | Auto HTTPS, HTTP/2/3, reverse proxy |
| **Supervisor** | Queue worker management |
| **Mailpit** | SMTP testing (dev/staging only) |

---

## Arsitektur

```
Internet
    │
    ▼
 FrankenPHP (Caddy) — HTTPS, gzip, security headers
    │
 Laravel Octane — worker mode (app tetap di memory)
    │
 Laravel 13 — Livewire v4 + API Controllers
    │
────────────────────────────────────────────────
│           │           │            │          │
▼           ▼           ▼            ▼          ▼
PostgreSQL  Redis(DB0)  Redis(DB1)   Redis(DB2) MinIO
                        Cache        Queue
                                     Redis(DB3)
                                     Session
```

### Perbedaan Environment

| Aspek | Development | Staging | Production |
|-------|-------------|---------|------------|
| **APP_ENV** | `local` | `staging` | `production` |
| **APP_DEBUG** | true | false | false |
| **LOG_LEVEL** | debug | debug | warning |
| **Mail** | Mailpit | Mailpit | SMTP eksternal |
| **Octane Workers** | 1 | 4 | auto (CPU cores) |
| **Queue Workers** | 1 | 2 | 4 |
| **DB Database** | `mono_scholarship` | `mono_scholarship_staging` | `mono_scholarship_production` |
| **MinIO Bucket** | `development-files` | `staging-files` | `production-files` |
| **APP_URL** | `http://localhost:8000` | `https://staging.domain.com` | `https://domain.com` |

---

## Struktur Project

```
mono-scholarship/
├── app/
│   ├── Actions/              # Single-responsibility action classes (14 files)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/          # 25 REST API controllers
│   │   │   ├── Export/       # Export controllers (PDF, CSV)
│   │   │   └── Public/       # Web controllers
│   │   ├── Middleware/       # Custom middleware (EnsureNotBlacklisted)
│   │   └── Responses/        # Fortify login/register responses
│   ├── Jobs/                 # Queue jobs (ProcessBatchScoring, SendNotification, etc.)
│   ├── Livewire/             # 22 Livewire components
│   ├── Models/               # 16 Eloquent models
│   ├── Observers/            # Model observers
│   ├── Providers/            # Service providers
│   └── Services/             # Business logic (ScoringEngine, OtpService, etc.)
├── bootstrap/
│   └── app.php               # Laravel bootstrap config
├── config/                   # 20+ config files
├── database/
│   ├── migrations/           # 25 migration files
│   └── seeders/              # RoleSeeder, BbkMadiunSeeder
├── docker/
│   ├── php/
│   │   ├── dev.Dockerfile    # FrankenPHP dev image
│   │   └── prod.Dockerfile   # FrankenPHP production image
│   ├── frankenphp/
│   │   └── Caddyfile         # Caddy web server config
│   └── supervisor/
│       └── queue-worker.conf # Queue worker supervisor config
├── routes/
│   ├── api.php               # 89 REST API routes
│   ├── web.php               # Web routes (Livewire)
│   └── console.php           # Scheduled tasks
├── docker-compose.yml              # Base (Redis + MinIO)
├── docker-compose.override.yml     # Development (Postgres + Mailpit + App)
├── docker-compose.staging.yml      # Staging
├── docker-compose.production.yml   # Production
├── deploy.sh                 # Zero-downtime deployment script
├── rollback.sh               # Rollback script
├── API.md                    # Dokumentasi REST API lengkap
└── README.md                 # Dokumentasi ini
```

---

## Persyaratan Sistem

| Tools | Development | Staging / Production |
|-------|-------------|---------------------|
| PHP | ^8.4 | ^8.4 (dalam Docker) |
| Composer | ✅ | ✅ (dalam Docker) |
| Node.js + npm | ✅ | ✅ (dalam Docker) |
| Docker Desktop | ✅ | ✅ |
| PostgreSQL | Otomatis via Docker | Otomatis via Docker |
| Redis | Otomatis via Docker | Otomatis via Docker |

---

## Cara Menjalankan Development

### 1. Persiapan

```bash
# Clone
git clone <repository-url>
cd mono-scholarship

# Copy .env
cp .env.example .env
php artisan key:generate
```

### 2. Jalankan Full Stack (Docker)

```bash
# Membangun dan menjalankan semua service
docker compose up -d --build
```

Ini akan menjalankan:

| Service | Container | Port | Keterangan |
|---------|-----------|------|------------|
| **App** | `beasiswa-app` | 8000 | FrankenPHP + Octane (hot-reload) |
| **PostgreSQL** | `beasiswa-postgres` | 5432 | Database |
| **Redis** | `beasiswa-redis` | 6379 | Cache, Queue, Session |
| **MinIO** | `beasiswa-minio` | 9000/9001 | File storage + console |
| **Mailpit** | `beasiswa-mailpit` | 8025/1025 | Email testing UI + SMTP |
| **Queue** | `beasiswa-queue` | — | Redis queue worker |
| **Scheduler** | `beasiswa-scheduler` | — | Laravel scheduler |

### 3. Setup Database

```bash
# Masuk ke container app
docker compose exec app bash

# Di dalam container:
php artisan migrate
php artisan db:seed --class=RoleSeeder
php artisan storage:link
npm run build
exit
```

> **Catatan:** Atau jalankan `composer setup` setelah service berjalan.

### 4. Akses

| Layanan | URL |
|---------|-----|
| **Aplikasi** | http://localhost:8000 |
| **Mailpit UI** | http://localhost:8025 |
| **MinIO Console** | http://localhost:9001 (admin/minioadmin) |
| **PostgreSQL** | localhost:5432 |

### 5. Cek Octane

```bash
docker compose exec app php artisan octane:status
# Output: Octane server is running.
```

### Development Tanpa Docker (Laragon)

Jika menggunakan Laragon, jalankan hanya services pendukung via Docker:

```bash
docker compose up -d redis minio mailpit
```

Lalu jalankan aplikasi via Laragon (PHP built-in server + Vite):

```bash
composer dev
```

---

## Cara Menjalankan Staging

Staging adalah mirror production dengan perbedaan minimal (Mailpit aktif, debug logging).

```bash
# Build dan jalankan staging stack
docker compose -f docker-compose.yml -f docker-compose.staging.yml up -d --build

# Setup database
docker compose -f docker-compose.yml -f docker-compose.staging.yml exec app php artisan migrate --force

# Seed jika pertama kali
docker compose -f docker-compose.yml -f docker-compose.staging.yml exec app php artisan db:seed --class=RoleSeeder --force
```

Staging aktif di **https://staging.domain.com** (port 80/443).

---

## Cara Deployment Production

### Opsi 1: Docker Compose

```bash
# 1. Siapkan .env.production
cp .env.example .env.production
# Edit .env.production dengan credential production

# 2. Build dan deploy
docker compose -f docker-compose.yml -f docker-compose.production.yml up -d --build

# 3. Migrate
docker compose -f docker-compose.yml -f docker-compose.production.yml exec app php artisan migrate --force
```

### Opsi 2: Deploy Script (Bare Metal / VPS)

```bash
# 1. Prepare server
mkdir -p /var/www/beasiswa/{shared,releases}
mkdir -p /var/www/beasiswa/shared/storage/{app,framework/{cache,sessions,views,testing},logs}
ln -sf /var/www/beasiswa/shared/storage /var/www/beasiswa/shared/storage-link

# 2. Deploy
./deploy.sh /var/www/beasiswa main
```

**Yang dilakukan oleh `deploy.sh`:**
1. Checkout release baru ke `releases/YYYYMMDD_HHMMSS/`
2. Link `.env` dan `storage/` dari folder `shared/`
3. `composer install --no-dev --optimize-autoloader`
4. `npm ci && npm run build`
5. `php artisan migrate --force`
6. `php artisan config:cache`, `route:cache`, `event:cache`, `view:cache`, `optimize`
7. Atomic symlink switch: `current -> releases/<new-release>`
8. `php artisan octane:reload` (zero-downtime, tanpa restart container)
9. `php artisan queue:restart`
10. Hapus release lama (keep 5 terakhir)

---

## Cara Rollback

```bash
./rollback.sh /var/www/beasiswa
```

Rollback memindahkan symlink `current` ke release sebelumnya, lalu menjalankan `octane:reload` dan `queue:restart`.

---

## Akun Demo

Setelah menjalankan `RoleSeeder` dan `BbkMadiunSeeder`:

| Role | Email | Password |
|------|-------|----------|
| Super Admin | `superadmin@beasiswa.test` | `password` |
| Admin | `admin@beasiswa.test` | `password` |
| Verifier | `verifier1@beasiswa.test`, `verifier2@beasiswa.test` | `password` |
| Approver | `approver@beasiswa.test` | `password` |
| Treasurer | `treasurer@beasiswa.test` | `password` |
| Applicant | `applicant1` s.d. `applicant10@beasiswa.test` | `password` |

---

## Environment Variables

### Variabel Utama

```env
# App
APP_NAME="Platform Beasiswa"
APP_ENV=local|staging|production
APP_DEBUG=true|false
APP_KEY=<base64-key>
APP_URL=http://localhost:8000

# Database (PostgreSQL)
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=mono_scholarship
DB_USERNAME=postgres
DB_PASSWORD=secret

# Redis (dipisah per fungsi — lihat catatan di bawah)
REDIS_CLIENT=predis
REDIS_HOST=redis
REDIS_PORT=6379
REDIS_DB=0          # Default (cadangan)
REDIS_CACHE_DB=1    # Cache
REDIS_QUEUE_DB=2    # Queue
REDIS_SESSION_DB=3  # Session

# Octane
OCTANE_SERVER=frankenphp
OCTANE_WORKERS=auto
OCTANE_MAX_REQUESTS=500

# MinIO
FILESYSTEM_DISK=minio
MINIO_ENDPOINT=http://minio:9000
MINIO_ACCESS_KEY=minioadmin
MINIO_SECRET_KEY=minioadmin
MINIO_BUCKET=development-files

# SMTP
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_FROM_ADDRESS=noreply@beasiswa.test

# Fonnte (WhatsApp)
FONNTE_TOKEN=
```

### Catatan Redis
- Redis menggunakan **4 database terpisah** untuk menghindari key collision
- `REDIS_DB=0` — default, untuk data sementara
- `REDIS_CACHE_DB=1` — cache data (Laravel Cache facade)
- `REDIS_QUEUE_DB=2` — queue jobs
- `REDIS_SESSION_DB=3` — session data
- Konfigurasi ini sudah di-set di `config/database.php`, `config/cache.php`, `config/queue.php`, dan `config/session.php`
- Setiap environment (dev/staging/production) memiliki Redis instance sendiri → database ID aman digunakan ulang

---

## API Documentation

Dokumentasi REST API lengkap (89 endpoint) tersedia di **[API.md](./API.md)**.

Isinya meliputi:
- 7 tabel ringkasan endpoint per role
- Authentication guide (register, login, token, OTP)
- Format response dan struktur data
- Pagination, filtering, searching
- Upload file specifications
- Authorization matrix
- HTTP error codes
- Business flow diagrams
- Contoh integrasi Axios
- Postman collection (JSON)
- OpenAPI 3.0 spec (YAML)
- Catatan backend

---

## Optimasi & Cache

### Production Wajib Menjalankan

```bash
php artisan config:cache
php artisan route:cache
php artisan event:cache
php artisan view:cache
php artisan optimize
```

Semua ini sudah otomatis di `docker/php/prod.Dockerfile` dan `deploy.sh`.

### Octane Worker Mode

- App di-boot sekali, request berikutnya lebih cepat (tidak boot ulang PHP)
- Worker count bisa diatur via `OCTANE_WORKERS` env var
- Default: `auto` (sama dengan jumlah CPU cores)
- `OCTANE_MAX_REQUESTS`: reload worker setelah N request (cegah memory leak)
- Graceful reload: `php artisan octane:reload` — tanpa downtime

### Queue Workers

- Konfigurasi via Supervisor di `docker/supervisor/queue-worker.conf`
- 3 queue: `default`, `scoring`, `notifications`
- Worker count bisa diatur via `QUEUE_WORKERS` env var
- Restart: `php artisan queue:restart`

### Scheduler

- Berjalan via service `scheduler` di Docker Compose
- Perintah: `php artisan schedule:work`
- Task harian: `AutoManageScholarshipStatus` (00:01), Livewire temp cleanup

---

## Backup

### PostgreSQL

```bash
# Backup
docker compose exec postgres pg_dump -U postgres mono_scholarship_production > backup_$(date +%Y%m%d).sql

# Restore
cat backup.sql | docker compose exec -T postgres psql -U postgres mono_scholarship_production
```

### MinIO

Gunakan `mc` (MinIO Client):

```bash
# Backup bucket ke local
docker compose exec minio mc mirror --overwrite local/production-files /backup/minio/

# Restore
docker compose exec minio mc mirror --overwrite /backup/minio/ local/production-files
```

---

## Scaling

| Komponen | Cara Scale | Catatan |
|----------|-----------|---------|
| **Octane Workers** | Naikkan `OCTANE_WORKERS` | Setara CPU cores, jangan oversubscribe |
| **Queue Workers** | Naikkan `QUEUE_WORKERS` | Pisahkan queue untuk job berat (scoring) |
| **Redis** | Cluster / Sentinel | Untuk HA Redis |
| **PostgreSQL** | Read replica + connection pooling | pgBouncer untuk pooling |
| **MinIO** | Distributed mode | Untuk production berskala besar |

---

## Docker Compose Reference

| File | Isi | Perintah |
|------|-----|----------|
| `docker-compose.yml` | Base — Redis, MinIO | Wajib di semua environment |
| `docker-compose.override.yml` | Dev — Postgres, Mailpit, App (hot-reload) | Auto-merge saat `docker compose up -d` |
| `docker-compose.staging.yml` | Staging — semua service, debug logging | `-f docker-compose.staging.yml` |
| `docker-compose.production.yml` | Production — external SMTP, optimasi | `-f docker-compose.production.yml` |

---

## Hal yang Perlu Dikonfigurasi Manual

- [ ] **DNS** — domain mengarah ke IP server
- [ ] **SSL** — otomatis oleh Caddy (Let's Encrypt), pastikan domain sudah pointing
- [ ] **Caddyfile production** — update domain di `docker/frankenphp/Caddyfile`
- [ ] **SMTP** — set `MAIL_HOST`, `MAIL_USERNAME`, `MAIL_PASSWORD` di `.env.production`
- [ ] **MinIO** — ganti credential default, atur bucket `production-files`
- [ ] **Cloudflare** — proxy + DNS (opsional)
- [ ] **Monitoring** — instal Laravel Pulse atau Sentry
- [ ] **Backup** — setup cron untuk PostgreSQL + MinIO backup
- [ ] **.env.production** — buat dan isi credential (jangan di-commit)
- [ ] **MinIO storage** — atur retensi file dan lifecycle policy

---

## Catatan Teknis

| # | Catatan |
|---|---------|
| 1 | **Format tanggal**: `Y-m-d H:i:s` (WIB) |
| 2 | **File storage**: MinIO (S3-compatible, localhost:9000) |
| 3 | **File download**: Temporary signed URL (berlaku 60 menit) |
| 4 | **NIK & nomor rekening**: Dienkripsi di database (Laravel encryption-at-rest) |
| 5 | **Upload**: Max 2 MB, format JPG/PNG/PDF |
| 6 | **Soft Delete**: Scholarships, Users |
| 7 | **Verification Logs**: Immutable (tidak bisa dihapus) |
| 8 | **Application Scores**: Immutable setelah difinalisasi |
| 9 | **Batch scoring**: Async — dispatch job, polling progress via cache |
| 10 | **ID**: Auto-increment integer (bukan UUID) |
| 11 | **Octane aman**: Sudah diaudit — tidak ada static state, service class final, Liveware compatible |
| 12 | **CORS**: Allow dari `FRONTEND_URL` env var |

---

## Lisensi

Hak cipta © 2024-2026 Pemerintah Kabupaten Madiun. Seluruh hak cipta dilindungi.
