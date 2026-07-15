<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PengelolaRekapTenantExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $tenant_id;
    protected $startDate;
    protected $endDate;

    public function __construct($tenant_id, $startDate = null, $endDate = null)
    {
        $this->tenant_id = $tenant_id;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $query = Order::with('customer')
            ->where('tenant_id', $this->tenant_id)
            ->where('payment_status', 'success')
            ->where('order_status', 'selesai');

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']);
        }

        return $query->orderBy('created_at', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'ID Pesanan',
            'Tanggal',
            'Waktu',
            'Nama Pelanggan',
            'Item Pesanan',
            'Total Harga (Rp)'
        ];
    }

    public function map($order): array
    {
        // Gabungkan semua item yang dipesan
        $itemsText = '';
        if ($order->items) {
            $itemNames = [];
            foreach ($order->items as $item) {
                $itemNames[] = ($item->menu->nama_menu ?? 'Menu Dihapus') . ' (' . $item->quantity . 'x)';
            }
            $itemsText = implode(', ', $itemNames);
        }

        return [
            '#' . str_pad($order->id, 5, '0', STR_PAD_LEFT),
            $order->created_at->format('d/m/Y'),
            $order->created_at->format('H:i'),
            $order->user->name ?? '-',
            $itemsText,
            $order->total_price,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
