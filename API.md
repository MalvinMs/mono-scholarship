# API Documentation — Platform Beasiswa

**Base URL:** `http://localhost:8000/api`  
**Auth:** Laravel Sanctum (Bearer Token)  
**Format:** JSON  
**Bahasa:** Indonesia  

---

## 1. Ringkasan Endpoint

### Public (No Auth)

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| POST | `/auth/register` | Registrasi akun baru |
| POST | `/auth/login` | Login dan dapatkan token |
| POST | `/auth/forgot-password` | Kirim link reset password |
| POST | `/auth/reset-password` | Reset password |
| GET | `/scholarships` | Daftar program beasiswa |
| GET | `/scholarships/{scholarship}` | Detail program beasiswa |
| GET | `/scholarships/{scholarship}/form-config` | Konfigurasi formulir pendaftaran |
| GET | `/announcements` | Daftar pengumuman |
| GET | `/announcements/{scholarship}` | Detail hasil pengumuman |
| POST | `/announcements/{scholarship}/check` | Cek nomor registrasi |

### Authenticated (Semua Role)

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| POST | `/auth/logout` | Hapus token saat ini |
| GET | `/auth/user` | Info user saat ini |
| GET | `/auth/tokens` | Daftar token aktif |
| DELETE | `/auth/tokens/{tokenId}` | Revoke token tertentu |
| POST | `/auth/otp/send` | Kirim kode OTP |
| POST | `/auth/otp/verify` | Verifikasi kode OTP |
| GET | `/profile` | Lihat profil |
| PUT | `/profile` | Update profil |
| PUT | `/profile/password` | Ubah password |

### Applicant (`role:applicant`)

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/applicant/dashboard` | Dashboard pelamar |
| GET | `/applicant/applications` | Daftar pendaftaran saya |
| POST | `/applicant/applications` | Buat pendaftaran baru |
| GET | `/applicant/applications/{application}` | Detail pendaftaran |
| POST | `/applicant/applications/{application}/files` | Upload file kualifikasi |
| DELETE | `/applicant/applications/{application}/files/{qualification}` | Hapus file |
| GET | `/applicant/applications/{application}/documents/{document}/download` | Download file |
| PUT | `/applicant/applications/{application}/bank` | Update rekening bank |
| POST | `/applicant/applications/{application}/renewal` | Perpanjangan semester |

### Verifier (`role:verifier`)

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/verifier/applications` | Antrean verifikasi |
| GET | `/verifier/applications/{application}` | Detail aplikasi |
| POST | `/verifier/applications/{application}/documents/{document}/approve` | Setujui dokumen |
| POST | `/verifier/applications/{application}/documents/{document}/reject` | Tolak dokumen |
| POST | `/verifier/applications/{application}/answers/{answer}/approve` | Setujui jawaban |
| POST | `/verifier/applications/{application}/answers/{answer}/correct` | Koreksi jawaban |
| POST | `/verifier/applications/{application}/finalize` | Finalisasi skor |
| POST | `/verifier/applications/{application}/blacklist` | Blacklist pelamar |

