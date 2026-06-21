<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class TenantSalesReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $tenant_id;

    public function __construct($tenant_id)
    {
        $this->tenant_id = $tenant_id;
    }

    public function collection()
    {
        return Order::with(['user', 'items.menu'])
            ->where('tenant_id', $this->tenant_id)
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID Pesanan',
            'Tanggal',
            'Waktu',
            'Nama Pelanggan',
            'Layanan',
            'Menu yang Dipesan',
            'Total Pembayaran (Rp)'
        ];
    }

    public function map($order): array
    {
        $menuList = $order->items->map(function ($item) {
            return $item->nama_menu . ' (x' . $item->quantity . ')';
        })->implode(', ');

        return [
            $order->order_id,
            Carbon::parse($order->created_at)->format('d M Y'),
            Carbon::parse($order->created_at)->format('H:i'),
            $order->user->name ?? 'Guest',
            $order->order_type == 'dine-in' ? 'Makan di Tempat' : 'Bawa Pulang',
            $menuList,
            $order->total_price
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
