<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Audit Log — {{ $scholarship->name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; margin: 15px; }
        h2 { text-align: center; margin-bottom: 4px; }
        .subtitle { text-align: center; color: #666; font-size: 9px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f3f4f6; text-align: left; padding: 6px 4px; border-bottom: 2px solid #d1d5db; font-size: 9px; text-transform: uppercase; }
        td { padding: 5px 4px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        .footer { margin-top: 20px; text-align: right; font-size: 8px; color: #999; }
    </style>
</head>
<body>
    <h2>Laporan Audit Log Verifikasi</h2>
    <p class="subtitle">{{ $scholarship->name }} — {{ $scholarship->academic_year }}</p>

    <table>
        <thead>
            <tr>
                <th width="100">Verifikator</th>
                <th width="80">Aksi</th>
                <th>No. Registrasi</th>
                <th>Detail</th>
                <th width="60">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td>{{ $log->verifier?->name }}</td>
                    <td>{{ str_replace('_', ' ', $log->action) }}</td>
                    <td>{{ $log->application?->registration_number }}</td>
                    <td>
                        @if($log->field_changed){{ $log->field_changed }}: {{ $log->old_value }} → {{ $log->new_value }}@endif
                        @if($log->reason)<br><em>{{ $log->reason }}</em>@endif
                    </td>
                    <td>{{ $log->created_at?->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center;padding:20px;">Belum ada log verifikasi.</td></tr>
            @endforelse
        </tbody>
    </table>

    <p class="footer">Dicetak: {{ now()->format('d M Y H:i') }}</p>
</body>
</html>
