<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TenantSalesReportExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $tenant = Tenant::where('user_id', Auth::id())->firstOrFail();
        
        $filter = $request->query('filter', 'bulan_ini'); // hari_ini, minggu_ini, bulan_ini, semua
        
        // Data for Chart
        $labels = [];
        $data = [];
        
        $startDate = Carbon::today()->startOfMonth();
        $endDate = Carbon::now();
        
        if ($filter == 'hari_ini') {
            $startDate = Carbon::today();
            $endDate = Carbon::today()->endOfDay();
            
            $orders = Order::where('tenant_id', $tenant->id)
                ->whereBetween('updated_at', [$startDate, $endDate])
                ->where('payment_status', 'success')
                ->where('order_status', 'selesai')
                ->select('updated_at', 'total_price')
                ->get();
                
            $salesData = [];
            foreach ($orders as $order) {
                $hour = (int) $order->updated_at->format('H');
                $salesData[$hour] = ($salesData[$hour] ?? 0) + $order->total_price;
            }
            
            for ($i = 0; $i <= 23; $i++) {
                $labels[] = sprintf('%02d:00', $i);
                $data[] = $salesData[$i] ?? 0;
            }
        } elseif ($filter == 'minggu_ini') {
            $startDate = Carbon::today()->subDays(6); // last 7 days
            $endDate = Carbon::now();
            
            $orders = Order::where('tenant_id', $tenant->id)
                ->whereBetween('updated_at', [$startDate, $endDate])
                ->where('payment_status', 'success')
                ->where('order_status', 'selesai')
                ->select('updated_at', 'total_price')
                ->get();
                
            $salesData = [];
            foreach ($orders as $order) {
                $dateStr = $order->updated_at->format('Y-m-d');
                $salesData[$dateStr] = ($salesData[$dateStr] ?? 0) + $order->total_price;
            }
            
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $dateStr = $date->format('Y-m-d');
                $labels[] = $date->format('d M');
                $data[] = $salesData[$dateStr] ?? 0;
            }
        } elseif ($filter == 'semua') {
            $startDate = Carbon::parse('2000-01-01'); // Long time ago
            $endDate = Carbon::now();
            
            $orders = Order::where('tenant_id', $tenant->id)
                ->where('payment_status', 'success')
                ->where('order_status', 'selesai')
                ->select('updated_at', 'total_price')
                ->get();
                
            $salesData = [];
            foreach ($orders as $order) {
                $dateLabel = $order->updated_at->format('M Y');
                $salesData[$dateLabel] = ($salesData[$dateLabel] ?? 0) + $order->total_price;
            }
            
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::today()->startOfMonth()->subMonths($i);
                $dateLabel = $date->format('M Y');
                $labels[] = $dateLabel;
                $data[] = $salesData[$dateLabel] ?? 0;
            }
        } else {
            // bulan_ini (last 30 days)
            $filter = 'bulan_ini';
            $startDate = Carbon::today()->subDays(29);
            $endDate = Carbon::now();
            
            $orders = Order::where('tenant_id', $tenant->id)
                ->whereBetween('updated_at', [$startDate, $endDate])
                ->where('payment_status', 'success')
                ->where('order_status', 'selesai')
                ->select('updated_at', 'total_price')
                ->get();
                
            $salesData = [];
            foreach ($orders as $order) {
                $dateStr = $order->updated_at->format('Y-m-d');
                $salesData[$dateStr] = ($salesData[$dateStr] ?? 0) + $order->total_price;
            }
            
            for ($i = 29; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $dateStr = $date->format('Y-m-d');
                $labels[] = $date->format('d M');
                $data[] = $salesData[$dateStr] ?? 0;
            }
        }

        // Base Query for Summary and Table
        $query = Order::where('tenant_id', $tenant->id)
            ->where('payment_status', 'success')
            ->where('order_status', 'selesai');
            
        if ($filter != 'semua') {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Summary metrics
        $totalPendapatanKotor = (clone $query)->sum('total_price');
        $totalPendapatanBersih = $totalPendapatanKotor * 0.70;
        $pesananSelesai = (clone $query)->count();
            
        // Recent completed orders for table
        $orders = (clone $query)->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends(['filter' => $filter]);

        return view('tenant.reports.index', compact(
            'tenant', 
            'labels', 
            'data', 
            'filter',
            'totalPendapatanKotor',
            'totalPendapatanBersih',
            'pesananSelesai',
            'orders'
        ));
    }

    public function exportExcel(Request $request)
    {
        $tenant = Tenant::where('user_id', Auth::id())->firstOrFail();
        $filter = $request->query('filter', 'bulan_ini');
        
        return Excel::download(new TenantSalesReportExport($tenant->id, $filter), 'Rekap_Penjualan_' . date('Y-m-d') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $tenant = Tenant::where('user_id', Auth::id())->firstOrFail();
        $filter = $request->query('filter', 'bulan_ini');
        
        $query = Order::with(['user', 'items.menu'])
            ->where('tenant_id', $tenant->id)
            ->where('payment_status', 'success')
            ->where('order_status', 'selesai');
            
        if ($filter == 'hari_ini') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($filter == 'minggu_ini') {
            $query->whereBetween('created_at', [Carbon::today()->subDays(6), Carbon::now()]);
        } elseif ($filter == 'bulan_ini') {
            $query->whereBetween('created_at', [Carbon::today()->subDays(29), Carbon::now()]);
        }
            
        $orders = $query->orderBy('created_at', 'desc')->get();
            
        $totalPendapatanKotor = $orders->sum('total_price');
        $totalPendapatanBersih = $totalPendapatanKotor * 0.70;
            
        $pdf = Pdf::loadView('tenant.reports.pdf', compact('tenant', 'orders', 'totalPendapatanKotor', 'totalPendapatanBersih', 'filter'));
        
        return $pdf->download('Rekap_Penjualan_' . date('Y-m-d') . '.pdf');
    }
}
