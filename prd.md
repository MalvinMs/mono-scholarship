# PRD — Platform Beasiswa Multi-Program
## Sistem Manajemen Beasiswa Generik dengan Dynamic Qualification Engine
### Arsitektur Monolith · Laravel 13 · Livewire v4 · Custom UI (shadcn-inspired)

**Versi:** 2.1  
**Tanggal:** 18 Juni 2026  
**Status:** Final — Siap Development  
**Changelog v2.1 (16-18 Juni 2026):**
- Q-01: Verifikator ditugaskan per beasiswa (relasi `scholarship_verifiers`)
- Q-02: Model renewal berbasis kuota antar periode (slot carry-forward)
- Q-03: Multi-program bersamaan diizinkan, satu pendaftar boleh daftar banyak program
- Q-04: Mekanisme blacklist pendaftar oleh verifikator dengan alasan tercatat
- Q-05: Template notifikasi dapat dikelola Admin dan Super Admin
- Q-06: Branding/kop surat di luar scope untuk saat ini
- Q-07: Skor hanya dihitung final setelah semua dokumen diverifikasi valid
- Q-08: Batas ukuran file upload 2 MB per file

**Changelog v2.1 (16 Juni 2026):**
- Q-09: UI direfactor — Flowbite dihapus, diganti komponen custom shadcn-inspired berbasis Tailwind CSS v4
- Q-10: Design tokens: semantic color system (14 tokens via CSS variables), font Inter, OKLCH colors
- Q-11: Icon library: Lucide Icons via blade-lucide-icons
- Q-12: Dark mode via CSS variables class strategy dengan warna shadcn default
- Q-13: 26 reusable Blade components di resources/views/components/ui/ (button, input, card, table, modal, drawer, dsb)
- Q-14: Mobile sidebar dengan hamburger menu dan drawer/sheet component
- Q-15: Fix Tailwind v4 JIT bug: `@source "../views";` diwajibkan di app.css agar class dari Blade terkompilasi
- Q-16: Typography menggunakan native Tailwind (text-5xl, text-4xl) alih-alih arbitrary values untuk menghindari issue line-height "gepeng"
- Q-17: Layout responsif wajib menggunakan container padding (px-6) untuk menghindari teks menabrak batas layar di mobile

**Changelog v2.2 (18 Juni 2026):**
- Q-18: Override Fortify `RegisterResponse` — setelah registrasi user diarahkan ke halaman OTP (`/email/verify`) bukan `/dashboard`
- Q-19: Override Fortify `LoginResponse` — cek `email_verified_at === null`, redirect ke OTP verification page jika belum verifikasi
- Q-20: Custom RegisterResponse binding di FortifyServiceProvider (singleton)

---

## 1. Latar Belakang & Visi

### 1.1 Masalah yang Diselesaikan

Pengelolaan beasiswa di tingkat pemerintah daerah dan institusi saat ini sangat fragmentatif: setiap program beasiswa memiliki form sendiri (Google Form), proses seleksi manual, dan tidak ada standarisasi. Ketika ada program beasiswa baru, tim harus membangun proses dari nol — form, penilaian, pemeringkatan, dan notifikasi semuanya manual.

Referensi konkret: Program Bantuan Beasiswa Kuliah (BBK) Kabupaten Madiun 2024 — 447 pendaftar, verifikasi manual oleh 6 orang, pemeringkatan semi-manual, tidak skalabel untuk pertumbuhan berikutnya.

### 1.2 Visi Platform

Membangun sebuah **platform beasiswa multi-program** di mana satu instalasi sistem mampu mengelola banyak jenis beasiswa secara bersamaan, masing-masing dengan konfigurasi independen:

- Kualifikasi seleksi (pertanyaan + pilihan jawaban + bobot skor) yang ditentukan oleh admin tanpa coding
- Dokumen persyaratan yang berbeda per beasiswa
- Aturan seleksi, tie-breaker, dan kuota yang independen per program
- Mekanisme renewal lintas periode dengan slot prioritas penerima aktif
- Verifikator yang ditugaskan spesifik per program

### 1.3 Contoh Program yang Berjalan dalam Satu Platform

| Program | Karakteristik |
|---|---|
| BBK Kabupaten Madiun 2024/2025 | 7 indikator kemiskinan + prestasi, kuota 50, verifikasi aktif |
| BBK Kabupaten Madiun 2025/2026 | Program lanjutan — slot renewal dari 2024/2025, kuota baru 100 |
| Beasiswa Prestasi Akademik | Berbasis IPK murni, tanpa indikator kemiskinan |
| Beasiswa Mahasiswa Baru | Berbasis DTKS, tidak butuh IPK |
| Beasiswa Hafidz Qur'an | Indikator hafalan + rekomendasi lembaga |

---

## 2. Tujuan Sistem

1. **Generalisasi** — Satu platform menjalankan program beasiswa apapun tanpa modifikasi kode.
2. **Otomatisasi scoring** — Skor dihitung otomatis berdasarkan konfigurasi yang admin buat; skor final hanya ditetapkan setelah semua dokumen pendaftar diverifikasi valid.
3. **Transparansi** — Pendaftar dapat melihat breakdown skor per indikator.
4. **Akuntabilitas** — Semua perubahan data, keputusan blacklist, dan tindakan verifikator tercatat dalam audit log yang tidak dapat dimanipulasi.
5. **Efisiensi operasional** — Admin mengkonfigurasi program baru dalam hitungan menit; proses seleksi yang sebelumnya memakan hari menjadi jam.
6. **Kontinuitas** — Sistem mendukung perpanjangan beasiswa lintas periode dengan mekanisme carry-forward kuota yang transparan.

---

## 3. Aktor Sistem & Kewenangan

| Aktor | Lingkup Akses | Kewenangan Utama |
|---|---|---|
| **Super Admin** | Platform-wide | Konfigurasi global, kelola semua admin, monitoring sistem, template notifikasi |
| **Admin Program** | Per instansi | Buat & konfigurasi beasiswa, tugaskan verifikator per beasiswa, jalankan seleksi, kelola pencairan, template notifikasi |
| **Verifikator** | Per beasiswa yang ditugaskan saja | Verifikasi dokumen, koreksi jawaban qualification, blacklist pendaftar |
| **Approver (Kepala)** | Per instansi | Dashboard eksekutif, approval final penetapan penerima |
| **Bendahara** | Per instansi | Kelola data pencairan, export rekening, update status transfer |
| **Pendaftar** | Akun pribadi | Daftar di beasiswa manapun yang terbuka, upload dokumen, pantau status, renewal semester |

> **Catatan:** Verifikator tidak memiliki akses lintas program. Jika seorang verifikator ditugaskan ke BBK 2024/2025, ia tidak dapat melihat antrian BBK 2025/2026 kecuali admin juga menugaskannya ke program tersebut secara eksplisit.

---

## 4. Ruang Lingkup

### Dalam Scope

- Manajemen program beasiswa dengan konfigurasi dinamis per program
- Dynamic Qualification Engine: pertanyaan + pilihan jawaban + bobot skor tanpa coding
- Penugasan verifikator per program (bukan platform-wide)
- Pendaftaran online multi-beasiswa (satu pendaftar boleh ikut banyak program)
- Skor sementara dihitung saat submit; skor final hanya setelah semua dokumen valid
- Verifikasi dokumen dengan fitur koreksi data, blacklist pendaftar, dan audit trail
- Mekanisme renewal lintas periode: slot penerima aktif dipotong dari kuota program baru
- Notifikasi WA + Email (dikonfigurasi per program oleh Admin atau Super Admin)
- Dashboard dan laporan per program
- Export Excel & PDF
- Sistem blacklist pendaftar

### Di Luar Scope (saat ini)

- Integrasi API Dukcapil / PDDikti / DTKS (roadmap Fase 3)
- Branding / kop surat pada export laporan
- Mobile app native (PWA via Blade cukup untuk MVP)
- Modul banding formal
- Multi-tenancy lintas instansi (roadmap Fase 4)

---

## 5. Konsep Inti

### 5.1 Dynamic Qualification Engine

Qualification Engine memungkinkan admin mendefinisikan seluruh aspek seleksi melalui UI tanpa menyentuh kode. Hierarki konfigurasinya:

```
Scholarship (Program Beasiswa)
  └── QualificationGroup (Kelompok — opsional, untuk pengelompokan UI)
        └── Qualification (Pertanyaan/Indikator Seleksi)
              ├── type: single_choice | multi_choice | numeric_range | file_upload | text
              ├── is_required: boolean
              ├── is_file_upload_required: boolean (apakah butuh bukti dokumen)
              ├── file_upload_label: string
              └── QualificationOption / QualificationRange (Pilihan + Skor)
```

**Tipe Qualification:**

