<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Menu;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $tenant = Tenant::with('kantin')->where('user_id', Auth::id())->firstOrFail();
        
        $today = Carbon::today();

        // Data Menu
        $totalMenu = Menu::where('tenant_id', $tenant->id)->count();
        $menuTersedia = Menu::where('tenant_id', $tenant->id)->where('status', 'tersedia')->count();
        $menuHabis = Menu::where('tenant_id', $tenant->id)->where('status', 'habis')->count();

        // Data Pesanan & Penjualan Hari Ini
        $pesananHariIni = Order::where('tenant_id', $tenant->id)
            ->whereDate('created_at', $today)
            ->where('payment_status', 'success')
            ->count();

        $menungguDiproses = Order::where('tenant_id', $tenant->id)
            ->where('payment_status', 'success')
            ->where('order_status', 'belum_diproses')
            ->count();

        $sedangDisiapkan = Order::where('tenant_id', $tenant->id)
            ->where('payment_status', 'success')
            ->where('order_status', 'diproses')
            ->count();

        $penjualanHariIni = Order::where('tenant_id', $tenant->id)
            ->whereDate('created_at', $today)
            ->where('payment_status', 'success')
            ->where('order_status', 'selesai')
            ->sum('total_price');

        $pesananSelesaiHariIni = Order::where('tenant_id', $tenant->id)
            ->whereDate('created_at', $today)
            ->where('payment_status', 'success')
            ->where('order_status', 'selesai')
            ->count();

        // Daftar Pesanan Terbaru
        $pesananTerbaru = Order::with(['user', 'items.menu'])
            ->where('tenant_id', $tenant->id)
            ->where('payment_status', 'success')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('tenant.dashboard', compact(
            'tenant', 
            'totalMenu', 
            'menuTersedia', 
            'menuHabis',
            'pesananHariIni',
            'menungguDiproses',
            'sedangDisiapkan',
            'penjualanHariIni',
            'pesananSelesaiHariIni',
            'pesananTerbaru'
        ));
    }

    public function toggleStoreStatus(Request $request)
    {
        $tenant = Tenant::where('user_id', Auth::id())->firstOrFail();
        
        // Toggle the is_open boolean
        $tenant->is_open = !$tenant->is_open;
        $tenant->save();

        $statusMessage = $tenant->is_open ? 'Toko berhasil dibuka!' : 'Toko berhasil ditutup sementara!';
        return redirect()->route('tenant.dashboard')->with('success', $statusMessage);
    }
}