### Admin (`role:admin|super-admin`)

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/admin/dashboard` | Dashboard admin |
| GET | `/admin/scholarships` | Daftar program beasiswa |
| POST | `/admin/scholarships` | Buat program baru |
| GET | `/admin/scholarships/{scholarship}` | Detail program |
| PUT | `/admin/scholarships/{scholarship}` | Update program |
| DELETE | `/admin/scholarships/{scholarship}` | Hapus program |
| GET | `/admin/scholarships/{scholarship}/qualification-groups` | Grup kualifikasi |
| POST | `/admin/scholarships/{scholarship}/qualification-groups` | Buat grup |
| PUT | `/admin/scholarships/{scholarship}/qualification-groups/{group}` | Update grup |
| DELETE | `/admin/scholarships/{scholarship}/qualification-groups/{group}` | Hapus grup |
| GET | `/admin/scholarships/{scholarship}/qualifications` | Daftar kualifikasi |
| POST | `/admin/scholarships/{scholarship}/qualifications` | Buat kualifikasi |
| PUT | `/admin/qualifications/{qualification}` | Update kualifikasi |
| DELETE | `/admin/qualifications/{qualification}` | Hapus kualifikasi |
| POST | `/admin/qualifications/{qualification}/options` | Buat opsi pilihan |
| PUT | `/admin/qualifications/{qualification}/options/{option}` | Update opsi |
| DELETE | `/admin/qualifications/{qualification}/options/{option}` | Hapus opsi |
| POST | `/admin/qualifications/{qualification}/ranges` | Buat rentang nilai |
| PUT | `/admin/qualifications/{qualification}/ranges/{range}` | Update rentang |
| DELETE | `/admin/qualifications/{qualification}/ranges/{range}` | Hapus rentang |
| GET | `/admin/scholarships/{scholarship}/verifiers` | Daftar verifikator |
| POST | `/admin/scholarships/{scholarship}/verifiers` | Tambah verifikator |
| DELETE | `/admin/scholarships/{scholarship}/verifiers/{user}` | Hapus verifikator |
| GET | `/admin/scholarships/{scholarship}/tiebreaker` | Konfigurasi tiebreaker |
| PUT | `/admin/scholarships/{scholarship}/tiebreaker` | Update tiebreaker |
| GET | `/admin/users` | Daftar pengguna |
| POST | `/admin/users` | Buat pengguna |
| GET | `/admin/users/{user}` | Detail pengguna |
| PUT | `/admin/users/{user}` | Update pengguna |
| DELETE | `/admin/users/{user}` | Hapus pengguna |
| GET | `/admin/blacklist` | Daftar blacklist |
| POST | `/admin/blacklist/{blacklist_log}/revoke` | Cabut blacklist |
| GET | `/admin/scholarships/{scholarship}/renewal-summary` | Ringkasan renewal |
| POST | `/admin/scholarships/{scholarship}/batch-scoring` | Jalankan batch scoring |
| GET | `/admin/scholarships/{scholarship}/batch-scoring/progress` | Progress scoring |
| GET | `/admin/scholarships/{scholarship}/selection-results` | Hasil seleksi |
| GET | `/admin/scholarships/{scholarship}/selection-results/{score}` | Detail hasil |
| GET | `/admin/scholarships/{scholarship}/notifications` | Konfigurasi notif |
| PUT | `/admin/scholarships/{scholarship}/notifications` | Update notifikasi |
| POST | `/admin/scholarships/{scholarship}/notifications/test` | Test notifikasi |
| GET | `/admin/audit-logs/verification` | Log verifikasi |
| GET | `/admin/audit-logs/blacklist` | Log blacklist |
| GET | `/admin/scholarships/{scholarship}/export/applicants` | Export CSV pelamar |
| GET | `/admin/scholarships/{scholarship}/export/pdf/recipients` | PDF penerima |
| GET | `/admin/scholarships/{scholarship}/export/pdf/audit-log` | PDF audit log |

### Approver (`role:approver`)

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/approver/dashboard` | Dashboard approver |
| GET | `/approver/scholarships/{scholarship}/candidates` | Kandidat penerima |
| POST | `/approver/scholarships/{scholarship}/approve` | Tetapkan penerima |

### Treasurer (`role:treasurer`)

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/treasurer/disbursements` | Daftar pencairan |
| GET | `/treasurer/disbursements/{disbursement}` | Detail pencairan |
| PUT | `/treasurer/disbursements/{disbursement}` | Update status pencairan |
| GET | `/treasurer/scholarships/{scholarship}/export` | Export Excel pencairan |
| GET | `/treasurer/scholarships/{scholarship}/export/pdf` | PDF pencairan |

---

## 2. Authentication Guide

### Flow Auth

```
Registrasi -> Login -> Dapat Token -> Simpan Token -> Kirim Bearer -> Logout
```

### Register

Mendaftarkan akun baru (role `applicant`) dan langsung mengirim OTP ke email.

```http
POST /api/auth/register
Content-Type: application/json