| Tipe | Deskripsi | Contoh |
|---|---|---|
| `single_choice` | Pilih satu opsi | Asal sekolah, kepemilikan rumah |
| `multi_choice` | Pilih satu atau lebih (skor = nilai tertinggi dari pilihan) | Prestasi yang dimiliki |
| `numeric_range` | Input angka, skor dari range yang cocok | IPK, penghasilan orang tua |
| `file_upload` | Upload dokumen tanpa mempengaruhi skor | Dokumen tambahan umum |
| `text` | Teks bebas untuk review verifikator (tidak mempengaruhi skor) | Motivasi, narasi keluarga |

**Formula Scoring Generik:**
```
Skor Mentah = SUM(nilai jawaban tiap Qualification)
Skor Maks   = SUM(nilai tertinggi dari setiap Qualification)
```

### 5.2 Model Renewal Lintas Periode

Renewal bukan sekadar "perpanjangan dalam program yang sama" — renewal adalah proses di mana penerima aktif dari satu periode mendaftar ulang ke periode program berikutnya dengan **slot prioritas yang dipotong dari kuota program baru**.

**Mekanisme:**

```
BBK 2024/2025 — quota_primary: 50, diumumkan → 50 penerima aktif

Admin buat BBK 2025/2026 — quota_primary: 100
  → Admin menghubungkan: predecessor_scholarship_id = BBK 2024/2025
  → Sistem otomatis alokasikan:
      - quota_renewal_slots = jumlah penerima aktif dari 2024/2025 yang eligible
      - quota_new_applicant  = quota_primary - quota_renewal_slots (sisa untuk pendaftar baru)

Contoh:
  50 penerima aktif 2024/2025, semua dokumen renewal valid → quota_renewal_slots = 50
  quota_new_applicant = 100 - 50 = 50 (slot terbuka untuk pendaftar baru)

Jika hanya 40 dari 50 yang renewal dan memenuhi syarat:
  quota_renewal_slots = 40
  quota_new_applicant = 100 - 40 = 60
```

**Aturan Prioritas:**
- Slot renewal diproses terlebih dahulu sebelum ranking pendaftar baru.
- Penerima aktif yang memenuhi syarat renewal (IPK ≥ threshold, dokumen valid) secara otomatis masuk ke daftar penerima periode baru tanpa bersaing dengan pendaftar baru.
- Penerima aktif yang **tidak** submit renewal atau IPK di bawah threshold kehilangan slot — slot tersebut dialihkan ke `quota_new_applicant`.

### 5.3 Mekanisme Skor: Sementara vs Final

Sistem membedakan dua kondisi skor secara tegas:

| Kondisi | Skor | Keterangan |
|---|---|---|
| **Setelah Submit** | Skor Sementara | Dihitung dari jawaban form; `is_final = false` |
| **Setelah Verifikasi** | Skor Final | Dihitung ulang setelah semua dokumen disetujui verifikator; `is_final = true` |

Skor sementara ditampilkan ke pendaftar sebagai estimasi. Skor final adalah satu-satunya yang digunakan dalam proses ranking dan penetapan penerima.

> **Implikasi:** Meskipun admin menonaktifkan modul verifikasi pada suatu program, sistem tetap memerlukan konfirmasi manual dari admin untuk mengubah semua skor menjadi final sebelum batch ranking dapat dijalankan. Tidak ada skor yang langsung final 100% otomatis tanpa satu titik review.

### 5.4 Mekanisme Blacklist

Verifikator dapat memblacklist pendaftar dengan alasan yang tercatat jika ditemukan pelanggaran deklarasi (contoh: klaim tidak menerima beasiswa lain namun terbukti memiliki beasiswa aktif dari dokumen yang diupload).

**Efek blacklist:**
- Pendaftar yang di-blacklist tidak dapat mendaftar ke program manapun di platform ini selama status blacklist aktif.
- Admin dapat mencabut blacklist dengan alasan tertulis.
- Seluruh riwayat blacklist (kapan, oleh siapa, alasan, apakah dicabut) tersimpan dalam `blacklist_logs`.

---

## 6. Alur Bisnis

### 6.1 Setup Program Beasiswa Baru (Admin)

```
Login Admin
  → Buat Scholarship (nama, kuota, tanggal buka/tutup, deskripsi)
  → Jika program lanjutan: tentukan predecessor_scholarship_id
  → Konfigurasi Qualification (tambah indikator + opsi + skor)
  → Konfigurasi Tie-breaker (urutkan qualification)
  → Konfigurasi Notifikasi (channel WA/Email, template per event)
  → Tugaskan Verifikator (pilih dari daftar user berole verifier)
  → Publikasikan (manual atau terjadwal via date_start)
```

### 6.2 Pendaftaran (Mahasiswa)

```
Daftar Akun → Verifikasi OTP (WA atau Email, sesuai konfigurasi program)
  → Lengkapi Profil (data diri, pendidikan, keluarga)
  → Lihat Daftar Beasiswa Aktif
  → Pilih Beasiswa → Cek apakah diblacklist → Cek apakah sudah daftar di periode ini
  → Isi Form Qualification Dinamis (per program)
  → Upload Dokumen (sesuai konfigurasi qualification)
  → Simpan Draft / Submit
  → Terima Nomor Registrasi
  → Skor Sementara Dihitung Otomatis
  → Pantau Status via Dashboard
```

### 6.3 Verifikasi (Verifikator)

```
Login → Pilih Program yang Ditugaskan
  → Lihat Antrian Pendaftar (filter: status, progress dokumen)
  → Buka Detail Pendaftar
  → Review tiap dokumen per qualification:
      - Approve → lanjut
      - Reject → input alasan → pendaftar diminta re-upload (status: needs_revision)
      - Koreksi jawaban qualification (jika dokumen tidak sesuai klaim) → input alasan
  → Setelah semua dokumen approved:
      - Sistem hitung ulang skor final (is_final = true)
      - Status pendaftar → verified
  → Atau: Blacklist pendaftar (jika ditemukan pelanggaran) → input alasan detail
```

### 6.4 Seleksi & Penetapan

```
Admin Tutup Periode Pendaftaran
  → Proses Renewal terlebih dahulu:
      - Identifikasi penerima aktif predecessor yang submit renewal & valid
      - Alokasikan ke slot renewal (potong dari quota_primary)
      - Hitung sisa kuota untuk pendaftar baru
  → Jalankan Batch Ranking pendaftar baru (Job Queue):
      - Hanya proses pendaftar dengan status verified (is_final = true)
      - Terapkan ranking berdasarkan total_score DESC
      - Terapkan tie-breaker sesuai urutan konfigurasi
      - Klasifikasikan: Utama / Cadangan / Tidak Lolos
  → Admin / Approver review hasil ranking
  → Approver setujui penetapan → skor dan ranking dikunci (immutable)
  → Sistem kirim notifikasi ke semua pendaftar
  → Halaman pengumuman publik aktif
```

### 6.5 Pencairan Dana

```
Penerima Lolos → Konfirmasi Data Rekening Bank
  → Bendahara review daftar penerima + rekening
  → Export Excel (format siap transfer)
  → Proses transfer via bank (di luar sistem)
  → Bendahara update status: waiting → processing → disbursed
```

### 6.6 Renewal ke Periode Berikutnya

```
Admin buat Scholarship periode baru (dengan predecessor_scholarship_id)
  → Sistem tampilkan estimasi slot renewal dari penerima aktif predecessor
  → Admin buka periode renewal
  → Penerima aktif dapat notifikasi: "Program renewal dibuka, segera submit"
  → Penerima upload transkrip terbaru + input IPK
  → Verifikator yang ditugaskan ke program baru verifikasi dokumen renewal
  → Jika valid dan IPK ≥ min_gpa_renewal → masuk slot prioritas (tidak bersaing ranking)
  → Jika tidak valid / tidak submit → slot dilepas ke quota_new_applicant
  → Admin trigger finalisasi renewal → slot terkunci → buka pendaftaran baru
```

---

## 7. Persyaratan Fungsional

### 7.1 Manajemen Program Beasiswa

- **F-01** Admin membuat program beasiswa: nama, deskripsi, tahun anggaran, kuota utama, tanggal buka/tutup, besaran dana.
- **F-02** Admin dapat menentukan `predecessor_scholarship_id` untuk program lanjutan, yang mengaktifkan mekanisme slot renewal.
- **F-03** Admin dapat mengatur pembukaan otomatis berdasarkan `date_start` via scheduled job, atau manual.
- **F-04** Admin dapat menduplikasi konfigurasi qualification dari program yang sudah ada sebagai template.
- **F-05** Admin dapat menugaskan satu atau lebih verifikator ke suatu program. Verifikator yang tidak ditugaskan tidak dapat mengakses antrian program tersebut.
- **F-06** Admin dapat mengonfigurasi notifikasi per program: channel (WA / Email / keduanya), template pesan per event. Super Admin juga memiliki akses yang sama.

### 7.2 Dynamic Qualification Builder

