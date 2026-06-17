<?php

namespace App\Exports;

use App\Models\Disbursement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DisbursementExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize, WithStyles
{
    public function __construct(
        private int $scholarshipId,
    ) {}

    public function collection()
    {
        return Disbursement::with(['application.user', 'scholarship'])
            ->where('scholarship_id', $this->scholarshipId)
            ->get();
    }

    public function headings(): array
    {
        return ['No. Registrasi', 'Nama Penerima', 'Bank', 'No. Rekening', 'Nama Pemegang', 'Nominal', 'Status'];
    }

    public function map($disbursement): array
    {
        return [
            $disbursement->application?->registration_number ?? '-',
            $disbursement->application?->user?->name ?? '-',
            $disbursement->bank_name,
            "'" . $disbursement->account_number, // preserve leading zeros
            $disbursement->account_holder_name,
            $disbursement->amount,
            match ($disbursement->status) {
                'waiting' => 'Menunggu',
                'processing' => 'Diproses',
                'disbursed' => 'Sudah Cair',
                default => $disbursement->status,
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