{
    "name": "Budi Santoso",
    "nik": "3501010101990001",
    "email": "budi@example.com",
    "phone": "081234567890",
    "password": "password123",
    "password_confirmation": "password123",
    "device_name": "react-spa"
}
```

**Response (201):**
```json
{
    "data": {
        "user": {
            "id": 1,
            "name": "Budi Santoso",
            "email": "budi@example.com",
            "phone": "081234567890",
            "nik": "3501010101990001",
            "roles": ["applicant"],
            "is_active": true,
            "is_blacklisted": false
        },
        "token": "1|abc123def456..."
    },
    "message": "Registrasi berhasil. OTP telah dikirim ke email Anda."
}
```

### Login

```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "superadmin@beasiswa.test",
    "password": "password",
    "device_name": "react-spa"
}
```

**Response (200):**
```json
{
    "data": {
        "user": {
            "id": 1,
            "name": "Super Admin",
            "email": "superadmin@beasiswa.test",
            "roles": ["super-admin", "admin"]
        },
        "token": "1|abc123def456..."
    },
    "message": "Login berhasil."
}
```

### Menggunakan Token

Setiap request yang membutuhkan auth wajib menyertakan header:

```http
Authorization: Bearer 1|abc123def456...
Accept: application/json
```

### Logout

```http
POST /api/auth/logout
Authorization: Bearer 1|abc123def456...
```

**Response (200):**
```json
{
    "data": null,
    "message": "Logout berhasil."
}
```

### Manajemen Token

**Lihat daftar token:**
```http
GET /api/auth/tokens
Authorization: Bearer 1|abc123def456...
```

**Hapus token tertentu:**
```http
DELETE /api/auth/tokens/2
Authorization: Bearer 1|abc123def456...
```

> **Catatan:** Tidak ada refresh token. Token Sanctum berlaku hingga di-revoke manual. Buat token baru dengan login ulang jika token lama hilang.

---

## 3. Format Response

### Success (Single)
```json
{
    "data": { ... },
    "message": "Operasi berhasil."
}
```

### Success (List / Paginated)
```json
{
    "data": [ ... ],
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 72
    }
}
```

### Error Validation (422)
```json
{
    "message": "Validation failed",
    "errors": {
        "email": ["Email sudah terdaftar."],
        "password": ["Password minimal 8 karakter."]
    }
}
```

### Error Umum
```json
{
    "message": "Email atau password salah."
}
```

| Properti | Tipe | Deskripsi |
|----------|------|-----------|
| `data` | object/array/null | Payload utama response |
| `message` | string | Pesan deskriptif |
| `errors` | object | Hanya ada saat validasi gagal |
| `meta` | object | Informasi pagination |

---

## 4. Struktur Data

### User
| Field | Type | Nullable | Deskripsi |
|-------|------|----------|-----------|
| id | integer | No | ID user |
| name | string | No | Nama lengkap |
| email | string | No | Email |
| phone | string | Yes | Nomor telepon |
| nik | string | Yes | NIK (terenkripsi di database) |
| birth_date | string (Y-m-d) | Yes | Tanggal lahir |
| birth_place | string | Yes | Tempat lahir |
| address | string | Yes | Alamat |
| village | string | Yes | Desa/kelurahan |
| district | string | Yes | Kecamatan |
| city | string | Yes | Kota/kabupaten |
| province | string | Yes | Provinsi |
| education_level | string | Yes | SMA/SMK/MA/D3/D4/S1/S2 |
| school_name | string | Yes | Nama sekolah/universitas |
| major | string | Yes | Jurusan |
| current_semester | integer | Yes | Semester saat ini |
| is_active | boolean | No | Status aktif |
| is_blacklisted | boolean | No | Status blacklist |
| roles | array | No | Daftar role |
| email_verified_at | string | Yes | Waktu verifikasi email |
| phone_verified_at | string | Yes | Waktu verifikasi telepon |

### Scholarship
| Field | Type | Nullable | Deskripsi |
|-------|------|----------|-----------|
| id | integer | No | ID program |
| name | string | No | Nama program |
| slug | string | No | Slug untuk URL |
| description | text | Yes | Deskripsi |
| academic_year | string | Yes | Tahun akademik |
| fund_amount | integer | Yes | Jumlah dana per mahasiswa |
| quota_primary | integer | No | Kuota utama |
| quota_reserve | integer | No | Kuota cadangan |
| date_start | string (Y-m-d) | Yes | Tanggal mulai pendaftaran |
| date_end | string (Y-m-d) | Yes | Tanggal tutup |
| status | string | No | draft/open/closed/renewal_open/renewal_closed/selecting/announced |

### Application
| Field | Type | Nullable | Deskripsi |
|-------|------|----------|-----------|
| id | integer | No | ID aplikasi |
| registration_number | string | No | Nomor registrasi |
| status | string | No | draft/submitted/under_review/needs_revision/verified/selected/rejected |
| is_renewal | boolean | No | Apakah perpanjangan |
| submitted_at | string | Yes | Waktu submit |
| selection_result | string | Yes | utama/cadangan/tidak_lolos |
| rank | integer | Yes | Peringkat |
| total_score | integer | Yes | Skor total |

### Disbursement
| Field | Type | Nullable | Deskripsi |
|-------|------|----------|-----------|
| id | integer | No | ID pencairan |
| scholarship | string | No | Nama program |
| applicant | string | No | Nama penerima |
| registration_number | string | No | Nomor registrasi |
| bank_name | string | Yes | Nama bank |
| account_holder_name | string | Yes | Nama pemilik rekening |
| amount | integer | Yes | Jumlah dana |
| status | string | No | waiting/processing/disbursed/failed |
| disbursed_at | string | Yes | Waktu pencairan |

---

## 5. Pagination

Semua endpoint yang mengembalikan list data mendukung parameter berikut:

| Parameter | Type | Default | Keterangan |
|-----------|------|---------|------------|
| `page` | integer | 1 | Halaman |
| `per_page` | integer | 15 | Data per halaman (max 100) |

**Response:**
```json
{
    "data": [ ... ],
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 72
    }
}
```

---

## 6. Filtering & Searching

Format query parameter untuk filtering:

```
?filter[field]=value&search=keyword&sort=-created_at
```

| Parameter | Contoh | Deskripsi |
|-----------|--------|-----------|
| `filter[status]` | `?filter[status]=open` | Filter berdasarkan status |
| `filter[scholarship_id]` | `?filter[scholarship_id]=1` | Filter per program |
| `filter[selection_result]` | `?filter[selection_result]=utama` | Filter hasil seleksi |
| `filter[is_active]` | `?filter[is_active]=true` | Filter status aktif |
| `filter[role]` | `?filter[role]=verifier` | Filter role user |
| `search` | `?search=budi` | Pencarian teks |
| `sort` | `?sort=-created_at` | Urutkan (prefix `-` = DESC) |

**Contoh:**
```
GET /api/admin/users?filter[role]=verifier&search=budi&per_page=50
GET /api/admin/scholarships?filter[status]=open&sort=-created_at
GET /api/admin/blacklist?filter[is_active]=true
```

---

## 7. Upload File

### Endpoint Upload
```
POST /api/applicant/applications/{application}/files
```

### Spesifikasi File

| Aturan | Nilai |
|--------|-------|
| Ukuran maksimum | 2 MB |
| Format yang diizinkan | JPG, JPEG, PNG, PDF |
| Content-Type | image/jpeg, image/png, application/pdf |

### Contoh Request (multipart/form-data)

```
POST /api/applicant/applications/5/files
Authorization: Bearer 1|abc123def456...
Content-Type: multipart/form-data

