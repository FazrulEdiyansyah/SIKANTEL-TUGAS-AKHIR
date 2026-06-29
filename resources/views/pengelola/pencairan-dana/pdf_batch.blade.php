<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pencairan Dana Batch - {{ $batchId }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; }
        .page-break { page-break-after: always; }
        .page-break:last-child { page-break-after: auto; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #e0e0e0; padding-bottom: 10px; }
        .header h1 { margin: 0 0 5px 0; font-size: 20px; }
        .header p { margin: 0; color: #666; font-size: 12px; }
        .info-table { width: 100%; margin-bottom: 30px; }
        .info-table td { padding: 5px; }
        .info-label { font-weight: bold; width: 150px; }
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .data-table th, .data-table td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        .data-table th { background-color: #f5f5f5; }
        .text-right { text-align: right !important; }
        .footer { margin-top: 50px; text-align: right; }
        .signature-box { display: inline-block; text-align: center; width: 200px; }
        .signature-line { margin-top: 80px; border-bottom: 1px solid #333; }
        .signature-name { margin-top: 5px; font-weight: bold; }
    </style>
</head>
<body>

    @foreach($pencairan_danas as $pencairan)
    <div class="page-break">
        <div class="header">
            <h1>LAPORAN PENCAIRAN DANA TENANT</h1>
            <p>Sistem Informasi Kantin Telkom University</p>
            <p>Batch ID: {{ $batchId }}</p>
        </div>

        <table class="info-table">
            <tr>
                <td class="info-label">Nama Tenant</td>
                <td>: {{ $pencairan->tenant->nama_tenant }}</td>
            </tr>
            <tr>
                <td class="info-label">Kantin</td>
                <td>: {{ $pencairan->tenant->kantin->nama_kantin ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Periode Laporan</td>
                <td>: {{ \Carbon\Carbon::parse($pencairan->start_date)->format('d F Y') }} - {{ \Carbon\Carbon::parse($pencairan->end_date)->format('d F Y') }}</td>
            </tr>
            <tr>
                <td class="info-label">Tanggal Cetak</td>
                <td>: {{ now()->format('d F Y H:i:s') }}</td>
            </tr>
        </table>

        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Keterangan</th>
                    <th class="text-right">Nominal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Total Penjualan Kotor</td>
                    <td class="text-right">Rp {{ number_format($pencairan->total_penjualan, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Potongan Bagi Hasil (Tel-U 30%)</td>
                    <td class="text-right">Rp {{ number_format($pencairan->dana_telu, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td><strong>Dana Cair (Tenant 70%)</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($pencairan->dana_tenant, 0, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>

        <p style="font-size: 12px; color: #666; margin-top: -15px; margin-bottom: 40px;">
            * Laporan ini dibuat melalui sistem SIKANTEL. Status Pengajuan: {{ strtoupper($pencairan->status) }}
        </p>

        <div class="footer">
            <div class="signature-box">
                <div>Bandung, {{ now()->format('d F Y') }}</div>
                <div>Pengelola Kantin</div>
                <div class="signature-line"></div>
                <div class="signature-name">{{ $pencairan->pengelola->name ?? auth()->user()->name ?? 'Pengelola' }}</div>
            </div>
        </div>
    </div>
    @endforeach

</body>
</html>
