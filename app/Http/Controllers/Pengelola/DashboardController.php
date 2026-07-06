<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kantin;
use App\Models\Tenant;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $kantinId = $request->query('kantin_id');

        $totalKantin = Kantin::count();
        $totalTenant = Tenant::count();
        
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $penjualanBulanIniQuery = Order::where('payment_status', 'success')
            ->where('order_status', 'selesai')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear);
            
        if ($kantinId) {
            $penjualanBulanIniQuery->whereHas('tenant', function ($q) use ($kantinId) {
                $q->where('kantin_id', $kantinId);
            });
        }
        
        $penjualanBulanIni = $penjualanBulanIniQuery->sum('total_price');

        // Data for Chart (Last 30 days) - Optimized to single query
        $startDate = Carbon::today()->subDays(29);
        $endDate = Carbon::today()->endOfDay();

        $chartQuery = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'success')
            ->where('order_status', 'selesai');

        if ($kantinId) {
            $chartQuery->whereHas('tenant', function ($q) use ($kantinId) {
                $q->where('kantin_id', $kantinId);
            });
        }

        $salesData = $chartQuery->selectRaw('DATE(created_at) as date, SUM(total_price) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $labels = [];
        $data = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $labels[] = $date->format('d M');
            $data[] = $salesData->get($dateStr, 0);
        }

        // Top 5 Tenants by Sales
        $topTenantsQuery = Tenant::with('kantin')
            ->withSum(['orders' => function ($query) {
                $query->where('orders.payment_status', 'success')
                      ->where('orders.order_status', 'selesai');
            }], 'total_price')
            ->withCount(['orders' => function ($query) {
                $query->where('orders.payment_status', 'success')
                      ->where('orders.order_status', 'selesai');
            }]);

        if ($kantinId) {
            $topTenantsQuery->where('kantin_id', $kantinId);
        }

        $topTenants = $topTenantsQuery
            ->orderByRaw('"orders_sum_total_price" DESC NULLS LAST')
            ->take(5)
            ->get();

        // Kantin Teramai (by total sales amount instead of order count)
        $kantinTeramaiQuery = Kantin::withSum(['orders' => function ($query) {
                $query->where('orders.payment_status', 'success')
                      ->where('orders.order_status', 'selesai');
            }], 'total_price');
            
        if ($kantinId) {
            $kantinTeramaiQuery->where('id', $kantinId);
        }

        $kantinTeramai = $kantinTeramaiQuery
            ->orderByRaw('"orders_sum_total_price" DESC NULLS LAST')
            ->first();
            
        // Get all kantins for the filter dropdown
        $allKantins = Kantin::orderBy('nama_kantin')->get();

        return view('pengelola.dashboard', compact(
            'totalKantin', 
            'totalTenant', 
            'penjualanBulanIni',
            'labels',
            'data',
            'topTenants',
            'kantinTeramai',
            'allKantins',
            'kantinId'
        ));
    }

    public function kantinIndex()
    {
        return view('pengelola.kantin.index');
    }
}
