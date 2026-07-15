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

        $totalKantin = Kantin::where('status', 'aktif')->count();
        $totalTenant = Tenant::where('status', 'aktif')->count();
        
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

        $timeFilter = $request->query('time_filter', 'bulan');

        $labels = [];
        $data = [];

        if ($timeFilter == 'hari') {
            $startDate = Carbon::today();
            $endDate = Carbon::today()->endOfDay();

            $chartQuery = Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('payment_status', 'success')
                ->where('order_status', 'selesai');

            if ($kantinId) {
                $chartQuery->whereHas('tenant', function ($q) use ($kantinId) {
                    $q->where('kantin_id', $kantinId);
                });
            }

            $salesData = $chartQuery->selectRaw('HOUR(created_at) as hour, SUM(total_price) as total')
                ->groupBy('hour')
                ->pluck('total', 'hour');

            for ($i = 6; $i <= 21; $i++) {
                $labels[] = sprintf('%02d:00', $i);
                $data[] = $salesData->get($i, 0);
            }
        } elseif ($timeFilter == 'minggu') {
            $startDate = Carbon::now()->startOfWeek(); // Senin
            $endDate = Carbon::now()->endOfWeek(); // Minggu

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

            for ($i = 0; $i < 7; $i++) {
                $date = Carbon::now()->startOfWeek()->addDays($i);
                $dateStr = $date->format('Y-m-d');
                $labels[] = $date->translatedFormat('l'); // Hari (Senin, Selasa...)
                $data[] = $salesData->get($dateStr, 0);
            }
        } else {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();

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

            $daysInMonth = Carbon::now()->daysInMonth;
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $date = Carbon::now()->startOfMonth()->addDays($i - 1);
                $dateStr = $date->format('Y-m-d');
                $labels[] = $date->format('d M');
                $data[] = $salesData->get($dateStr, 0);
            }
        }

        if ($request->ajax() && $request->has('fetch_chart')) {
            return response()->json([
                'labels' => $labels,
                'data' => $data,
            ]);
        }

        $filterWaktuTenant = $request->query('filter_waktu_tenant', 'bulan_ini');
        $filterWaktuKantin = $request->query('filter_waktu_kantin', 'bulan_ini');

        // Top 5 Tenants by Sales
        $topTenantsQuery = Tenant::with('kantin')
            ->where('status', 'aktif')
            ->withSum(['orders' => function ($query) use ($filterWaktuTenant, $currentMonth, $currentYear) {
                $query->where('orders.payment_status', 'success')
                      ->where('orders.order_status', 'selesai');
                if ($filterWaktuTenant === 'bulan_ini') {
                    $query->whereMonth('orders.created_at', $currentMonth)
                          ->whereYear('orders.created_at', $currentYear);
                }
            }], 'total_price')
            ->withCount(['orders' => function ($query) use ($filterWaktuTenant, $currentMonth, $currentYear) {
                $query->where('orders.payment_status', 'success')
                      ->where('orders.order_status', 'selesai');
                if ($filterWaktuTenant === 'bulan_ini') {
                    $query->whereMonth('orders.created_at', $currentMonth)
                          ->whereYear('orders.created_at', $currentYear);
                }
            }]);

        if ($kantinId) {
            $topTenantsQuery->where('kantin_id', $kantinId);
        }

        $topTenants = $topTenantsQuery
            ->orderByRaw('orders_sum_total_price DESC NULLS LAST')
            ->take(5)
            ->get();

        // Kantin Teramai
        $kantinTeramaiQuery = Kantin::where('status', 'aktif')
            ->withSum(['orders' => function ($query) use ($filterWaktuKantin, $currentMonth, $currentYear) {
                $query->where('orders.payment_status', 'success')
                      ->where('orders.order_status', 'selesai');
                if ($filterWaktuKantin === 'bulan_ini') {
                    $query->whereMonth('orders.created_at', $currentMonth)
                          ->whereYear('orders.created_at', $currentYear);
                }
            }], 'total_price');
            
        if ($kantinId) {
            $kantinTeramaiQuery->where('id', $kantinId);
        }

        $kantinTeramai = $kantinTeramaiQuery
            ->orderByRaw('orders_sum_total_price DESC NULLS LAST')
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