qualification_id: 12
file: [file.pdf]
```

### Response (201)
```json
{
    "data": {
        "id": 25,
        "file_name": "transkrip_nilai.pdf",
        "verification_status": "pending"
    },
    "message": "File berhasil diunggah."
}
```

### Catatan
- File disimpan di **MinIO** (S3-compatible)
- File diganti jika upload ulang untuk kualifikasi yang sama
- Status verifikasi awal selalu `pending`

---

## 8. Validasi

### Register
| Field | Aturan |
|-------|--------|
| name | required, string, max:255 |
| nik | required, string, max:20, unique:users |
| email | required, email, max:255, unique:users |
| phone | required, string, max:20 |
| password | required, string, min:8, confirmed |
| password_confirmation | required (dengan confirmed) |
| device_name | nullable, string |

### Profile Update
| Field | Aturan |
|-------|--------|
| name | sometimes, string, max:255 |
| email | sometimes, email, unique:users (ignore current) |
| phone | nullable, string, max:20 |
| education_level | nullable, in:SMA,SMK,MA,PAKET_C,D3,D4,S1,S2 |
| current_semester | nullable, integer, min:1, max:14 |

### Password Change
| Field | Aturan |
|-------|--------|
| current_password | required, string |
| password | required, string, min:8, confirmed |

### Create User (Admin)
| Field | Aturan |
|-------|--------|
| name | required, string, max:255 |
| email | required, email, unique:users |
| password | required, string, min:8, confirmed |
| role | required, in:applicant,verifier,admin,approver,treasurer |

---

## 9. Authorization

| Role | Akses |
|------|-------|
| **Guest** | Register, login, forgot password, lihat scholarships, lihat announcements |
| **Applicant** | Semua auth di atas + dashboard, aplikasi, file, bank, renewal |
| **Verifier** | Semua auth + verifikasi aplikasi, dokumen, jawaban, blacklist |
| **Admin** | Semua auth + CRUD scholarships, kualifikasi, users, blacklist, batch scoring, notifikasi, audit log, export |
| **Super Admin** | Sama dengan admin |
| **Approver** | Semua auth + dashboard approver, lihat kandidat, tetapkan penerima |
| **Treasurer** | Semua auth + daftar pencairan, update status, export |

---

## 10. Error Code

| HTTP Code | Arti | Kapan Terjadi |
|-----------|------|---------------|
| 200 | OK | Request berhasil |
| 201 | Created | Resource berhasil dibuat |
| 202 | Accepted | Request diterima (batch scoring) |
| 400 | Bad Request | Input tidak valid |
| 401 | Unauthorized | Token tidak ada atau invalid |
| 403 | Forbidden | Tidak punya akses / blacklisted |
| 404 | Not Found | Resource tidak ditemukan |
| 422 | Unprocessable Entity | Validasi gagal |
| 429 | Too Many Requests | Rate limit tercapai |
| 500 | Internal Server Error | Error server |

---

## 11. Business Flow

### Alur Beasiswa
```
Admin buat program
       |
