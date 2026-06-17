<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penerima — {{ $scholarship->name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; margin: 20px; }
        h2 { text-align: center; margin-bottom: 4px; }
        .subtitle { text-align: center; color: #666; font-size: 10px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f3f4f6; text-align: left; padding: 8px 6px; border-bottom: 2px solid #d1d5db; font-size: 10px; text-transform: uppercase; }
        td { padding: 6px; border-bottom: 1px solid #e5e7eb; }
        .utama { color: #16a34a; font-weight: bold; }
        .cadangan { color: #ca8a04; }
        .footer { margin-top: 30px; text-align: right; font-size: 9px; color: #999; }
    </style>
</head>
<body>
    <h2>Laporan Penerima Beasiswa</h2>
    <p class="subtitle">{{ $scholarship->name }} — {{ $scholarship->academic_year }} · Kuota: {{ $scholarship->quota_primary }}</p>

    <table>
        <thead>
            <tr>
                <th width="40">Rank</th>
                <th>No. Registrasi</th>
                <th>Nama</th>
                <th width="60">Skor</th>
                <th width="80">Hasil</th>
            </tr>
        </thead>
        <tbody>
            @forelse($results as $result)
                <tr>
                    <td>{{ $result['rank'] }}</td>
                    <td>{{ $result['registration_number'] }}</td>
                    <td>{{ $result['name'] }}</td>
                    <td>{{ $result['total_score'] }}</td>
                    <td class="{{ $result['selection_result'] }}">
                        {{ match($result['selection_result']) { 'utama' => 'Lolos Utama', 'cadangan' => 'Cadangan', default => 'Tidak Lolos' } }}
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center;padding:20px;">Belum ada data penerima.</td></tr>
            @endforelse
        </tbody>
    </table>

    <p class="footer">Dicetak: {{ now()->format('d M Y H:i') }}</p>
</body>
</html>
