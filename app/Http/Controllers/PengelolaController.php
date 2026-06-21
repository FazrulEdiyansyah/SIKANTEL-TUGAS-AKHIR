<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kantin;
use App\Models\Tenant;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PengelolaController extends Controller
{
    public function dashboard()
    {
        $totalKantin = Kantin::count();
        $totalTenant = Tenant::count();
        
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $penjualanBulanIni = Order::where('status', 'completed')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('total_price');

        // Data for Chart (Last 30 days)
        $labels = [];
        $data = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('d M');
            
            $dailyTotal = Order::whereDate('created_at', $date)
                ->where('status', 'completed')
                ->sum('total_price');
                
            $data[] = $dailyTotal;
        }

        // Top 5 Tenants by Sales
        $topTenants = Tenant::with('kantin')
            ->withSum(['orders' => function ($query) {
                $query->where('orders.status', 'completed');
            }], 'total_price')
            ->withCount(['orders' => function ($query) {
                $query->where('orders.status', 'completed');
            }])
            ->orderByDesc('orders_sum_total_price')
            ->take(5)
            ->get();

        // Kantin Teramai (by completed orders)
        $kantinTeramai = Kantin::withCount(['orders' => function ($query) {
                $query->where('orders.status', 'completed');
            }])
            ->orderByDesc('orders_count')
            ->first();

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