Program open -> Pelamar daftar (multi-step form)
       |
Verifikator verifikasi dokumen & jawaban
       |
Auto-finalisasi skor (saat semua dokumen disetujui)
       |
Admin jalankan batch scoring
       |
Sistem proses renewal -> ranking -> tiebreaker
       |
Approver tetapkan penerima
       |
Pengumuman -> Notifikasi ke pelamar
       |
Treasurer kelola pencairan dana
```

### Alur Aplikasi (Applicant)
```
Lihat program tersedia
       |
Klik daftar -> Isi form multi-step
       |
Upload dokumen -> Submit
       |
Pantau status di dashboard
       |
[Jika perlu revisi] Upload ulang dokumen yang ditolak
       |
Lihat hasil seleksi di pengumuman
```

---

## 12. Contoh Integrasi Frontend (Axios)

### Setup Axios
```javascript
import axios from 'axios';

const api = axios.create({
    baseURL: 'http://localhost:8000/api',
    headers: { 'Accept': 'application/json' },
});

api.interceptors.request.use((config) => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            localStorage.removeItem('token');
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

export default api;
```

### Auth
```javascript
async function login(email, password) {
    const { data } = await api.post('/auth/login', {
        email, password, device_name: 'react-spa',
    });
    localStorage.setItem('token', data.data.token);
    return data.data.user;
}

async function register(form) {
    const { data } = await api.post('/auth/register', form);
    localStorage.setItem('token', data.data.token);
    return data.data.user;
}

async function logout() {
    await api.post('/auth/logout');
    localStorage.removeItem('token');
}

async function getUser() {
    const { data } = await api.get('/auth/user');
    return data.data;
}
```

### Scholarships
```javascript
async function getScholarships(page = 1) {
    const { data } = await api.get('/scholarships', { params: { page } });
    return data;
}

async function getFormConfig(slug) {
    const { data } = await api.get(`/scholarships/${slug}/form-config`);
    return data.data;
}
```

### Submit Application
```javascript
async function submitApplication(scholarshipSlug, answers, files, isDraft = false) {
    const formData = new FormData();
    formData.append('scholarship_slug', scholarshipSlug);
    formData.append('is_draft', isDraft);

    Object.entries(answers).forEach(([key, value]) => {
        formData.append(`answers[${key}]`, value);
    });
    if (files) {
        Object.entries(files).forEach(([key, file]) => {
            formData.append(`files[${key}]`, file);
        });
    }

    const { data } = await api.post('/applicant/applications', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
    });
    return data;
}

