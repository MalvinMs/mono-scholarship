<?php

namespace App\Exports;

use App\Models\Application;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ApplicantsExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize, WithStyles
{
    public function __construct(
        private int $scholarshipId,
    ) {}

    public function collection()
    {
        return Application::with(['user', 'score'])
            ->where('scholarship_id', $this->scholarshipId)
            ->whereHas('score', fn($q) => $q->where('selection_result', 'utama'))
            ->get();
    }

    public function headings(): array
    {
        return ['Rank', 'No. Registrasi', 'Nama', 'NIK', 'Email', 'Telepon', 'Skor', 'Hasil'];
    }

    public function map($application): array
    {
        return [
            $application->score?->rank ?? '-',
            $application->registration_number,
            $application->user?->name ?? '-',
            $application->user?->nik ?? '-',
            $application->user?->email ?? '-',
            $application->user?->phone ?? '-',
            $application->score?->total_score ?? 0,
            $application->score?->selection_result ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
