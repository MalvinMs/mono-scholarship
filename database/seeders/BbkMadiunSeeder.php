<?php

namespace Database\Seeders;

use App\Models\Qualification;
use App\Models\QualificationGroup;
use App\Models\QualificationOption;
use App\Models\QualificationRange;
use App\Models\Scholarship;
use App\Models\ScholarshipVerifier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BbkMadiunSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================================
        // Create users
        // ============================================================
        $admin = User::firstOrCreate(
            ['email' => 'admin@madiun.test'],
            [
                'name' => 'Admin BBK Madiun',
                'nik' => '3519000000000001',
                'phone' => '081111111111',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        $verifier1 = User::firstOrCreate(
            ['email' => 'verifier1@madiun.test'],
            [
                'name' => 'Verifikator 1',
                'nik' => '3519000000000002',
                'phone' => '081111111112',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]
        );
        $verifier1->assignRole('verifier');

        $verifier2 = User::firstOrCreate(
            ['email' => 'verifier2@madiun.test'],
            [
                'name' => 'Verifikator 2',
                'nik' => '3519000000000003',
                'phone' => '081111111113',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]
        );
        $verifier2->assignRole('verifier');

        $approver = User::firstOrCreate(
            ['email' => 'approver@madiun.test'],
            [
                'name' => 'Kepala Dinas',
                'nik' => '3519000000000004',
                'phone' => '081111111114',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]
        );
        $approver->assignRole('approver');

        $treasurer = User::firstOrCreate(
            ['email' => 'bendahara@madiun.test'],
            [
                'name' => 'Bendahara',
                'nik' => '3519000000000005',
                'phone' => '081111111115',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]
        );
        $treasurer->assignRole('treasurer');

        // 10 applicants
        $applicants = [];
        for ($i = 1; $i <= 10; $i++) {
            $applicants[] = User::firstOrCreate(
                ['email' => "pendaftar{$i}@madiun.test"],
                [
                    'name' => "Pendaftar {$i}",
                    'nik' => '35190000000000' . sprintf('%02d', $i + 5),
                    'phone' => '0811111111' . sprintf('%02d', $i + 15),
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                    'phone_verified_at' => now(),
                    'birth_date' => '2000-01-' . sprintf('%02d', $i),
                    'birth_place' => 'Madiun',
                    'address' => "Jl. Raya Madiun No. {$i}",
                    'village' => 'Desa ' . $i,
                    'district' => 'Kecamatan ' . $i,
                    'city' => 'Kabupaten Madiun',
                    'province' => 'Jawa Timur',
                    'education_level' => $i <= 5 ? 'SMA' : 'S1',
                ]
            );
            $applicants[$i - 1]->assignRole('applicant');
        }

        // ============================================================
        // BBK 2024/2025 Scholarship
        // ============================================================
        $bbk2024 = Scholarship::firstOrCreate(
            ['slug' => 'bbk-2024-2025'],
            [
                'name' => 'BBK Kabupaten Madiun 2024/2025',
                'description' => 'Program Bantuan Beasiswa Kuliah untuk mahasiswa Kabupaten Madiun tahun akademik 2024/2025.',
                'academic_year' => '2024/2025',
                'fund_amount' => 6000000,
                'quota_primary' => 50,
                'quota_reserve' => 10,
                'date_start' => '2024-06-01',
                'date_end' => '2024-07-31',
                'status' => 'announced',
                'is_verification_enabled' => true,
                'otp_channel' => 'whatsapp',
                'min_gpa_renewal' => 3.50,
                'scoring_display_mode' => 'absolute',
                'created_by' => $admin->id,
            ]
        );

        // ============================================================
        // BBK 2025/2026 Scholarship (renewal from 2024/2025)
        // ============================================================
        $bbk2025 = Scholarship::firstOrCreate(
            ['slug' => 'bbk-2025-2026'],
            [
                'name' => 'BBK Kabupaten Madiun 2025/2026',
                'description' => 'Program Bantuan Beasiswa Kuliah lanjutan untuk mahasiswa Kabupaten Madiun tahun akademik 2025/2026.',
                'academic_year' => '2025/2026',
                'fund_amount' => 6000000,
                'quota_primary' => 100,
                'quota_reserve' => 15,
                'predecessor_scholarship_id' => $bbk2024->id,
                'date_start' => '2025-06-01',
                'date_end' => '2025-08-15',
                'status' => 'open',
                'is_verification_enabled' => true,
                'otp_channel' => 'whatsapp',
                'min_gpa_renewal' => 3.50,
                'scoring_display_mode' => 'absolute',
                'created_by' => $admin->id,
            ]
        );

        // ============================================================
        // Assign verifiers
        // ============================================================
        ScholarshipVerifier::firstOrCreate([
            'scholarship_id' => $bbk2025->id,
            'user_id' => $verifier1->id,
        ], [
            'assigned_by' => $admin->id,
            'assigned_at' => now(),
        ]);
        ScholarshipVerifier::firstOrCreate([
            'scholarship_id' => $bbk2025->id,
            'user_id' => $verifier2->id,
        ], [
            'assigned_by' => $admin->id,
            'assigned_at' => now(),
        ]);

        // ============================================================
        // Qualification: 7 indikator kemiskinan + prestasi
        // ============================================================
        $group1 = QualificationGroup::firstOrCreate(
            ['scholarship_id' => $bbk2025->id, 'name' => 'Indikator Kemiskinan'],
            ['sort_order' => 1]
        );

        $group2 = QualificationGroup::firstOrCreate(
            ['scholarship_id' => $bbk2025->id, 'name' => 'Indikator Pendidikan & Prestasi'],
            ['sort_order' => 2]
        );

        // Indikator 1: Kepemilikan Rumah (single_choice)
        $q1 = Qualification::firstOrCreate(
            ['scholarship_id' => $bbk2025->id, 'name' => 'Status Kepemilikan Rumah'],
            [
                'qualification_group_id' => $group1->id,
                'type' => 'single_choice',
                'is_required' => true,
                'is_file_upload_required' => true,
                'file_upload_label' => 'Upload Bukti Kepemilikan / Sewa Rumah',
                'sort_order' => 1,
            ]
        );
        QualificationOption::firstOrCreate(['qualification_id' => $q1->id, 'label' => 'Milik Sendiri', 'value' => 10, 'sort_order' => 1]);
        QualificationOption::firstOrCreate(['qualification_id' => $q1->id, 'label' => 'Sewa / Kontrak', 'value' => 20, 'sort_order' => 2]);
        QualificationOption::firstOrCreate(['qualification_id' => $q1->id, 'label' => 'Menumpang', 'value' => 30, 'sort_order' => 3]);

        // Indikator 2: Pekerjaan Orang Tua (single_choice)
        $q2 = Qualification::firstOrCreate(
            ['scholarship_id' => $bbk2025->id, 'name' => 'Pekerjaan Orang Tua / Wali'],
            [
                'qualification_group_id' => $group1->id,
                'type' => 'single_choice',
                'is_required' => true,
                'is_file_upload_required' => true,
                'file_upload_label' => 'Upload Surat Keterangan Pekerjaan',
                'sort_order' => 2,
            ]
        );
        QualificationOption::firstOrCreate(['qualification_id' => $q2->id, 'label' => 'PNS / TNI / Polri', 'value' => 0, 'sort_order' => 1]);
        QualificationOption::firstOrCreate(['qualification_id' => $q2->id, 'label' => 'Pegawai Swasta', 'value' => 5, 'sort_order' => 2]);
        QualificationOption::firstOrCreate(['qualification_id' => $q2->id, 'label' => 'Wiraswasta', 'value' => 10, 'sort_order' => 3]);
        QualificationOption::firstOrCreate(['qualification_id' => $q2->id, 'label' => 'Buruh / Petani', 'value' => 20, 'sort_order' => 4]);
        QualificationOption::firstOrCreate(['qualification_id' => $q2->id, 'label' => 'Tidak Bekerja', 'value' => 30, 'sort_order' => 5]);

        // Indikator 3: Penghasilan Orang Tua (numeric_range)
        $q3 = Qualification::firstOrCreate(
            ['scholarship_id' => $bbk2025->id, 'name' => 'Penghasilan Orang Tua per Bulan (Rp)'],
            [
                'qualification_group_id' => $group1->id,
                'type' => 'numeric_range',
                'is_required' => true,
                'is_file_upload_required' => true,
                'file_upload_label' => 'Upload Slip Gaji / Surat Keterangan Penghasilan',
                'sort_order' => 3,
            ]
        );
        QualificationRange::firstOrCreate(['qualification_id' => $q3->id, 'range_min' => 0, 'range_max' => 500000, 'value' => 30, 'label' => 'Sangat Rendah', 'sort_order' => 1]);
        QualificationRange::firstOrCreate(['qualification_id' => $q3->id, 'range_min' => 500001, 'range_max' => 1500000, 'value' => 20, 'label' => 'Rendah', 'sort_order' => 2]);
        QualificationRange::firstOrCreate(['qualification_id' => $q3->id, 'range_min' => 1500001, 'range_max' => 3000000, 'value' => 10, 'label' => 'Menengah', 'sort_order' => 3]);
        QualificationRange::firstOrCreate(['qualification_id' => $q3->id, 'range_min' => 3000001, 'range_max' => 99999999, 'value' => 0, 'label' => 'Tinggi', 'sort_order' => 4]);

        // Indikator 4: Jumlah Tanggungan (numeric_range)
        $q4 = Qualification::firstOrCreate(
            ['scholarship_id' => $bbk2025->id, 'name' => 'Jumlah Tanggungan Keluarga'],
            [
                'qualification_group_id' => $group1->id,
                'type' => 'numeric_range',
                'is_required' => true,
                'is_file_upload_required' => true,
                'file_upload_label' => 'Upload Kartu Keluarga',
                'sort_order' => 4,
            ]
        );
        QualificationRange::firstOrCreate(['qualification_id' => $q4->id, 'range_min' => 0, 'range_max' => 2, 'value' => 0, 'label' => '≤ 2 orang', 'sort_order' => 1]);
        QualificationRange::firstOrCreate(['qualification_id' => $q4->id, 'range_min' => 3, 'range_max' => 4, 'value' => 10, 'label' => '3-4 orang', 'sort_order' => 2]);
        QualificationRange::firstOrCreate(['qualification_id' => $q4->id, 'range_min' => 5, 'range_max' => 99, 'value' => 20, 'label' => '≥ 5 orang', 'sort_order' => 3]);

        // Indikator 5: Sumber Air (single_choice)
        $q5 = Qualification::firstOrCreate(
            ['scholarship_id' => $bbk2025->id, 'name' => 'Sumber Air Bersih'],
            [
                'qualification_group_id' => $group1->id,
                'type' => 'single_choice',
                'is_required' => true,
                'is_file_upload_required' => false,
                'sort_order' => 5,
            ]
        );
        QualificationOption::firstOrCreate(['qualification_id' => $q5->id, 'label' => 'PDAM', 'value' => 0, 'sort_order' => 1]);
        QualificationOption::firstOrCreate(['qualification_id' => $q5->id, 'label' => 'Sumur / Mata Air', 'value' => 10, 'sort_order' => 2]);

        // Indikator 6: Daya Listrik (single_choice)
        $q6 = Qualification::firstOrCreate(
            ['scholarship_id' => $bbk2025->id, 'name' => 'Daya Listrik Rumah'],
            [
                'qualification_group_id' => $group1->id,
                'type' => 'single_choice',
                'is_required' => true,
                'is_file_upload_required' => true,
                'file_upload_label' => 'Upload Foto Meteran / Tagihan Listrik',
                'sort_order' => 6,
            ]
        );
        QualificationOption::firstOrCreate(['qualification_id' => $q6->id, 'label' => '> 900 VA', 'value' => 0, 'sort_order' => 1]);
        QualificationOption::firstOrCreate(['qualification_id' => $q6->id, 'label' => '450-900 VA', 'value' => 10, 'sort_order' => 2]);
        QualificationOption::firstOrCreate(['qualification_id' => $q6->id, 'label' => '≤ 450 VA / Tidak Ada', 'value' => 20, 'sort_order' => 3]);

        // Indikator 7: DTKS/KKS (single_choice)
        $q7 = Qualification::firstOrCreate(
            ['scholarship_id' => $bbk2025->id, 'name' => 'Penerima DTKS / KKS / PKH'],
            [
                'qualification_group_id' => $group1->id,
                'type' => 'single_choice',
                'is_required' => false,
                'is_file_upload_required' => true,
                'file_upload_label' => 'Upload Bukti DTKS/KKS/PKH',
                'sort_order' => 7,
            ]
        );
        QualificationOption::firstOrCreate(['qualification_id' => $q7->id, 'label' => 'Tidak', 'value' => 0, 'sort_order' => 1]);
        QualificationOption::firstOrCreate(['qualification_id' => $q7->id, 'label' => 'Ya, PKH', 'value' => 25, 'sort_order' => 2]);
        QualificationOption::firstOrCreate(['qualification_id' => $q7->id, 'label' => 'Ya, KKS', 'value' => 25, 'sort_order' => 3]);

        // Indikator 8: IPK (numeric_range) — Group 2
        $q8 = Qualification::firstOrCreate(
            ['scholarship_id' => $bbk2025->id, 'name' => 'IPK Terakhir'],
            [
                'qualification_group_id' => $group2->id,
                'type' => 'numeric_range',
                'is_required' => true,
                'is_file_upload_required' => true,
                'file_upload_label' => 'Upload Transkrip Nilai',
                'sort_order' => 1,
            ]
        );
        QualificationRange::firstOrCreate(['qualification_id' => $q8->id, 'range_min' => 0, 'range_max' => 2.99, 'value' => 0, 'label' => '< 3.00', 'sort_order' => 1]);
        QualificationRange::firstOrCreate(['qualification_id' => $q8->id, 'range_min' => 3.00, 'range_max' => 3.50, 'value' => 10, 'label' => '3.00-3.50', 'sort_order' => 2]);
        QualificationRange::firstOrCreate(['qualification_id' => $q8->id, 'range_min' => 3.51, 'range_max' => 4.00, 'value' => 20, 'label' => '3.51-4.00', 'sort_order' => 3]);

        // Indikator 9: Prestasi (multi_choice)
        $q9 = Qualification::firstOrCreate(
            ['scholarship_id' => $bbk2025->id, 'name' => 'Prestasi yang Dimiliki'],
            [
                'qualification_group_id' => $group2->id,
                'type' => 'multi_choice',
                'is_required' => false,
                'is_file_upload_required' => true,
                'file_upload_label' => 'Upload Sertifikat Prestasi',
                'sort_order' => 2,
            ]
        );
        QualificationOption::firstOrCreate(['qualification_id' => $q9->id, 'label' => 'Tidak Ada', 'value' => 0, 'sort_order' => 1]);
        QualificationOption::firstOrCreate(['qualification_id' => $q9->id, 'label' => 'Juara Kelas / Sekolah', 'value' => 5, 'sort_order' => 2]);
        QualificationOption::firstOrCreate(['qualification_id' => $q9->id, 'label' => 'Juara Kabupaten / Kota', 'value' => 15, 'sort_order' => 3]);
        QualificationOption::firstOrCreate(['qualification_id' => $q9->id, 'label' => 'Juara Provinsi', 'value' => 25, 'sort_order' => 4]);
        QualificationOption::firstOrCreate(['qualification_id' => $q9->id, 'label' => 'Juara Nasional / Internasional', 'value' => 40, 'sort_order' => 5]);

        // Indikator 10: Motivasi (text)
        Qualification::firstOrCreate(
            ['scholarship_id' => $bbk2025->id, 'name' => 'Motivasi dan Rencana Studi'],
            [
                'qualification_group_id' => $group2->id,
                'type' => 'text',
                'is_required' => true,
                'is_file_upload_required' => false,
                'description' => 'Ceritakan motivasi Anda melanjutkan studi dan rencana setelah lulus.',
                'sort_order' => 3,
            ]
        );

        $this->command->info('BBK Madiun seeder: 2 programs, 10 qualifications, 5 users created.');
    }
}
