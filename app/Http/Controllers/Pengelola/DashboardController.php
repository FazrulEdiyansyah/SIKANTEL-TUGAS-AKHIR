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
    public function dashboard()
    {
        $totalKantin = Cache::remember('pengelola_total_kantin', 300, fn() => Kantin::count());
        $totalTenant = Cache::remember('pengelola_total_tenant', 300, fn() => Tenant::count());
        
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $penjualanBulanIni = Cache::remember("pengelola_penjualan_{$currentMonth}_{$currentYear}", 300, function () use ($currentMonth, $currentYear) {
            return Order::where('payment_status', 'success')
                ->where('order_status', 'selesai')
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum('total_price');
        });

        // Data for Chart (Last 30 days)
        $chartData = Cache::remember('pengelola_chart_data_30days', 300, function () {
            $labels = [];
            $data = [];
            for ($i = 29; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $labels[] = $date->format('d M');
                
                $dailyTotal = Order::whereDate('created_at', $date)
                    ->where('payment_status', 'success')
                    ->where('order_status', 'selesai')
                    ->sum('total_price');
                    
                $data[] = $dailyTotal;
            }
            return ['labels' => $labels, 'data' => $data];
        });
        
        $labels = $chartData['labels'];
        $data = $chartData['data'];

        // Top 5 Tenants by Sales
        $topTenants = Cache::remember('pengelola_top_tenants', 300, function () {
            return Tenant::with('kantin')
                ->withSum(['orders' => function ($query) {
                    $query->where('orders.payment_status', 'success')
                          ->where('orders.order_status', 'selesai');
                }], 'total_price')
                ->withCount(['orders' => function ($query) {
                    $query->where('orders.payment_status', 'success')
                          ->where('orders.order_status', 'selesai');
                }])
                ->orderByDesc('orders_sum_total_price')
                ->take(5)
                ->get();
        });

        // Kantin Teramai (by completed orders)
        $kantinTeramai = Cache::remember('pengelola_kantin_teramai', 300, function () {
            return Kantin::withCount(['orders' => function ($query) {
                    $query->where('orders.payment_status', 'success')
                          ->where('orders.order_status', 'selesai');
                }])
                ->orderByDesc('orders_count')
                ->first();
        });

        return view('pengelola.dashboard', compact(
            'totalKantin', 
            'totalTenant', 
            'penjualanBulanIni',
            'labels',
            'data',
            'topTenants',
            'kantinTeramai'
        ));
    }

    public function kantinIndex()
    {
        return view('pengelola.kantin.index');
    }
}
