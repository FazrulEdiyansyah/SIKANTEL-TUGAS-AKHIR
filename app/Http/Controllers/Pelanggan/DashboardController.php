<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kantin;
use App\Models\Tenant;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $search = $request->input('search');
        
        $kantins = Kantin::where('status', 'aktif')
            ->when($search, function ($query, $search) {
                return $query->where('nama_kantin', 'ilike', "%{$search}%");
            })
            ->get();
            
        return view('pelanggan.dashboard', compact('kantins'));
    }

    public function showKantin(Kantin $kantin, Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $tenants = $kantin->tenants()
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('status', 'aktif')
            ->when($search, function ($query, $search) {
                return $query->where('nama_tenant', 'ilike', "%{$search}%");
            })
            ->when($status !== null, function ($query) use ($status) {
                if ($status === '1' || $status === '0') {
                    return $query->where('is_open', $status);
                }
                return $query;
            })
            ->orderBy('is_open', 'desc')
            ->get();

        return view('pelanggan.kantin.show', compact('kantin', 'tenants'));
    }

    public function showTenant(Tenant $tenant, Request $request)
    {
        $tenant->load(['kantin']);
        $tenant->loadAvg('reviews', 'rating');
        $tenant->loadCount('reviews');

        $search = $request->input('search');

        $menus = $tenant->menus()
            ->when($search, function ($query, $search) {
                return $query->where('nama_menu', 'ilike', "%{$search}%");
            })
            ->orderByRaw("CASE WHEN status = 'tersedia' THEN 1 ELSE 0 END DESC")
            ->get();

        return view('pelanggan.tenant.show', compact('tenant', 'menus'));
    }
}