async function uploadFile(applicationId, qualificationId, file) {
    const formData = new FormData();
    formData.append('qualification_id', qualificationId);
    formData.append('file', file);

    const { data } = await api.post(
        `/applicant/applications/${applicationId}/files`, formData,
        { headers: { 'Content-Type': 'multipart/form-data' } }
    );
    return data;
}
```

### Batch Scoring (Async)
```javascript
async function runBatch(scholarshipId) {
    const { data } = await api.post(`/admin/scholarships/${scholarshipId}/batch-scoring`);
    return data; // 202 Accepted
}

async function pollProgress(scholarshipId) {
    const { data } = await api.get(`/admin/scholarships/${scholarshipId}/batch-scoring/progress`);
    return data.data; // { stage: 'processing_renewal', progress: 50 }
}
```

---

## 13. OpenAPI (Swagger) — Format YAML

```yaml
openapi: 3.0.0
info:
  title: Platform Beasiswa API
  description: REST API untuk sistem manajemen beasiswa
  version: 1.0.0
servers:
  - url: http://localhost:8000/api
    description: Development server

components:
  securitySchemes:
    bearerAuth:
      type: http
      scheme: bearer
      bearerFormat: plainText

  schemas:
    User:
      type: object
      properties:
        id: { type: integer }
        name: { type: string }
        email: { type: string, format: email }
        phone: { type: string, nullable: true }
        nik: { type: string, nullable: true }
        roles: { type: array, items: { type: string } }
        is_active: { type: boolean }
        is_blacklisted: { type: boolean }
        created_at: { type: string, format: datetime }

    LoginRequest:
      type: object
      required: [email, password]
      properties:
        email: { type: string, format: email }
        password: { type: string, format: password }
        device_name: { type: string }

    TokenResponse:
      type: object
      properties:
        data:
          type: object
          properties:
            user: { $ref: '#/components/schemas/User' }
            token: { type: string }

    ErrorResponse:
      type: object
      properties:
        message: { type: string }
        errors: { type: object, nullable: true }

    PaginationMeta:
      type: object
      properties:
        current_page: { type: integer }
        last_page: { type: integer }
        per_page: { type: integer }
        total: { type: integer }

