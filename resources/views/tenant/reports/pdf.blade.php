<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Penjualan {{ $tenant->nama_tenant }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #ed1c24;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #ed1c24;
            margin: 0 0 5px 0;
            font-size: 24px;
        }
        .header p {
            margin: 0;
            color: #666;
        }
        .summary {
            margin-bottom: 20px;
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
        }
        .summary p {
            margin: 5px 0;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #ed1c24;
            color: white;
            font-size: 11px;
        }
        td {
            font-size: 11px;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            text-align: right;
            margin-top: 30px;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Rekap Penjualan - {{ $tenant->nama_tenant }}</h1>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d M Y, H:i') }}</p>
    </div>

    <div class="summary">
        <p>Total Pesanan Selesai: {{ count($orders) }} pesanan</p>
        <p>Total Pendapatan: Rp{{ number_format($totalPendapatan, 0, ',', '.') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">ID Pesanan</th>
                <th width="15%">Waktu</th>
                <th width="15%">Pelanggan</th>
                <th width="10%">Layanan</th>
                <th width="25%">Menu</th>
                <th width="15%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $index => $order)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ substr($order->order_id, -8) }}</td>
                    <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</td>
                    <td>{{ $order->user->name ?? 'Guest' }}</td>
                    <td>{{ $order->order_type == 'dine-in' ? 'Dine In' : 'Takeaway' }}</td>
                    <td>
                        @foreach($order->items as $item)
                            {{ $item->nama_menu }} (x{{ $item->quantity }})<br>
                        @endforeach
                    </td>
                    <td class="text-right">Rp{{ number_format($order->total_price, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Belum ada data penjualan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak dari sistem SIKANTEL
    </div>

</body>
</html>