- **F-07** Admin menambahkan Qualification dengan tipe: `single_choice`, `multi_choice`, `numeric_range`, `file_upload`, `text`.
- **F-08** Untuk `single_choice` dan `multi_choice`: admin menambahkan pilihan jawaban beserta nilai skor per pilihan.
- **F-09** Untuk `numeric_range`: admin mendefinisikan range angka (min–max) dan nilai skor per range. Sistem memvalidasi tidak ada range yang overlap saat admin menyimpan.
- **F-10** Admin menandai apakah jawaban qualification memerlukan bukti dokumen (`is_file_upload_required`). Jika aktif, dokumen wajib diupload dan wajib diverifikasi sebelum skor bisa final.
- **F-11** Admin mengatur urutan qualification (drag-and-drop) dan mengelompokkannya dalam `QualificationGroup`.
- **F-12** Admin mengonfigurasi urutan tie-breaker dari daftar qualification yang ada (drag-and-drop).
- **F-13** Konfigurasi qualification dikunci setelah ada satu pendaftar masuk. Perubahan hanya diizinkan melalui duplikasi program ke program baru.

### 7.3 Pendaftaran Akun & Profil

- **F-14** Pendaftar mendaftar akun dengan NIK, nama, email, dan nomor WhatsApp.
- **F-15** Verifikasi OTP wajib setelah daftar. Channel OTP (WA / Email) dikonfigurasi per program.
- **F-16** Pendaftar melengkapi profil: data diri (TTL, alamat, kecamatan, desa), data pendidikan (jenjang, kampus, prodi, semester), data keluarga.
- **F-17** Data profil dapat diperbarui kapan saja; namun data yang sudah ter-submit ke suatu beasiswa tidak berubah retroaktif (snapshot disimpan di `applications.snapshot_profile` saat submit).
- **F-18** Sistem mendeteksi duplikasi akun berdasarkan NIK.
- **F-19** Sistem mencegah pendaftar yang berstatus blacklist aktif dari mendaftar ke program manapun.

### 7.4 Pendaftaran Beasiswa

- **F-20** Pendaftar melihat daftar program beasiswa yang sedang buka. Satu pendaftar dapat mengikuti lebih dari satu program sekaligus.
- **F-21** Sistem menampilkan syarat, deskripsi, dan estimasi skor maksimum sebelum pendaftar memulai pengisian.
- **F-22** Form pendaftaran di-generate dinamis berdasarkan konfigurasi Qualification program tersebut.
- **F-23** Sistem mencegah duplikasi: satu NIK hanya boleh mendaftar satu kali per program per periode.
- **F-24** Pendaftar dapat menyimpan draft dan melanjutkan pengisian sebelum batas penutupan.
- **F-25** Setelah submit: pendaftar menerima nomor registrasi unik, skor sementara dihitung otomatis, dan breakdown skor per qualification ditampilkan.
- **F-26** Ukuran maksimum per file yang diupload adalah **2 MB**. Sistem menolak file yang melebihi batas dan menampilkan pesan error yang jelas. Format yang diterima: JPG, PNG, PDF.

### 7.5 Verifikasi Dokumen

- **F-27** Verifikator hanya dapat mengakses antrian pendaftar dari program yang ditugaskan kepadanya oleh admin.
- **F-28** Antrian verifikasi menampilkan daftar pendaftar dengan filter: status, progress dokumen (x dari y dokumen disetujui), dan urutan waktu submit.
- **F-29** Verifikator me-review setiap dokumen: approve, reject (dengan alasan), atau request re-upload.
- **F-30** Verifikator dapat mengoreksi jawaban qualification jika dokumen tidak sesuai klaim, disertai alasan wajib. Setiap koreksi dicatat dalam `verification_logs` dengan nilai lama dan nilai baru.
- **F-31** Setelah seluruh dokumen wajib disetujui, sistem otomatis menghitung ulang skor final (`is_final = true`) dan mengubah status pendaftar menjadi `verified`.
- **F-32** Pendaftar dengan status `needs_revision` dapat meng-upload ulang dokumen yang ditolak. Setelah re-upload, status kembali ke `under_review` dan masuk antrian verifikator kembali.
- **F-33** Verifikator dapat memblacklist pendaftar jika ditemukan pelanggaran deklarasi. Blacklist wajib disertai alasan tertulis yang jelas dan tercatat dalam `blacklist_logs`. Admin dapat mencabut blacklist.

### 7.6 Scoring & Seleksi

- **F-34** Skor sementara dihitung otomatis saat: pendaftar pertama kali submit, pendaftar re-submit setelah revisi.
- **F-35** Skor final dihitung ulang otomatis oleh sistem setelah verifikator menyetujui seluruh dokumen wajib pendaftar (`is_final = true`).
- **F-36** Hanya pendaftar dengan `is_final = true` yang masuk dalam proses batch ranking.
- **F-37** Slot renewal diproses dan dikunci lebih dahulu sebelum ranking pendaftar baru dimulai.
- **F-38** Batch ranking dijalankan sebagai Job Queue (non-blocking) dengan progress indicator real-time untuk admin.
- **F-39** Tie-breaker diterapkan otomatis sesuai urutan konfigurasi; setiap langkah tie-breaker dicatat dalam `tiebreaker_log`.
- **F-40** Hasil diklasifikasikan: Lolos Utama (≤ sisa kuota setelah renewal), Lolos Cadangan (≤ `quota_reserve`), Tidak Lolos.
- **F-41** Admin dan Approver dapat mereview hasil ranking sebelum penetapan final.
- **F-42** Setelah Approver menyetujui, skor, ranking, dan hasil seleksi dikunci (immutable). Notifikasi dikirim ke semua pendaftar.

### 7.7 Pengumuman

- **F-43** Halaman publik pengumuman: pendaftar cek hasil menggunakan nomor registrasi tanpa login.
- **F-44** Pendaftar yang lolos melihat instruksi langkah selanjutnya (konfirmasi data rekening).
- **F-45** Notifikasi WA/Email dikirim ke seluruh pendaftar sesuai hasil masing-masing.

### 7.8 Renewal Semester

- **F-46** Admin membuat program periode baru dan menghubungkannya ke `predecessor_scholarship_id`.
- **F-47** Sistem menghitung estimasi `quota_renewal_slots` berdasarkan jumlah penerima aktif dari predecessor.
- **F-48** Admin membuka periode renewal; penerima aktif dari predecessor mendapat notifikasi.
- **F-49** Penerima aktif mengajukan renewal: upload transkrip terbaru dan input IPK.
- **F-50** Verifikator program baru memverifikasi dokumen renewal. Jika IPK < `min_gpa_renewal` → renewal ditolak, slot dilepas.
- **F-51** Penerima yang lolos renewal otomatis masuk daftar penerima program baru (tidak bersaing ranking dengan pendaftar baru).
- **F-52** Slot dari penerima yang tidak renewal atau tidak memenuhi syarat dialihkan ke kuota pendaftar baru.
- **F-53** Admin dapat mengonfigurasi apakah pendaftar baru bisa mendaftar sebelum atau sesudah periode renewal ditutup.

### 7.9 Pencairan Dana

- **F-54** Penerima lolos mengisi data rekening bank: nama bank, nomor rekening, nama pemegang rekening.
- **F-55** Bendahara melihat daftar penerima per program beserta status pencairan dan data rekening.
- **F-56** Bendahara mengekspor data rekening dalam format Excel.
- **F-57** Bendahara memperbarui status pencairan (`waiting → processing → disbursed`), bulk update diizinkan.
- **F-58** Riwayat perubahan status pencairan tercatat beserta user yang mengubah dan timestamp.

### 7.10 Dashboard & Laporan

- **F-59** Dashboard Admin: jumlah pendaftar per status, progress verifikasi, sebaran skor, sebaran per wilayah/kampus, per program.
- **F-60** Dashboard Approver: ringkasan eksekutif semua program aktif, tren pendaftar per tahun, total anggaran terserap vs alokasi.
- **F-61** Laporan export: daftar penerima (Excel & PDF), rekap pencairan, audit log verifikasi per program.
- **F-62** Admin dapat melihat log blacklist: siapa yang diblacklist, oleh siapa, kapan, alasan, dan status (aktif/dicabut).

---

## 8. Persyaratan Non-Fungsional

