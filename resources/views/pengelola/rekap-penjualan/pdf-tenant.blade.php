<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Rekapitulasi Penjualan Tenant - {{ $tenant->nama_tenant }}</title>
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
                    <h2>Laporan Penjualan Tenant</h2>
                    <div class="doc-number">Kantin: {{ strtoupper($kantin->nama_kantin) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Informasi Laporan -->
    <div class="info-panel">
        <table class="info-table">
            <tr>
                <td class="info-label">TENANT</td>
                <td class="info-value">: <strong>{{ $tenant->nama_tenant }}</strong></td>
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
                <th width="15%">ID Pesanan</th>
                <th width="15%">Tanggal & Waktu</th>
                <th width="20%">Nama Pelanggan</th>
                <th width="30%">Item Menu</th>
                <th width="15%">Total Harga (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $index => $order)
                @php
                    $itemsText = '';
                    if ($order->items) {
                        $itemNames = [];
                        foreach ($order->items as $item) {
                            $itemNames[] = ($item->menu->nama_menu ?? 'Menu Dihapus') . ' (' . $item->quantity . 'x)';
                        }
                        $itemsText = implode(', ', $itemNames);
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center font-bold">#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td class="text-center">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $order->user->name ?? '-' }}</td>
                    <td>{{ $itemsText }}</td>
                    <td class="text-right font-bold">{{ number_format($order->total_price, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 40px; color: #6b7280;">Tidak ada pesanan tenant pada periode yang dipilih.</td>
                </tr>
            @endforelse
            
            @if(count($orders) > 0)
            <tr class="total-row">
                <td colspan="5" class="text-right">TOTAL PENJUALAN PERIODE INI</td>
                <td class="text-right">{{ number_format($totalPenjualan, 0, ',', '.') }}</td>
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
                        <div class="summary-title">Total Pesanan Selesai</div>
                        <div class="summary-value">{{ count($orders) }} Pesanan</div>
                    </div>
                </td>
                <td>
                    <div class="summary-box">
                        <div class="summary-title">Total Pendapatan Kotor</div>
                        <div class="summary-value" style="color: #ed1b24;">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
