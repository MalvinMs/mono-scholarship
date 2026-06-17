<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Pencairan — {{ $scholarship->name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; margin: 15px; }
        h2 { text-align: center; margin-bottom: 4px; }
        .subtitle { text-align: center; color: #666; font-size: 9px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f3f4f6; text-align: left; padding: 6px 4px; border-bottom: 2px solid #d1d5db; font-size: 9px; text-transform: uppercase; }
        td { padding: 5px 4px; border-bottom: 1px solid #e5e7eb; }
        .footer { margin-top: 20px; text-align: right; font-size: 8px; color: #999; }
    </style>
</head>
<body>
    <h2>Rekap Data Pencairan Dana</h2>
    <p class="subtitle">{{ $scholarship->name }} — {{ $scholarship->academic_year }}</p>

    <table>
        <thead>
            <tr>
                <th>No. Registrasi</th>
                <th>Penerima</th>
                <th>Bank</th>
                <th>No. Rekening</th>
                <th>Pemegang</th>
                <th width="80">Nominal</th>
                <th width="70">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($disbursements as $d)
                <tr>
                    <td>{{ $d->application?->registration_number }}</td>
                    <td>{{ $d->application?->user?->name }}</td>
                    <td>{{ $d->bank_name }}</td>
                    <td>{{ $d->account_number }}</td>
                    <td>{{ $d->account_holder_name }}</td>
                    <td style="text-align:right">Rp {{ number_format($d->amount, 0, ',', '.') }}</td>
                    <td>{{ match($d->status) { 'waiting' => 'Menunggu', 'processing' => 'Diproses', 'disbursed' => 'Cair', default => $d->status } }}</td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;padding:20px;">Belum ada data pencairan.</td></tr>
            @endforelse
        </tbody>
    </table>

    <p class="footer">Dicetak: {{ now()->format('d M Y H:i') }}</p>
</body>
</html>