| Kategori | Requirement |
|---|---|
| **Performa** | Halaman pendaftaran load < 3 detik. Batch scoring 1.000 pendaftar < 60 detik via Queue. Dynamic form generation tidak boleh menambah latency > 500ms dibanding form statis. |
| **Kapasitas** | Minimal 1.000 pendaftar per periode per program. Minimal 10 program beasiswa aktif bersamaan. |
| **Ketersediaan** | Uptime ≥ 99,5% selama periode pendaftaran aktif. |
| **Upload File** | Maksimum **2 MB per file**. Format yang diterima: JPG, PNG, PDF. Validasi di sisi client (Livewire) dan sisi server (Laravel validation rule). File ditolak sebelum dikirim ke MinIO jika melebihi batas. |
| **Keamanan** | HTTPS wajib. NIK dan nomor rekening dienkripsi at-rest (`encrypted` cast). RBAC via Spatie Permission. Session timeout 30 menit untuk admin/verifikator. File dokumen tidak dapat diakses publik — akses via signed temporary URL MinIO (expires 1 jam). |
| **Audit** | Setiap tindakan verifikator (koreksi data, approve/reject dokumen, blacklist) wajib tercatat dengan actor, timestamp, nilai lama, nilai baru, dan alasan. Log `verification_logs` dan `blacklist_logs` bersifat immutable. |
| **Integritas Skor** | Skor final (`is_final = true`) tidak dapat diubah setelah ditetapkan. Skor yang sudah di-approve Approver dikunci pada level model. |
| **Aksesibilitas** | Mobile-first — Tailwind Custom Components responsive. Mobile sidebar via drawer. Mayoritas pendaftar mengakses dari HP. |
| **Browser Support** | Chrome, Firefox, Safari versi 2 tahun terakhir. |

---

## 9. Struktur Data

### 9.1 Entity Relationship Overview

```
users ──────────────────────── applications ─────── application_answers
  │                                │   │                    │
  │ (via scholarship_verifiers)    │   └─── application_documents
  │                                │   └─── application_scores
scholarships ───────────────────  │
  │ (predecessor_scholarship_id) ──┘
  │
  ├── qualification_groups
  │     └── qualifications
  │           ├── qualification_options
  │           └── qualification_ranges
  │
  ├── scholarship_verifiers (pivot: scholarship_id, user_id)
  │
  └── disbursements

verification_logs  (immutable audit trail)
blacklist_logs     (immutable audit trail)
notifications_log
otp_verifications
```

### 9.2 Skema Tabel

---

**`users`** — Akun semua pengguna platform
```sql
id                   BIGINT PRIMARY KEY
name                 VARCHAR(255) NOT NULL
nik                  TEXT ENCRYPTED NOT NULL  -- Laravel encrypted cast
email                VARCHAR(255) UNIQUE
phone                VARCHAR(20)              -- nomor WhatsApp
email_verified_at    TIMESTAMP NULLABLE
phone_verified_at    TIMESTAMP NULLABLE

-- Data profil (untuk pendaftar)
birth_date           DATE NULLABLE
birth_place          VARCHAR(255) NULLABLE
address              TEXT NULLABLE
village              VARCHAR(255) NULLABLE    -- desa/kelurahan
district             VARCHAR(255) NULLABLE    -- kecamatan
city                 VARCHAR(255) NULLABLE
province             VARCHAR(255) NULLABLE
education_level      ENUM(SMA,SMK,MA,PAKET_C,D3,D4,S1,S2) NULLABLE
school_name          VARCHAR(255) NULLABLE
nisn                 VARCHAR(20) NULLABLE
university_name      VARCHAR(255) NULLABLE
major                VARCHAR(255) NULLABLE
current_semester     SMALLINT NULLABLE

is_active            BOOLEAN DEFAULT true
is_blacklisted       BOOLEAN DEFAULT false    -- flag cepat untuk pengecekan awal
created_at, updated_at

-- Role dikelola via Spatie Permission (model_has_roles)
-- is_blacklisted adalah cache; sumber kebenaran tetap di blacklist_logs
```

---

**`scholarships`** — Program beasiswa
```sql
id                        BIGINT PRIMARY KEY
name                      VARCHAR(255) NOT NULL
slug                      VARCHAR(255) UNIQUE NOT NULL
predecessor_scholarship_id BIGINT NULLABLE FK → scholarships.id
                          -- Jika diisi, program ini adalah lanjutan dari program tersebut
                          -- dan mekanisme renewal aktif

description               TEXT
academic_year             VARCHAR(20)          -- contoh: "2025/2026"
fund_amount               BIGINT               -- besaran dana per penerima (Rupiah)
quota_primary             SMALLINT NOT NULL    -- total kuota program ini
quota_reserve             SMALLINT DEFAULT 0   -- buffer cadangan
quota_renewal_locked      SMALLINT DEFAULT 0   -- diisi sistem saat renewal finalisasi
                          -- quota_new_applicant = quota_primary - quota_renewal_locked

date_start                DATE
date_end                  DATE
status                    ENUM(draft,open,renewal_open,renewal_closed,closed,selecting,announced)
                          -- renewal_open: periode renewal aktif untuk penerima predecessor
                          -- renewal_closed: renewal selesai, pendaftaran baru dibuka

is_verification_enabled   BOOLEAN DEFAULT true
notification_channels     JSONB  -- {whatsapp: bool, email: bool}
notification_templates    JSONB  -- {registered: "...", status_changed: "...", result: "..."}
otp_channel               ENUM(whatsapp,email,both) DEFAULT whatsapp
min_gpa_renewal           DECIMAL(3,2) DEFAULT 3.50
scoring_display_mode      ENUM(absolute,percentage) DEFAULT absolute
tiebreaker_config         JSONB  -- [{qualification_id: X, priority: 1}, ...]

created_by                BIGINT FK → users.id
published_at              TIMESTAMP NULLABLE
created_at, updated_at, deleted_at
```

---

**`scholarship_verifiers`** — Penugasan verifikator per program
```sql
id                  BIGINT PRIMARY KEY
scholarship_id      BIGINT FK → scholarships.id
user_id             BIGINT FK → users.id   -- harus memiliki role verifier
assigned_by         BIGINT FK → users.id   -- admin yang menugaskan
assigned_at         TIMESTAMP
created_at, updated_at

UNIQUE(scholarship_id, user_id)
```

---

**`qualification_groups`** — Kelompok indikator (opsional, untuk UI)
```sql
id              BIGINT PRIMARY KEY
scholarship_id  BIGINT FK → scholarships.id
name            VARCHAR(255)
description     TEXT NULLABLE
sort_order      SMALLINT DEFAULT 0
created_at, updated_at
```

---

**`qualifications`** — Indikator/pertanyaan seleksi per program
```sql
id                        BIGINT PRIMARY KEY
scholarship_id            BIGINT FK → scholarships.id
qualification_group_id    BIGINT NULLABLE FK → qualification_groups.id
name                      VARCHAR(255) NOT NULL
description               TEXT NULLABLE          -- hint yang ditampilkan ke pendaftar
type                      ENUM(single_choice,multi_choice,numeric_range,file_upload,text)
is_required               BOOLEAN DEFAULT true
is_file_upload_required   BOOLEAN DEFAULT false  -- apakah jawaban ini butuh bukti dokumen
file_upload_label         VARCHAR(255) NULLABLE  -- contoh: "Upload Bukti DTKS"
file_upload_description   TEXT NULLABLE
sort_order                SMALLINT DEFAULT 0
created_at, updated_at
```

---

**`qualification_options`** — Pilihan jawaban untuk single_choice / multi_choice
```sql
id                  BIGINT PRIMARY KEY
qualification_id    BIGINT FK → qualifications.id
label               VARCHAR(255) NOT NULL    -- ditampilkan ke pendaftar
value               SMALLINT NOT NULL        -- poin skor
description         TEXT NULLABLE            -- penjelasan tambahan pilihan
sort_order          SMALLINT DEFAULT 0
created_at, updated_at
```

---

**`qualification_ranges`** — Range nilai untuk tipe numeric_range
```sql
id                  BIGINT PRIMARY KEY
qualification_id    BIGINT FK → qualifications.id
range_min           DECIMAL(10,2) NOT NULL
range_max           DECIMAL(10,2) NOT NULL
value               SMALLINT NOT NULL        -- poin skor untuk range ini
label               VARCHAR(255) NULLABLE    -- contoh: "IPK Sangat Baik"
sort_order          SMALLINT DEFAULT 0
created_at, updated_at

-- Constraint: range tidak boleh overlap antar baris dengan qualification_id yang sama
-- Divalidasi di application layer (ScoringEngine) saat admin menyimpan
```

---

**`applications`** — Pendaftaran beasiswa per pendaftar
```sql
id                      BIGINT PRIMARY KEY
scholarship_id          BIGINT FK → scholarships.id
user_id                 BIGINT FK → users.id
registration_number     VARCHAR(50) UNIQUE NOT NULL   -- generated: BBK2025-00001
snapshot_profile        JSONB NOT NULL                -- snapshot user data saat submit (immutable)
status                  ENUM(draft,submitted,under_review,needs_revision,verified,selected,rejected)
is_renewal              BOOLEAN DEFAULT false
previous_application_id BIGINT NULLABLE FK → applications.id  -- link ke penerimaan periode sebelumnya

submitted_at            TIMESTAMP NULLABLE
verified_at             TIMESTAMP NULLABLE
selected_at             TIMESTAMP NULLABLE
rejection_reason        TEXT NULLABLE
created_at, updated_at

UNIQUE INDEX (scholarship_id, user_id) WHERE status != 'draft'
-- Satu user hanya boleh punya satu aplikasi non-draft per program
```

