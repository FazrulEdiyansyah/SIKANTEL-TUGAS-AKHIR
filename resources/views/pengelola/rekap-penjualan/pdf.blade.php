<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Rekapitulasi Penjualan - {{ $kantin->nama_kantin }}</title>
    <style>
        @page {
            margin: 40px 50px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1f2937;
            font-size: 11px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        /* Header / Kop Surat */
        .kop-surat {
            border-bottom: 3px solid #ed1b24;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .kop-surat table {
            width: 100%;
            border: none;
        }
        .kop-surat table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }
        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #ed1b24;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 4px;
        }
        .company-subtitle {
            font-size: 11px;
            color: #4b5563;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-weight: 600;
        }
        .company-address {
            font-size: 10px;
            color: #6b7280;
            margin-top: 5px;
        }
        .report-title {
            text-align: right;
        }
        .report-title h2 {
            font-size: 18px;
            color: #374151;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .report-title .doc-number {
            font-size: 10px;
            color: #9ca3af;
        }

        /* Informasi Laporan */
        .info-panel {
            margin-bottom: 25px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 4px 0;
            vertical-align: top;
            border: none;
            font-size: 11px;
        }
        .info-label {
            font-weight: bold;
            width: 120px;
            color: #374151;
        }
        .info-value {
            color: #1f2937;
        }

        /* Tabel Data Utama */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            border: 1px solid #d1d5db;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #d1d5db;
            padding: 10px 12px;
        }
        table.data-table th {
            background-color: #f3f4f6;
            color: #111827;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.5px;
        }
        table.data-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .text-left { text-align: left !important; }
        .font-bold { font-weight: bold !important; }
        
        /* Baris Total */
        .total-row td {
            background-color: #fee2e2 !important;
            font-weight: bold;
            color: #b91c1c;
            border-top: 2px solid #ed1b24;
        }

        /* Summary Boxes */
        .summary-container {
            width: 100%;
            margin-bottom: 40px;
        }
        .summary-container table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 15px 0;
        }
        .summary-container table td {
            border: none;
            padding: 0;
        }
        .summary-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-top: 4px solid #ed1b24;
            padding: 15px;
            border-radius: 4px;
        }
        .summary-box.dark {
            border-top-color: #334155;
        }
        .summary-box.blue {
            border-top-color: #0ea5e9;
        }
        .summary-title {
            font-size: 10px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
        }

        /* Signatures */
        .signature-section {
            width: 100%;
            margin-top: 50px;
            page-break-inside: avoid;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-table td {
            border: none;
            text-align: center;
            vertical-align: bottom;
            height: 120px;
            width: 33.33%;
        }
        .signature-title {
            font-weight: bold;
            color: #374151;
            margin-bottom: 60px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            color: #111827;
        }
        .signature-role {
            color: #6b7280;
            font-size: 10px;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
        .page-number:after {
            content: counter(page);
        }
    </style>
</head>
<body>
    @php
        // Resolve exactly what date to show
        $formattedDateRange = '';
        if ($displayStartDate && $displayEndDate) {
            if ($displayStartDate == $displayEndDate) {
                $formattedDateRange = \Carbon\Carbon::parse($displayStartDate)->format('d F Y');
            } else {
                $formattedDateRange = \Carbon\Carbon::parse($displayStartDate)->format('d F Y') . ' s/d ' . \Carbon\Carbon::parse($displayEndDate)->format('d F Y');
            }
        }
    @endphp

    <div class="footer">
        Dicetak pada: {{ now()->format('d F Y H:i:s') }} | Dokumen Resmi SIKANTEL | Halaman <span class="page-number"></span>
    </div>

    <!-- Kop Surat -->
    <div class="kop-surat">
        <table>
            <tr>
                <td width="60%">
                    <div class="company-name">SIKANTEL</div>
                    <div class="company-subtitle">Sistem Informasi Kantin Telkom University</div>
                    <div class="company-address">Jl. Telekomunikasi. 1, Terusan Buahbatu - Bojongsoang, Telkom University, Sukapura, Kec. Dayeuhkolot, Kabupaten Bandung, Jawa Barat 40257</div>
                </td>
                <td width="40%" class="report-title">
                    <h2>Laporan Penjualan</h2>
                    <div class="doc-number">Kantin: {{ strtoupper($kantin->nama_kantin) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Informasi Laporan -->
    <div class="info-panel">
        <table class="info-table">
            <tr>
                <td class="info-label">KANTIN</td>
                <td class="info-value">: <strong>{{ $kantin->nama_kantin }}</strong></td>
                <td class="info-label">PERIODE LAPORAN</td>
                <td class="info-value">: <strong>{{ $formattedDateRange }}</strong></td>
            </tr>
            <tr>
                <td class="info-label">DICETAK OLEH</td>
                <td class="info-value">: {{ auth()->user()->name ?? 'Administrator' }}</td>
                <td class="info-label">TANGGAL CETAK</td>
                <td class="info-value">: {{ now()->format('d F Y') }}</td>
            </tr>
        </table>
    </div>

    <!-- Tabel Data Utama -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Nama Tenant</th>
                <th width="15%">No. HP</th>
                <th width="10%">Total Pesanan</th>
                <th width="20%">Penjualan Kotor (Rp)</th>
                <th width="15%">Hak Tenant 70% (Rp)</th>
                <th width="15%">Hak Telkom 30% (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalTenantShare = 0;
                $totalTelkomShare = 0;
            @endphp
            @forelse($tenants as $index => $tenant)
                @php
                    $tenantShare = ($tenant->total_penjualan ?? 0) * 0.70;
                    $telkomShare = ($tenant->total_penjualan ?? 0) * 0.30;
                    $totalTenantShare += $tenantShare;
                    $totalTelkomShare += $telkomShare;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $tenant->nama_tenant }}</td>
                    <td class="text-center">{{ $tenant->no_telepon ?? '-' }}</td>
                    <td class="text-center">{{ $tenant->pesanan_selesai ?? 0 }}</td>
                    <td class="text-right">{{ number_format($tenant->total_penjualan ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($tenantShare, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($telkomShare, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 40px; color: #6b7280;">Tidak ada data penjualan tenant pada periode yang dipilih.</td>
                </tr>
            @endforelse
            
            @if(count($tenants) > 0)
            <tr class="total-row">
                <td colspan="3" class="text-right">TOTAL KESELURUHAN PADA PERIODE INI</td>
                <td class="text-center">{{ $totalSemuaPesanan }}</td>
                <td class="text-right">{{ number_format($totalSemuaPenjualan, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totalTenantShare, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totalTelkomShare, 0, ',', '.') }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <!-- Summary / Ringkasan Eksekutif -->
    <div class="summary-container">
        <table>
            <tr>
                <td>
                    <div class="summary-box dark">
                        <div class="summary-title">Total Tenant Aktif (Periode Ini)</div>
                        <div class="summary-value">{{ count($tenants) }} Tenant</div>
                    </div>
                </td>
                <td>
                    <div class="summary-box blue">
                        <div class="summary-title">Akumulasi Hak Tenant (70%)</div>
                        <div class="summary-value">Rp {{ number_format($totalTenantShare, 0, ',', '.') }}</div>
                    </div>
                </td>
                <td>
                    <div class="summary-box">
                        <div class="summary-title">Akumulasi Pendapatan Telkom (30%)</div>
                        <div class="summary-value" style="color: #ed1b24;">Rp {{ number_format($totalTelkomShare, 0, ',', '.') }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
