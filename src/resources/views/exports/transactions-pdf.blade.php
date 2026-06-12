cat > src/resources/views/exports/transactions-pdf.blade.php << 'BLADE'
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { font-family: Arial, sans-serif; font-size: 12px; }
        body { margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #059669; padding-bottom: 10px; }
        .header h1 { color: #059669; font-size: 18px; margin: 0; }
        .header p { color: #666; margin: 4px 0; font-size: 11px; }
        .summary { display: flex; gap: 10px; margin-bottom: 16px; width: 100%; }
        .summary-card { flex: 1; padding: 10px; border-radius: 6px; }
        .inc { background: #dcfce7; border-left: 4px solid #16a34a; }
        .exp { background: #fee2e2; border-left: 4px solid #dc2626; }
        .bal { background: #dbeafe; border-left: 4px solid #2563eb; }
        .summary-card p { margin: 0; }
        .lbl { font-size: 10px; color: #666; }
        .val { font-size: 14px; font-weight: bold; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #059669; color: white; padding: 8px; text-align: left; font-size: 11px; }
        td { padding: 7px 8px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
        tr:nth-child(even) { background: #f9fafb; }
        .badge { padding: 2px 8px; border-radius: 99px; font-size: 10px; font-weight: bold; }
        .footer { margin-top: 20px; text-align: center; color: #999; font-size: 10px; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>DompetKu &mdash; {{ $title }}</h1>
        <p>Periode: {{ $period }}</p>
        <p>Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB | {{ $userName }}</p>
    </div>

    <div class="summary">
        <div class="summary-card inc">
            <p class="lbl">Total Pemasukan</p>
            <p class="val" style="color:#16a34a;">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
        </div>
        <div class="summary-card exp">
            <p class="lbl">Total Pengeluaran</p>
            <p class="val" style="color:#dc2626;">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
        </div>
        <div class="summary-card bal">
            <p class="lbl">Saldo Bersih</p>
            <p class="val" style="color:#2563eb;">Rp {{ number_format($totalIncome - $totalExpense, 0, ',', '.') }}</p>
        </div>
    </div>

    @if($transactions->isEmpty())
        <div style="text-align:center;padding:30px;color:#999;">
            <p>Tidak ada transaksi pada periode ini.</p>
        </div>
    @else
    <table>
        <thead>
            <tr>
                <th>Tipe</th>
                @if($showUserName)<th>Nama</th>@endif
                <th>Kategori</th>
                <th>Jumlah</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $tx)
            @php
                $isIncome = $tx->type === 'Pemasukan';
                $statusLabel = $tx->status === 'approved' ? 'Disetujui' : ($tx->status === 'pending' ? 'Pending' : ($tx->status === 'rejected' ? 'Ditolak' : $tx->status));
                $statusBg = $tx->status === 'approved' ? '#dcfce7' : ($tx->status === 'pending' ? '#fef9c3' : '#fee2e2');
                $statusColor = $tx->status === 'approved' ? '#15803d' : ($tx->status === 'pending' ? '#a16207' : '#b91c1c');
            @endphp
            <tr>
                <td>
                    <span class="badge" style="background:{{ $isIncome ? '#dcfce7' : '#fee2e2' }};color:{{ $isIncome ? '#15803d' : '#b91c1c' }}">
                        {{ $tx->type }}
                    </span>
                </td>
                @if($showUserName)<td>{{ $tx->user_name ?? '-' }}</td>@endif
                <td>{{ $tx->category_name }}</td>
                <td style="color:{{ $isIncome ? '#16a34a' : '#dc2626' }};font-weight:bold;">
                    {{ $isIncome ? '+' : '-' }}Rp {{ number_format((float)$tx->amount, 0, ',', '.') }}
                </td>
                <td>{{ \Carbon\Carbon::parse($tx->date)->format('d/m/Y') }}</td>
                <td>
                    <span class="badge" style="background:{{ $statusBg }};color:{{ $statusColor }}">
                        {{ $statusLabel }}
                    </span>
                </td>
                <td>{{ $tx->description ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        &copy; {{ date('Y') }} DompetKu &mdash; Laporan ini dibuat otomatis oleh sistem
    </div>
</body>
</html>
BLADE