---

**`application_answers`** — Jawaban pendaftar per qualification
```sql
id                          BIGINT PRIMARY KEY
application_id              BIGINT FK → applications.id
qualification_id            BIGINT FK → qualifications.id

-- single_choice
selected_option_id          BIGINT NULLABLE FK → qualification_options.id
-- multi_choice
selected_option_ids         JSONB NULLABLE                -- array of qualification_option.id
-- numeric_range
numeric_value               DECIMAL(10,2) NULLABLE        -- angka yang diinput pendaftar
-- text
text_value                  TEXT NULLABLE

computed_score              SMALLINT NOT NULL DEFAULT 0   -- skor dari jawaban ini

-- Koreksi oleh verifikator
is_corrected_by_verifier    BOOLEAN DEFAULT false
original_selected_option_id BIGINT NULLABLE               -- nilai sebelum koreksi
original_numeric_value      DECIMAL(10,2) NULLABLE
corrected_at                TIMESTAMP NULLABLE
corrected_by                BIGINT NULLABLE FK → users.id

created_at, updated_at

UNIQUE(application_id, qualification_id)
```

---

**`application_documents`** — Dokumen yang diupload per qualification
```sql
id                      BIGINT PRIMARY KEY
application_id          BIGINT FK → applications.id
qualification_id        BIGINT NULLABLE FK → qualifications.id
doc_label               VARCHAR(255)              -- dari file_upload_label qualification
file_path               VARCHAR(500)              -- path di MinIO
file_name               VARCHAR(255)              -- nama file asli
file_size               INT                       -- bytes, maksimum 2.097.152 (2MB)
mime_type               VARCHAR(100)
uploaded_at             TIMESTAMP

verification_status     ENUM(pending,approved,rejected) DEFAULT pending
verified_by             BIGINT NULLABLE FK → users.id
verified_at             TIMESTAMP NULLABLE
rejection_reason        TEXT NULLABLE
created_at, updated_at
```

---

**`application_scores`** — Rekapitulasi skor per pendaftar
```sql
id                  BIGINT PRIMARY KEY
application_id      BIGINT UNIQUE FK → applications.id
scholarship_id      BIGINT FK → scholarships.id

score_breakdown     JSONB   -- {qualification_id: {name, answer_label, score}, ...}
total_score         SMALLINT NOT NULL DEFAULT 0
max_possible_score  SMALLINT NOT NULL

-- Diisi saat batch ranking
rank                INT NULLABLE
tiebreaker_log      JSONB NULLABLE   -- [{step: 1, qualification: "Kemiskinan", winner: "id_X"}, ...]
selection_result    ENUM(utama,cadangan,tidak_lolos) NULLABLE

is_final            BOOLEAN DEFAULT false   -- true setelah semua dokumen verified
finalized_at        TIMESTAMP NULLABLE      -- saat Approver setujui penetapan
calculated_at       TIMESTAMP
```

---

**`verification_logs`** — Audit trail tindakan verifikator (immutable)
```sql
id              BIGINT PRIMARY KEY
application_id  BIGINT FK → applications.id
verifier_id     BIGINT FK → users.id
action          ENUM(document_approved,document_rejected,document_rerequested,
                     answer_corrected,status_changed,applicant_blacklisted)
target_type     ENUM(document,answer,application)
target_id       BIGINT              -- ID dokumen atau jawaban yang diubah
field_changed   VARCHAR(255) NULLABLE
old_value       TEXT NULLABLE
new_value       TEXT NULLABLE
reason          TEXT NOT NULL
created_at      TIMESTAMP

-- Tidak ada updated_at / deleted_at — log ini immutable
-- Tidak ada operasi DELETE pada tabel ini di production
```

---

**`blacklist_logs`** — Riwayat blacklist pendaftar (immutable)
```sql
id              BIGINT PRIMARY KEY
user_id         BIGINT FK → users.id       -- pendaftar yang diblacklist
application_id  BIGINT FK → applications.id  -- konteks: dari program mana
blacklisted_by  BIGINT FK → users.id       -- verifikator yang melakukan
reason          TEXT NOT NULL
is_active       BOOLEAN DEFAULT true       -- false jika sudah dicabut
revoked_by      BIGINT NULLABLE FK → users.id
revoked_at      TIMESTAMP NULLABLE
revoke_reason   TEXT NULLABLE
created_at      TIMESTAMP

-- Tidak ada updated_at / deleted_at — append-only
-- is_active di-update saat pencabutan (satu-satunya exception)
```

---

**`disbursements`** — Data pencairan dana
```sql
id                  BIGINT PRIMARY KEY
application_id      BIGINT FK → applications.id
scholarship_id      BIGINT FK → scholarships.id
bank_name           VARCHAR(100)
account_number      TEXT ENCRYPTED          -- Laravel encrypted cast
account_holder_name VARCHAR(255)
amount              BIGINT                  -- nominal yang dicairkan (Rupiah)
status              ENUM(waiting,processing,disbursed) DEFAULT waiting
notes               TEXT NULLABLE
disbursed_at        TIMESTAMP NULLABLE
processed_by        BIGINT FK → users.id
created_at, updated_at
```

---

**`otp_verifications`** — OTP verifikasi akun
```sql
id          BIGINT PRIMARY KEY
user_id     BIGINT FK → users.id
channel     ENUM(whatsapp,email)
code        VARCHAR(255)        -- bcrypt hashed
expires_at  TIMESTAMP
is_used     BOOLEAN DEFAULT false
created_at
```

---

**`notifications_log`** — Log notifikasi yang dikirim
```sql
id              BIGINT PRIMARY KEY
user_id         BIGINT FK → users.id
application_id  BIGINT NULLABLE FK → applications.id
channel         ENUM(whatsapp,email)
event_type      ENUM(registered,otp,status_changed,needs_revision,
                     result_announced,renewal_reminder,disbursed,blacklisted)
recipient       VARCHAR(255)     -- nomor WA atau alamat email
message_body    TEXT
status          ENUM(sent,failed)
error_message   TEXT NULLABLE
sent_at         TIMESTAMP
created_at
```

### 9.3 Indeks Penting

```sql
-- Pencegahan duplikasi pendaftaran (kecuali draft)
CREATE UNIQUE INDEX idx_applications_unique_active
  ON applications(scholarship_id, user_id)
  WHERE status != 'draft';

-- Performance ranking query
CREATE INDEX idx_scores_ranking
  ON application_scores(scholarship_id, total_score DESC, rank)
  WHERE is_final = true;

-- Pengecekan blacklist cepat
CREATE INDEX idx_users_blacklist ON users(is_blacklisted) WHERE is_blacklisted = true;

-- Antrian verifikasi per program
CREATE INDEX idx_applications_verif_queue
  ON applications(scholarship_id, status)
  WHERE status IN ('submitted', 'under_review', 'needs_revision');

-- Lookup verifikator per program
CREATE INDEX idx_scholarship_verifiers ON scholarship_verifiers(scholarship_id, user_id);
```

---

## 10. Validasi Bisnis Kritis

| Kode | Aturan | Layer |
|---|---|---|
| **BV-01** | Satu NIK hanya boleh mendaftar satu kali per program per periode (kecuali draft) | DB unique index + service |
| **BV-02** | Pendaftar dengan `is_blacklisted = true` tidak dapat mendaftar ke program manapun | Gate/Policy + service |
| **BV-03** | Verifikator hanya dapat mengakses pendaftar dari program yang ditugaskan kepadanya | Gate/Policy: `scholarship_verifiers` |
| **BV-04** | Konfigurasi qualification tidak dapat diubah setelah ada pendaftar masuk (`status != draft`) | Service layer guard |
| **BV-05** | Range `numeric_range` tidak boleh overlap | Admin form validation + ScoringEngine |
| **BV-06** | Skor hanya menjadi `is_final = true` setelah **semua** dokumen wajib disetujui verifikator | ApplicationObserver |
| **BV-07** | Skor final yang sudah di-approve Approver tidak dapat diubah oleh siapapun | Model guard + DB-level |
| **BV-08** | Hanya pendaftar dengan `is_final = true` yang diproses dalam batch ranking | `ProcessBatchScoring` Job guard |
| **BV-09** | Slot renewal diproses dan dikunci sebelum ranking pendaftar baru dimulai | `ProcessBatchScoring` Job sequence |
| **BV-10** | `quota_renewal_locked` tidak boleh melebihi `quota_primary` | Service validation |
| **BV-11** | File upload maksimum 2 MB, format JPG/PNG/PDF | Livewire client-side + Laravel server-side |
| **BV-12** | `verification_logs` dan `blacklist_logs` immutable — tidak ada DELETE | Observer guard + DB privilege |
| **BV-13** | Blacklist hanya dapat dilakukan oleh verifikator yang ditugaskan pada program tersebut | Gate/Policy |
| **BV-14** | Pencabutan blacklist hanya dapat dilakukan oleh Admin atau Super Admin | Gate/Policy |

