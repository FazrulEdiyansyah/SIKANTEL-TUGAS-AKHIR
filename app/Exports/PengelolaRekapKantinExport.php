<?php

namespace App\Exports;

use App\Models\Tenant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PengelolaRekapKantinExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $kantin_id;
    protected $startDate;
    protected $endDate;

    public function __construct($kantin_id, $startDate = null, $endDate = null)
    {
        $this->kantin_id = $kantin_id;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $startDate = $this->startDate;
        $endDate = $this->endDate;

        return Tenant::where('kantin_id', $this->kantin_id)
            ->withCount(['orders as pesanan_selesai' => function ($query) use ($startDate, $endDate) {
                $query->where('payment_status', 'success')
                      ->where('order_status', 'selesai');
                if ($startDate && $endDate) {
                    $query->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                }
            }])
            ->withSum(['orders as total_penjualan' => function ($query) use ($startDate, $endDate) {
                $query->where('payment_status', 'success')
                      ->where('order_status', 'selesai');
                if ($startDate && $endDate) {
                    $query->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                }
            }], 'total_price')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama Tenant',
            'No HP',
            'Total Pesanan Selesai',
            'Total Penjualan Kotor (Rp)',
        ];
    }

    public function map($tenant): array
    {
        return [
            $tenant->nama_tenant,
            $tenant->no_telepon ?? '-',
            $tenant->pesanan_selesai ?? 0,
            $tenant->total_penjualan ?? 0,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