paths:
  /auth/login:
    post:
      tags: [Auth]
      summary: Login
      requestBody:
        required: true
        content:
          application/json:
            schema: { $ref: '#/components/schemas/LoginRequest' }
      responses:
        '200':
          description: Login berhasil
          content:
            application/json:
              schema: { $ref: '#/components/schemas/TokenResponse' }
        '401':
          description: Email atau password salah
          content:
            application/json:
              schema: { $ref: '#/components/schemas/ErrorResponse' }

  /auth/register:
    post:
      tags: [Auth]
      summary: Registrasi
      requestBody:
        required: true
        content:
          application/json:
            schema:
              type: object
              required: [name, nik, email, phone, password, password_confirmation]
              properties:
                name: { type: string }
                nik: { type: string }
                email: { type: string, format: email }
                password: { type: string, format: password }
                password_confirmation: { type: string }
      responses:
        '201':
          description: Registrasi berhasil
          content:
            application/json:
              schema: { $ref: '#/components/schemas/TokenResponse' }

  /auth/logout:
    post:
      tags: [Auth]
      summary: Logout
      security: [{ bearerAuth: [] }]
      responses: { '200': { description: Logout berhasil } }

  /auth/user:
    get:
      tags: [Auth]
      summary: User info
      security: [{ bearerAuth: [] }]
      responses:
        '200':
          description: User info
          content:
            application/json:
              schema:
                type: object
                properties:
                  data: { $ref: '#/components/schemas/User' }

  /scholarships:
    get:
      tags: [Public]
      summary: Daftar program beasiswa
      responses:
        '200':
          description: Daftar scholarship

  /scholarships/{scholarship}/form-config:
    get:
      tags: [Public]
      summary: Form config untuk pendaftaran
      parameters:
        - name: scholarship
          in: path
          required: true
          schema: { type: string }
      responses:
        '200':
          description: Form config

  /applicant/applications:
    get:
      tags: [Applicant]
      summary: Daftar pendaftaran saya
      security: [{ bearerAuth: [] }]
      responses:
        '200':
          description: Daftar aplikasi
    post:
      tags: [Applicant]
      summary: Buat pendaftaran baru
      security: [{ bearerAuth: [] }]
      requestBody:
        required: true
        content:
          multipart/form-data:
            schema:
              type: object
              required: [scholarship_slug]
              properties:
                scholarship_slug: { type: string }
                is_draft: { type: boolean }
                answers: { type: object }
                files: { type: object }
      responses:
        '201':
          description: Pendaftaran berhasil
        '403':
          description: Blacklisted

  /profile:
    get:
      tags: [Profile]
      summary: Lihat profil
      security: [{ bearerAuth: [] }]
      responses: { '200': { description: User profile } }
    put:
      tags: [Profile]
      summary: Update profil
      security: [{ bearerAuth: [] }]
      responses: { '200': { description: Profile updated } }

  /profile/password:
    put:
      tags: [Profile]
      summary: Ubah password
      security: [{ bearerAuth: [] }]
      responses: { '200': { description: Password changed } }

  /admin/dashboard:
    get:
      tags: [Admin]
      summary: Dashboard admin
      security: [{ bearerAuth: [] }]
      responses: { '200': { description: Stats } }

  /admin/scholarships:
    get:
      tags: [Admin, Scholarships]
      summary: Daftar program (admin)
      security: [{ bearerAuth: [] }]
      parameters:
        - name: filter[status]
          in: query
          schema: { type: string }
      responses: { '200': { description: Daftar scholarship } }

  /admin/scholarships/{scholarship}/batch-scoring:
    post:
      tags: [Admin, Batch]
      summary: Jalankan batch scoring
      security: [{ bearerAuth: [] }]
      responses: { '202': { description: Processing started } }

  /admin/users:
    get:
      tags: [Admin, Users]
      summary: Daftar pengguna
      security: [{ bearerAuth: [] }]
      parameters:
        - name: filter[role]
          in: query
          schema: { type: string }
      responses: { '200': { description: Daftar users } }
    post:
      tags: [Admin, Users]
      summary: Buat pengguna
      security: [{ bearerAuth: [] }]
      responses: { '201': { description: User created } }

  /approver/scholarships/{scholarship}/approve:
    post:
      tags: [Approver]
      summary: Tetapkan penerima
      security: [{ bearerAuth: [] }]
      responses: { '200': { description: Penerima ditetapkan } }

  /treasurer/disbursements:
    get:
      tags: [Treasurer]
      summary: Daftar pencairan
      security: [{ bearerAuth: [] }]
      parameters:
        - name: filter[status]
          in: query
          schema:
            type: string
            enum: [waiting, processing, disbursed, failed]
      responses: { '200': { description: Daftar pencairan } }

  /treasurer/disbursements/{disbursement}:
    put:
      tags: [Treasurer]
      summary: Update status pencairan
      security: [{ bearerAuth: [] }]
      parameters:
        - name: disbursement
          in: path
          required: true
          schema: { type: integer }
      responses: { '200': { description: Status updated } }