---

## 11. Arsitektur Teknis

### 11.1 Tech Stack

| Layer | Pilihan | Keterangan |
|---|---|---|
| **Framework** | Laravel 13 (PHP 8.3+) | Backbone monolith |
| **Frontend Reaktif** | Livewire v4 | Full-stack reactive, AJAX otomatis, validasi real-time |
| **UI Layer** | Custom Tailwind CSS v4 Components (shadcn-inspired) | 26 reusable Blade components di `components/ui/`, semantic color tokens, OKLCH |
| **Icons** | Lucide Icons (blade-lucide-icons) | 1700+ SVG icons via Blade component `<x-lucide-* />` |
| **JS Ringan** | Alpine.js (bundled Livewire) | Toggle, chart init, client-side state |
| **Database** | PostgreSQL 16 | Window function ranking, JSONB konfigurasi, partial index |
| **Cache / Queue** | Redis | Batch scoring job, session, OTP TTL, rate limiting |
| **File Storage** | MinIO (self-hosted S3-compatible) | Dokumen pendaftar, akses via signed URL |
| **Notifikasi** | Fonnte (WhatsApp) + SMTP (Email) | Laravel Notification + custom channel |
| **Export** | Laravel Excel + DomPDF | Excel & PDF laporan |
| **Auth** | Laravel Fortify + Spatie Permission | Session-based, RBAC multi-role |
| **Testing** | Pest PHP | Unit (ScoringEngine, RenewalEngine), Feature (Livewire), Integration |

### 11.2 Struktur Direktori

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Public/
│   │   │   └── AnnouncementController.php
│   │   └── Export/
│   │       ├── ExportApplicantsController.php
│   │       └── ExportDisbursementController.php
│   └── Middleware/
│       └── EnsureNotBlacklisted.php    -- cek blacklist sebelum akses form daftar
│
├── Livewire/
│   ├── Applicant/
│   │   ├── ApplicationForm.php         -- form pendaftaran dinamis (stepper)
│   │   ├── ApplicationStatus.php       -- tracker status + breakdown skor
│   │   ├── DocumentRevision.php        -- re-upload dokumen yang ditolak
│   │   ├── BankAccountForm.php         -- konfirmasi rekening penerima
│   │   └── SemesterRenewal.php         -- form renewal dengan upload transkrip
│   ├── Verifier/
│   │   ├── VerificationQueue.php       -- antrian per program yang ditugaskan
│   │   └── ApplicationDetail.php       -- detail + form verifikasi + koreksi + blacklist
│   ├── Admin/
│   │   ├── ScholarshipManager.php      -- CRUD program + konfigurasi renewal
│   │   ├── QualificationBuilder.php    -- builder indikator + opsi + range + skor
│   │   ├── TiebreakerConfigurator.php  -- drag-drop urutan tie-breaker
│   │   ├── VerifierAssignment.php      -- tugaskan verifikator ke program
│   │   ├── NotificationConfigurator.php
│   │   ├── ApplicationList.php         -- tabel pendaftar + filter
│   │   ├── BatchSelectionRunner.php    -- trigger batch + progress realtime
│   │   ├── SelectionResult.php         -- review hasil ranking sebelum penetapan
│   │   ├── BlacklistManager.php        -- lihat & cabut blacklist
│   │   └── UserManager.php
│   ├── Approver/
│   │   ├── ApproverDashboard.php
│   │   └── RecipientApproval.php       -- final approval penetapan penerima
│   ├── Treasurer/
│   │   └── DisbursementList.php        -- update status pencairan + export
│   └── Dashboard/
│       ├── AdminDashboard.php
│       └── ApproverDashboard.php
│
├── Actions/
│   ├── Scholarship/
│   │   ├── CreateScholarship.php
│   │   ├── PublishScholarship.php
│   │   └── FinalizeRenewalSlots.php    -- hitung & kunci quota_renewal_locked
│   ├── Application/
│   │   ├── SubmitApplication.php
│   │   └── SnapshotApplicantProfile.php
│   ├── Scoring/
│   │   ├── CalculateApplicationScore.php
│   │   ├── RunBatchRanking.php
│   │   └── ApplyTieBreaker.php
│   ├── Verification/
│   │   ├── ApproveDocument.php
│   │   ├── RejectDocument.php
│   │   ├── CorrectAnswer.php
│   │   └── FinalizeApplicantScore.php  -- trigger setelah semua dok approved
│   └── Blacklist/
│       ├── BlacklistApplicant.php
│       └── RevokeBlacklist.php
│
├── Services/
│   ├── ScoringEngine.php               -- pure PHP, fully testable
│   ├── RenewalEngine.php               -- kalkulasi slot renewal
│   ├── DynamicFormRenderer.php         -- generate form config dari Qualification
│   └── NotificationService.php
│
├── Jobs/
│   ├── ProcessBatchScoring.php         -- ranking + tie-breaker via Queue
│   ├── SendNotification.php            -- notifikasi WA/Email async
│   └── AutoManageScholarshipStatus.php -- scheduled: buka/tutup otomatis
│
├── Models/
│   ├── User.php
│   ├── Scholarship.php
│   ├── ScholarshipVerifier.php         -- pivot model
│   ├── QualificationGroup.php
│   ├── Qualification.php
│   ├── QualificationOption.php
│   ├── QualificationRange.php
│   ├── Application.php
│   ├── ApplicationAnswer.php
│   ├── ApplicationDocument.php
│   ├── ApplicationScore.php
│   ├── VerificationLog.php             -- readonly model (no update/delete)
│   ├── BlacklistLog.php                -- append-only model
│   ├── Disbursement.php
│   └── NotificationLog.php
│
├── Observers/
│   ├── ApplicationDocumentObserver.php -- trigger FinalizeApplicantScore saat semua dok approved
│   └── BlacklistLogObserver.php        -- sync users.is_blacklisted
│
└── Policies/
    ├── ApplicationPolicy.php           -- pastikan verifikator hanya akses program yang ditugaskan
    └── BlacklistPolicy.php             -- blacklist: verifikator, revoke: admin/superadmin

resources/
├── views/
│   ├── components/
│   │   ├── layouts/
│   │   │   ├── app.blade.php               -- layout internal: desktop sidebar + mobile drawer
│   │   │   └── public.blade.php            -- layout publik: sticky header
│   │   └── ui/                             -- DESIGN SYSTEM COMPONENTS (26 files)
│   │       ├── button.blade.php            -- 6 variants, 5 sizes
│   │       ├── input.blade.php             -- label + error + disabled
│   │       ├── textarea.blade.php
│   │       ├── select.blade.php
│   │       ├── checkbox.blade.php
│   │       ├── radio.blade.php
│   │       ├── toggle.blade.php            -- Alpine.js powered
│   │       ├── badge.blade.php             -- 6 variants
│   │       ├── icon.blade.php              -- Lucide wrapper
│   │       ├── card.blade.php              -- 4 padding sizes
│   │       ├── table.blade.php             -- + th/td/tr sub-komponen
│   │       ├── pagination.blade.php
│   │       ├── alert.blade.php             -- 5 variants + dismissible
│   │       ├── empty-state.blade.php
│   │       ├── loading.blade.php
│   │       ├── form-group.blade.php
│   │       ├── modal.blade.php             -- Alpine.js + backdrop blur
│   │       ├── dropdown.blade.php          -- + dropdown-item
│   │       ├── tabs.blade.php              -- + tab-trigger + tab-content
│   │       ├── drawer.blade.php            -- mobile sidebar sheet
│   │       ├── sidebar.blade.php           -- + sidebar-item + sidebar-section
│   │       ├── qualification-field.blade.php
│   │       ├── score-breakdown.blade.php
│   │       ├── document-uploader.blade.php
│   │       ├── status-badge.blade.php
│   │       └── renewal-slot-summary.blade.php
│   └── livewire/
└── css/
    └── app.css                               -- Design tokens · semantic colors · Inter font · animations
```

### 11.3 ScoringEngine — Desain

```php
// app/Services/ScoringEngine.php

final class ScoringEngine
{
    /**
     * Hitung skor untuk satu aplikasi.
     * Tidak ada dependency ke framework — fully unit testable.
     */
    public function calculate(Application $application): ScoreResult
    {
        $breakdown = [];
        $total     = 0;

        foreach ($application->scholarship->qualifications as $qualification) {
            $answer = $application->answers
                ->firstWhere('qualification_id', $qualification->id);

            $score = $this->resolveScore($qualification, $answer);

            $breakdown[$qualification->id] = [
                'name'         => $qualification->name,
                'answer_label' => $this->resolveLabel($qualification, $answer),
                'score'        => $score,
            ];

            $total += $score;
        }

        return new ScoreResult(
            total:    $total,
            max:      $this->calculateMax($application->scholarship),
            breakdown: $breakdown,
        );
    }

