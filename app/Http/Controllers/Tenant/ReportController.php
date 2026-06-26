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
        
        $filter = $request->query('filter', 'mingguan'); // mingguan, bulanan
        
        // Data for Chart
        $labels = [];
        $data = [];
        
        if ($filter == 'bulanan') {
            // Last 30 days
            for ($i = 29; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $labels[] = $date->format('d M');
                
                $dailyTotal = Order::where('tenant_id', $tenant->id)
                    ->whereDate('created_at', $date)
                    ->where('payment_status', 'success')
                    ->where('order_status', 'selesai')
                    ->sum('total_price');
                    
                $data[] = $dailyTotal;
            }
        } else {
            // Last 7 days (default)
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $labels[] = $date->format('d M');
                
                $dailyTotal = Order::where('tenant_id', $tenant->id)
                    ->whereDate('created_at', $date)
                    ->where('payment_status', 'success')
                    ->where('order_status', 'selesai')
                    ->sum('total_price');
                    
                $data[] = $dailyTotal;
            }
        }

        // Summary metrics
        $totalPendapatan = Order::where('tenant_id', $tenant->id)
            ->where('payment_status', 'success')
            ->where('order_status', 'selesai')
            ->sum('total_price');
            
        $pesananSelesai = Order::where('tenant_id', $tenant->id)
            ->where('payment_status', 'success')
            ->where('order_status', 'selesai')
            ->count();
            
        // Recent completed orders for table
        $orders = Order::with('user')
            ->where('tenant_id', $tenant->id)
            ->where('payment_status', 'success')
            ->where('order_status', 'selesai')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('tenant.reports.index', compact(
            'tenant', 
            'labels', 
            'data', 
            'filter',
            'totalPendapatan',
            'pesananSelesai',
            'orders'
        ));
    }

    public function exportExcel(Request $request)
    {
        $tenant = Tenant::where('user_id', Auth::id())->firstOrFail();
        return Excel::download(new TenantSalesReportExport($tenant->id), 'Rekap_Penjualan_' . date('Y-m-d') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $tenant = Tenant::where('user_id', Auth::id())->firstOrFail();
        
        $orders = Order::with(['user', 'items.menu'])
            ->where('tenant_id', $tenant->id)
            ->where('payment_status', 'success')
            ->where('order_status', 'selesai')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $totalPendapatan = $orders->sum('total_price');
            
        $pdf = Pdf::loadView('tenant.reports.pdf', compact('tenant', 'orders', 'totalPendapatan'));
        
        return $pdf->download('Rekap_Penjualan_' . date('Y-m-d') . '.pdf');
    }
}