```

---

## 14. Postman Collection

Simpan sebagai `.json` lalu import ke Postman.

```json
{
    "info": {
        "name": "Platform Beasiswa API",
        "description": "API untuk sistem manajemen beasiswa",
        "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
    },
    "auth": { "type": "bearer", "bearer": [{ "key": "token", "value": "{{token}}", "type": "string" }] },
    "variable": [
        { "key": "base_url", "value": "http://localhost:8000/api" },
        { "key": "token", "value": "" }
    ],
    "item": [
        {
            "name": "Auth",
            "item": [
                {
                    "name": "Register", "request": {
                        "method": "POST", "url": "{{base_url}}/auth/register",
                        "header": [{ "key": "Content-Type", "value": "application/json" }],
                        "body": { "mode": "raw", "raw": "{\n    \"name\": \"Budi Santoso\",\n    \"nik\": \"3501010101990001\",\n    \"email\": \"budi@example.com\",\n    \"phone\": \"081234567890\",\n    \"password\": \"password123\",\n    \"password_confirmation\": \"password123\"\n}" }
                    }
                },
                {
                    "name": "Login", "request": {
                        "method": "POST", "url": "{{base_url}}/auth/login",
                        "header": [{ "key": "Content-Type", "value": "application/json" }],
                        "body": { "mode": "raw", "raw": "{\n    \"email\": \"superadmin@beasiswa.test\",\n    \"password\": \"password\"\n}" }
                    }
                },
                {
                    "name": "Logout", "request": {
                        "method": "POST", "url": "{{base_url}}/auth/logout"
                    }
                },
                {
                    "name": "Current User", "request": {
                        "method": "GET", "url": "{{base_url}}/auth/user"
                    }
                }
            ]
        },
        {
            "name": "Applicant",
            "item": [
                {
                    "name": "Submit Application", "request": {
                        "method": "POST", "url": "{{base_url}}/applicant/applications",
                        "header": [{ "key": "Content-Type", "value": "multipart/form-data" }],
                        "body": {
                            "mode": "formdata",
                            "formdata": [
                                { "key": "scholarship_slug", "value": "bbk-2025-2026" },
                                { "key": "answers[12]", "value": "45" }
                            ]
                        }
                    }
                },
                {
                    "name": "Upload File", "request": {
                        "method": "POST", "url": "{{base_url}}/applicant/applications/1/files",
                        "header": [{ "key": "Content-Type", "value": "multipart/form-data" }],
                        "body": {
                            "mode": "formdata",
                            "formdata": [
                                { "key": "qualification_id", "value": "15" },
                                { "key": "file", "type": "file", "src": "/path/to/file.pdf" }
                            ]
                        }
                    }
                },
                {
                    "name": "Update Bank", "request": {
                        "method": "PUT", "url": "{{base_url}}/applicant/applications/1/bank",
                        "header": [{ "key": "Content-Type", "value": "application/json" }],
                        "body": { "mode": "raw", "raw": "{\n    \"bank_name\": \"Bank Jatim\",\n    \"account_number\": \"001234567890\",\n    \"account_holder_name\": \"Budi Santoso\"\n}" }
                    }
                }
            ]
        },
        {
            "name": "Admin",
            "item": [
                {
                    "name": "Create Scholarship", "request": {
                        "method": "POST", "url": "{{base_url}}/admin/scholarships",
                        "header": [{ "key": "Content-Type", "value": "application/json" }],
                        "body": { "mode": "raw", "raw": "{\n    \"name\": \"BBK 2026/2027\",\n    \"slug\": \"bbk-2026-2027\",\n    \"quota_primary\": 100,\n    \"status\": \"draft\"\n}" }
                    }
                },
                {
                    "name": "Run Batch Scoring", "request": {
                        "method": "POST", "url": "{{base_url}}/admin/scholarships/1/batch-scoring"
                    }
                }
            ]
        },
        {
            "name": "Approver",
            "item": [
                {
                    "name": "Approve Recipients", "request": {
                        "method": "POST", "url": "{{base_url}}/approver/scholarships/1/approve",
                        "header": [{ "key": "Content-Type", "value": "application/json" }],
                        "body": { "mode": "raw", "raw": "{\n    \"selected_ids\": [1, 2, 3, 5]\n}" }
                    }
                }
            ]
        },
        {
            "name": "Treasurer",
            "item": [
                {
                    "name": "Update Disbursement", "request": {
                        "method": "PUT", "url": "{{base_url}}/treasurer/disbursements/1",
                        "header": [{ "key": "Content-Type", "value": "application/json" }],
                        "body": { "mode": "raw", "raw": "{\n    \"status\": \"disbursed\",\n    \"notes\": \"Pencairan berhasil\"\n}" }
                    }
                }
            ]
        }
    ]
}
```

---

## 15. Catatan Backend

| # | Catatan |
|---|---------|
| 1 | **Format tanggal** menggunakan **Y-m-d H:i:s** (WIB) |
| 2 | **Soft Delete** digunakan pada `Scholarships`, `Users` |
| 3 | **File storage** menggunakan **MinIO** (localhost:9000) |
| 4 | **File download** via **temporary signed URL** (berlaku 60 menit) |
| 5 | **NIK** dan **nomor rekening** dienkripsi di database |
| 6 | **Pagination** default 15, maks 100 per halaman |
| 7 | **Rate limit** global 60/menit, auth 5/menit, OTP 3/menit, upload 10/menit |
| 8 | **Upload** maks 2 MB (JPG/PNG/PDF) |
| 9 | **Batch scoring** async — dispatch job, polling progress via cache |
| 10 | **Token** berlaku hingga di-revoke manual (tanpa expiry) |
| 11 | **Asumsi:** Semua ID auto-increment integer (bukan UUID) |
| 12 | **Asumsi:** CORS dari `FRONTEND_URL` (default `localhost:3000`) |

---

> Dokumentasi ini siap dibagikan ke Frontend Developer.