    private function resolveScore(Qualification $q, ?ApplicationAnswer $answer): int
    {
        return match ($q->type) {
            'single_choice'  => $answer?->selectedOption?->value ?? 0,
            'multi_choice'   => $answer?->selectedOptions->max('value') ?? 0,
            'numeric_range'  => $this->resolveRangeScore($q, $answer?->numeric_value),
            'file_upload',
            'text'           => 0,
        };
    }

    private function resolveRangeScore(Qualification $q, ?float $value): int
    {
        if ($value === null) return 0;

        return $q->ranges
            ->first(fn($r) => $value >= $r->range_min && $value <= $r->range_max)
            ?->value ?? 0;
    }

    private function calculateMax(Scholarship $scholarship): int
    {
        return $scholarship->qualifications->sum(function ($q) {
            return match ($q->type) {
                'single_choice'  => $q->options->max('value') ?? 0,
                'multi_choice'   => $q->options->max('value') ?? 0,
                'numeric_range'  => $q->ranges->max('value') ?? 0,
                default          => 0,
            };
        });
    }
}
```

### 11.4 RenewalEngine — Desain

```php
// app/Services/RenewalEngine.php

final class RenewalEngine
{
    /**
     * Hitung slot renewal: berapa penerima aktif dari predecessor
     * yang memenuhi syarat untuk masuk periode baru.
     */
    public function calculateRenewalSlots(Scholarship $newScholarship): RenewalSlotResult
    {
        $predecessor = $newScholarship->predecessor;

        if (!$predecessor) {
            return RenewalSlotResult::empty();
        }

        // Ambil semua penerima aktif dari predecessor
        $activeRecipients = Application::query()
            ->where('scholarship_id', $predecessor->id)
            ->where('status', 'selected')
            ->whereHas('score', fn($q) => $q->where('selection_result', 'utama'))
            ->get();

        // Filter yang sudah submit renewal ke program baru
        $renewalApplications = Application::query()
            ->where('scholarship_id', $newScholarship->id)
            ->where('is_renewal', true)
            ->whereIn('previous_application_id', $activeRecipients->pluck('id'))
            ->get();

        $eligibleRenewals = $renewalApplications->filter(
            fn($app) => $app->score?->is_final
                && $app->snapshot_profile['gpa'] >= $newScholarship->min_gpa_renewal
        );

        return new RenewalSlotResult(
            totalActiveRecipients:  $activeRecipients->count(),
            totalSubmittedRenewal:  $renewalApplications->count(),
            eligibleForRenewal:     $eligibleRenewals->count(),
            remainingForNew:        $newScholarship->quota_primary - $eligibleRenewals->count(),
        );
    }
}
```

### 11.5 ProcessBatchScoring Job — Urutan Eksekusi

```
ProcessBatchScoring::handle()
  │
  ├── 1. Guard: hanya jalankan jika scholarship.status = 'closed'
  │
  ├── 2. RENEWAL PHASE (jika ada predecessor)
  │     ├── Ambil semua renewal application yang is_final = true dan eligible
  │     ├── Set selection_result = 'utama' untuk semua yang eligible
  │     ├── Update scholarship.quota_renewal_locked
  │     └── Hitung sisa kuota: available = quota_primary - quota_renewal_locked
  │
  ├── 3. RANKING PHASE (pendaftar baru)
  │     ├── Ambil semua non-renewal application yang is_final = true
  │     ├── Sort DESC by total_score
  │     ├── Terapkan tie-breaker per konfigurasi (log setiap langkah)
  │     ├── Set rank per pendaftar
  │     └── Set selection_result:
  │           - rank <= available                           → utama
  │           - rank <= available + quota_reserve           → cadangan
  │           - rank > available + quota_reserve            → tidak_lolos
  │
  └── 4. FINALIZE
        ├── Update scholarship.status = 'selecting' (menunggu approval Approver)
        └── Dispatch event BatchRankingCompleted (untuk notifikasi admin)
```

### 11.6 Design System — Custom UI Components (shadcn-inspired)

| Komponen | Digunakan Pada |
|---|---|
| Komponen `x-ui.*` | Digunakan Pada |
|---|---|
| **button** | Semua aksi (6 variants: default/destructive/outline/secondary/ghost/link, 5 sizes) |
| **input** | Semua form input text/email/number/date |
| **textarea** | Deskripsi, catatan |
| **select** | Dropdown pilihan (status, program, role) |
| **checkbox** | Pilihan boolean |
| **radio** | Pilihan tunggal dari beberapa opsi |
| **toggle** | Switch on/off (Alpine.js) |
| **badge** | Status aplikasi, hasil seleksi, role, blacklist (6 variants) |
| **card** | Semua panel/pembungkus konten (4 padding sizes) |
| **table** + **th/td/tr** | Antrian verifikasi, daftar pendaftar, daftar penerima, log blacklist |
| **pagination** | Navigasi halaman pada semua tabel data |
| **alert** | Notifikasi in-app sukses/error/warning (5 variants + dismissible) |
| **empty-state** | Placeholder saat data kosong |
| **loading** | Spinner loading state |
| **form-group** | Pembungkus form field dengan label + error + description |
| **modal** | Konfirmasi aksi kritis (Alpine.js + backdrop blur) |
| **dropdown** | Menu konteks dropdown |
| **tabs** | Detail pendaftar (Profil / Jawaban / Dokumen / Skor / Log Verifikasi) |
| **drawer** | Mobile sidebar sheet (hamburger menu) |
| **sidebar** + **sidebar-item** + **sidebar-section** | Navigasi utama per role |
| **icon** | Wrapper Lucide Icons (`<x-lucide-* />` via blade-lucide-icons) |

---

## 12. Routing

### Publik (tanpa auth)
```
GET  /                                         → Landing, info platform, daftar program aktif
GET  /daftar                                   → Registrasi akun pendaftar
GET  /login
GET  /pengumuman/{scholarship:slug}            → Hasil seleksi publik per program
GET  /pengumuman/{scholarship:slug}/{reg_no}   → Detail hasil per pendaftar
```

### Pendaftar (role: applicant)
```
GET  /dashboard                                → Ringkasan semua pendaftaran aktif
GET  /beasiswa                                 → Daftar program yang sedang buka
GET  /beasiswa/{scholarship:slug}/daftar       → Livewire: ApplicationForm
GET  /pendaftaran/{application}/status         → Livewire: ApplicationStatus
GET  /pendaftaran/{application}/revisi         → Livewire: DocumentRevision
GET  /pendaftaran/{application}/rekening       → Livewire: BankAccountForm
GET  /renewal/{application}                    → Livewire: SemesterRenewal
```

### Verifikator (role: verifier — hanya program yang ditugaskan)
```
GET  /verifikasi                               → Livewire: VerificationQueue (filter per program)
GET  /verifikasi/{application}                 → Livewire: ApplicationDetail
```

### Admin (role: admin)
```
GET  /admin/dashboard                          → Livewire: AdminDashboard
GET  /admin/beasiswa                           → Livewire: ScholarshipManager (list)
GET  /admin/beasiswa/buat                      → Livewire: ScholarshipManager (create)
GET  /admin/beasiswa/{scholarship}/edit        → Livewire: ScholarshipManager (edit)
GET  /admin/beasiswa/{scholarship}/qualification   → Livewire: QualificationBuilder
GET  /admin/beasiswa/{scholarship}/tiebreaker     → Livewire: TiebreakerConfigurator
GET  /admin/beasiswa/{scholarship}/verifikator    → Livewire: VerifierAssignment
GET  /admin/beasiswa/{scholarship}/notifikasi     → Livewire: NotificationConfigurator
GET  /admin/beasiswa/{scholarship}/pendaftar      → Livewire: ApplicationList
GET  /admin/beasiswa/{scholarship}/seleksi        → Livewire: BatchSelectionRunner
GET  /admin/beasiswa/{scholarship}/hasil          → Livewire: SelectionResult
GET  /admin/blacklist                          → Livewire: BlacklistManager
GET  /admin/pengguna                           → Livewire: UserManager
GET  /admin/export/pendaftar/{scholarship}     → Controller: ExportApplicants
GET  /admin/export/pencairan/{scholarship}     → Controller: ExportDisbursement
```

### Approver (role: approver)
```
GET  /approver/dashboard                       → Livewire: ApproverDashboard
GET  /approver/penetapan/{scholarship}         → Livewire: RecipientApproval
```

### Bendahara (role: treasurer)
```
GET  /keuangan/pencairan                       → Livewire: DisbursementList
GET  /keuangan/export/{scholarship}            → Controller: ExportDisbursement
```

---

## 13. Alur Status Aplikasi

```
DRAFT
  ↓ (submit)
