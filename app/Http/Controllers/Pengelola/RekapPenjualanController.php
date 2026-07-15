<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kantin;
use App\Models\Tenant;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PengelolaRekapKantinExport;
use App\Exports\PengelolaRekapTenantExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class RekapPenjualanController extends Controller
{
    private function resolveDates(Request $request)
    {
        $period = $request->get('period', 'all');
        $startDate = null;
        $endDate = null;

        if ($period == 'today') {
            $startDate = now()->startOfDay()->format('Y-m-d');
            $endDate = now()->endOfDay()->format('Y-m-d');
        } elseif ($period == 'this_week') {
            $startDate = now()->startOfWeek()->format('Y-m-d');
            $endDate = now()->endOfWeek()->format('Y-m-d');
        } elseif ($period == 'this_month') {
            $startDate = now()->startOfMonth()->format('Y-m-d');
            $endDate = now()->endOfMonth()->format('Y-m-d');
        } elseif ($period == 'custom') {
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
        }

        return [$period, $startDate, $endDate];
    }

    public function index(Request $request)
    {
        list($period, $startDate, $endDate) = $this->resolveDates($request);

        $query = Kantin::withCount('tenants')
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
            }], 'total_price');
        
        if ($request->has('search') && $request->search != '') {
            $query->where('nama_kantin', 'like', '%' . $request->search . '%');
        }

        $kantins = $query->paginate(10)->withQueryString();
        
        return view('pengelola.rekap-penjualan.index', compact('kantins', 'period', 'startDate', 'endDate'));
    }

    public function show(Kantin $kantin, Request $request)
    {
        list($period, $startDate, $endDate) = $this->resolveDates($request);

        $query = $kantin->tenants()
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
            }], 'total_price');
        
        if ($request->has('search') && $request->search != '') {
            $query->where('nama_tenant', 'like', '%' . $request->search . '%');
        }

        $tenants = $query->paginate(10)->withQueryString();
        
        return view('pengelola.rekap-penjualan.show', compact('kantin', 'tenants', 'period', 'startDate', 'endDate'));
    }

    public function exportExcel(Kantin $kantin, Request $request)
    {
        list($period, $startDate, $endDate) = $this->resolveDates($request);
        
        $filename = 'Rekap_Penjualan_' . str_replace(' ', '_', $kantin->nama_kantin) . '_' . date('Ymd') . '.xlsx';
        return Excel::download(new PengelolaRekapKantinExport($kantin->id, $startDate, $endDate), $filename);
    }

    public function exportPdf(Kantin $kantin, Request $request)
    {
        list($period, $startDate, $endDate) = $this->resolveDates($request);

        $tenants = $kantin->tenants()
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
            
        $totalSemuaPesanan = $tenants->sum('pesanan_selesai');
        $totalSemuaPenjualan = $tenants->sum('total_penjualan');

        $displayStartDate = $startDate;
        $displayEndDate = $endDate;

        if (!$displayStartDate || !$displayEndDate) {
            $firstOrder = \App\Models\Order::whereHas('tenant', function($q) use ($kantin) {
                $q->where('kantin_id', $kantin->id);
            })->orderBy('created_at', 'asc')->first();
            
            $lastOrder = \App\Models\Order::whereHas('tenant', function($q) use ($kantin) {
                $q->where('kantin_id', $kantin->id);
            })->orderBy('created_at', 'desc')->first();
            
            $displayStartDate = $firstOrder ? $firstOrder->created_at->format('Y-m-d') : date('Y-m-d');
            $displayEndDate = $lastOrder ? $lastOrder->created_at->format('Y-m-d') : date('Y-m-d');
        }

        $pdf = Pdf::loadView('pengelola.rekap-penjualan.pdf', compact('kantin', 'tenants', 'period', 'displayStartDate', 'displayEndDate', 'totalSemuaPesanan', 'totalSemuaPenjualan'));
        $pdf->setPaper('A4', 'landscape');
        
        $filename = 'Rekap_Penjualan_' . str_replace(' ', '_', $kantin->nama_kantin) . '_' . date('Ymd') . '.pdf';
        return $pdf->download($filename);
    }

    public function showTenant(Kantin $kantin, Tenant $tenant, Request $request)
    {
        list($period, $startDate, $endDate) = $this->resolveDates($request);

        $query = $tenant->orders()
            ->where('payment_status', 'success')
            ->where('order_status', 'selesai');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        if ($request->has('search') && $request->search != '') {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })->orWhere('id', 'like', '%' . $request->search . '%');
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(5)->withQueryString();

        $totalPenjualan = $tenant->orders()
            ->where('payment_status', 'success')
            ->where('order_status', 'selesai')
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                return $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            })
            ->sum('total_price');

        $totalPesanan = $tenant->orders()
            ->where('payment_status', 'success')
            ->where('order_status', 'selesai')
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                return $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            })
            ->count();

        return view('pengelola.rekap-penjualan.show-tenant', compact('kantin', 'tenant', 'orders', 'period', 'startDate', 'endDate', 'totalPenjualan', 'totalPesanan'));
    }

    public function exportExcelTenant(Kantin $kantin, Tenant $tenant, Request $request)
    {
        list($period, $startDate, $endDate) = $this->resolveDates($request);
        
        $filename = 'Rekap_Penjualan_' . str_replace(' ', '_', $tenant->nama_tenant) . '_' . date('Ymd') . '.xlsx';
        return Excel::download(new PengelolaRekapTenantExport($tenant->id, $startDate, $endDate), $filename);
    }

    public function exportPdfTenant(Kantin $kantin, Tenant $tenant, Request $request)
    {
        list($period, $startDate, $endDate) = $this->resolveDates($request);

        $query = $tenant->orders()
            ->where('payment_status', 'success')
            ->where('order_status', 'selesai');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        $orders = $query->orderBy('created_at', 'asc')->get();

        $displayStartDate = $startDate;
        $displayEndDate = $endDate;

        if (!$displayStartDate || !$displayEndDate) {
            $firstOrder = $tenant->orders()->orderBy('created_at', 'asc')->first();
            $lastOrder = $tenant->orders()->orderBy('created_at', 'desc')->first();
            
            $displayStartDate = $firstOrder ? $firstOrder->created_at->format('Y-m-d') : date('Y-m-d');
            $displayEndDate = $lastOrder ? $lastOrder->created_at->format('Y-m-d') : date('Y-m-d');
        }

        $totalPenjualan = $orders->sum('total_price');
        $totalPesanan = $orders->count();

        $pdf = Pdf::loadView('pengelola.rekap-penjualan.pdf-tenant', compact('kantin', 'tenant', 'orders', 'period', 'displayStartDate', 'displayEndDate', 'totalPenjualan', 'totalPesanan'));
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'Rekap_Penjualan_' . str_replace(' ', '_', $tenant->nama_tenant) . '_' . date('Ymd') . '.pdf';
        return $pdf->download($filename);
    }
}