SUBMITTED
  ↓ (verifikasi aktif → antrian verifikator)
UNDER_REVIEW ──────────────────────────────────────────┐
  ↓ (verifikator: request re-upload)                   │
NEEDS_REVISION                                         │
  ↓ (pendaftar re-upload dokumen)                      │
UNDER_REVIEW (ulang) ──────────────────────────────────┘
  ↓ (semua dokumen wajib approved → is_final = true)
VERIFIED
  ↓ (batch ranking + approval Approver)
SELECTED (selection_result: utama / cadangan)
  atau
REJECTED

SELECTED ──→ [periode berikutnya] renewal via SUBMITTED (is_renewal = true)
```

**Transisi blacklist** (orthogonal terhadap status di atas):
```
UNDER_REVIEW | SUBMITTED
  ↓ (verifikator temukan pelanggaran)
[blacklist_logs dibuat]
users.is_blacklisted = true
application.status = REJECTED
```

Setiap transisi status men-dispatch event `ApplicationStatusChanged` yang di-listen oleh `NotificationService`.

---

## 14. Pertimbangan Implementasi

### 14.1 Dynamic Form Generation di Livewire

```php
// app/Livewire/Applicant/ApplicationForm.php

class ApplicationForm extends Component
{
    public Scholarship $scholarship;
    public array $answers = [];   // [qualification_id => value/option_id/etc]
    public array $files   = [];   // [qualification_id => TemporaryUploadedFile]
    public int $currentStep = 1;

    #[Rule] public array $validatedAnswers = [];

    public function mount(Scholarship $scholarship): void
    {
        // Cegah pendaftar yang diblacklist
        abort_if(auth()->user()->is_blacklisted, 403, 'Akun Anda tidak dapat mendaftar.');

        $this->scholarship->load([
            'qualificationGroups.qualifications.options',
            'qualificationGroups.qualifications.ranges',
        ]);
    }

    public function nextStep(): void
    {
        $this->validateCurrentStep();
        $this->currentStep++;
    }

    public function submit(): void
    {
        $this->validate();
        app(SubmitApplication::class)->execute($this->scholarship, auth()->user(), $this->answers, $this->files);
    }
}
```

Blade partial `qualification-field.blade.php` meng-handle render per tipe secara polimorfis:

```blade
@switch($qualification->type)
    @case('single_choice')
        {{-- Custom Radio Group (shadcn-inspired) --}}
        @break
    @case('numeric_range')
        {{-- Input number + range hint --}}
        @break
    @case('file_upload')
        {{-- Custom File Drop Zone, validasi 2MB client-side via Alpine --}}
        @break
    @case('text')
        {{-- Textarea --}}
        @break
@endswitch
```

### 14.2 Validasi Upload 2 MB

```javascript
// resources/views/components/document-uploader.blade.php (Alpine.js)
x-data="{
    validateFile(event) {
        const file = event.target.files[0];
        const maxSize = 2 * 1024 * 1024; // 2MB
        const allowed = ['image/jpeg', 'image/png', 'application/pdf'];

        if (file.size > maxSize) {
            this.error = 'Ukuran file maksimal 2 MB.';
            event.target.value = '';
            return;
        }
        if (!allowed.includes(file.type)) {
            this.error = 'Format file harus JPG, PNG, atau PDF.';
            event.target.value = '';
            return;
        }
        this.error = null;
        // Livewire upload
        @this.upload('files.{{ $qualification->id }}', file, ...);
    }
}"
```

Server-side (Laravel rule):
```php
'files.*' => ['file', 'max:2048', 'mimes:jpg,jpeg,png,pdf'],
```

### 14.3 Pola Livewire yang Digunakan

- **`#[Lazy]`** — Dashboard berat (chart, tabel statistik) tidak blocking render awal.
- **`wire:navigate`** — SPA-like navigation tanpa full page reload.
- **Form Objects** (`Livewire\Form`) — Satu Form Object per step di `ApplicationForm`.
- **`$dispatch` / `$on`** — Event bus: `VerificationQueue` refresh setelah `ApplicationDetail` update status.
- **Polling terbatas** — Hanya pada `BatchSelectionRunner` untuk progress indicator; dihentikan setelah job selesai (`wire:poll.stop`).
- **`wire:confirm`** — Konfirmasi inline sebelum aksi destruktif (blacklist, approve penetapan final).

---

## 15. Milestone Pengembangan

### Fase 0 — UI Refactor (pre-Fase 1) ✅ Selesai
- Setup design system: 14 semantic color tokens (shadcn-inspired OKLCH), font Inter, dark mode
- 26 reusable Blade components di `resources/views/components/ui/` (button, input, card, table, modal, dsb)
- Lucide Icons via blade-lucide-icons
- Mobile sidebar dengan hamburger menu + drawer/sheet component
- Animasi transisi halus pada semua komponen interaktif
- Flowbite dihapus sepenuhnya dari dependency

### Fase 1 — Foundation & Engine (Bulan 1–2) ✅ Selesai
- Setup: Laravel 13, Livewire v4, Custom UI Components, PostgreSQL, Redis, MinIO
- Auth: Laravel Fortify, Spatie Permission v8, role seeding (6 roles)
- Layout Blade: sidebar per role dengan design system, responsive mobile
- **ScoringEngine** (pure PHP, fully unit tested dengan Pest)
- **RenewalEngine** (kalkulasi slot, fully unit tested)
- ScholarshipManager + QualificationBuilder + TiebreakerConfigurator Livewire
- VerifierAssignment Livewire
- Middleware `EnsureNotBlacklisted`

### Fase 2 — Pendaftaran & Upload (Bulan 2–3)
- OTP verification (WA + Email)
- `ApplicationForm` Livewire (dynamic form dari Qualification config)
- File upload ke MinIO via Livewire `WithFileUploads`, validasi 2 MB
- Skor sementara real-time di step Review
- Dashboard pendaftar + ApplicationStatus
- Seeder BBK Madiun sebagai data contoh program

### Fase 3 — Verifikasi & Seleksi (Bulan 3–4)
- `VerificationQueue` + `ApplicationDetail` Livewire
- Koreksi jawaban verifikator + `verification_logs`
- `ApplicationDocumentObserver` → trigger `FinalizeApplicantScore`
- `BlacklistManager` + `BlacklistApplicant` / `RevokeBlacklist` Actions
- `BatchSelectionRunner` (renewal phase + ranking phase) + `SelectionResult`
- `RecipientApproval` Approver
- Notifikasi WA (Fonnte) + Email (SMTP) via Queue
- Halaman pengumuman publik

### Fase 4 — Operasional & Laporan (Bulan 4–5)
- Modul pencairan: `DisbursementList`, export rekening Excel
- Modul renewal semester (SemesterRenewal Livewire + RenewalEngine)
- Dashboard Admin + Approver (chart Chart.js via Alpine)
- Export PDF laporan (DomPDF)
- Audit log viewer (VerificationLog + BlacklistLog)
- Scheduled job: `AutoManageScholarshipStatus`

### Fase 5 — Integrasi & Skalabilitas (Opsional)
- Integrasi API PDDikti (validasi status mahasiswa aktif)
- Integrasi data DTKS (validasi kategori kemiskinan)
- Multi-tenancy: `organization_id` + subdomain routing
- Modul banding formal

---

## 16. Keputusan Desain yang Perlu Dicatat

Beberapa keputusan desain penting yang dibuat berdasarkan jawaban stakeholder, untuk referensi tim development:

| Keputusan | Alasan |
|---|---|
| Verifikator scoped per program via `scholarship_verifiers` | Mencegah akses lintas program yang tidak diinginkan; satu verifikator bisa ditugaskan ke banyak program tapi tidak otomatis |
| Slot renewal dipotong dari `quota_primary` program baru, bukan kuota terpisah | Lebih natural — "100 kuota BBK 2025/2026, 50 sudah diambil penerima lama, 50 sisa untuk baru" |
| `is_blacklisted` di `users` sebagai cache flag | Untuk pengecekan cepat tanpa query ke `blacklist_logs`; sumber kebenaran tetap di log |
| Skor final hanya setelah semua dokumen approved, meski verifikasi dinonaktifkan | Tidak ada mode "langsung final" — selalu butuh satu titik konfirmasi untuk integritas data |
| File upload 2 MB divalidasi di client-side (Alpine) DAN server-side (Laravel) | Defense in depth; client-side untuk UX cepat, server-side untuk keamanan |
| `verification_logs` dan `blacklist_logs` immutable (no DELETE privilege di production) | Akuntabilitas tidak boleh bisa dihapus oleh siapapun, termasuk admin |
| Skor final yang sudah di-approve Approver dikunci di level model | Setelah penetapan, tidak ada celah untuk manipulasi data penerima |
