<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kantin;
use App\Models\Tenant;

class PelangganController extends Controller
{
    public function dashboard(Request $request)
    {
        $search = $request->input('search');
        
        $kantins = Kantin::where('status', 'aktif')
            ->when($search, function ($query, $search) {
                return $query->where('nama_kantin', 'like', "%{$search}%");
            })
            ->get();
            
        return view('pelanggan.dashboard', compact('kantins'));
    }

    public function showKantin(Kantin $kantin, Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $tenants = $kantin->tenants()
            ->where('status', 'aktif')
            ->when($search, function ($query, $search) {
                return $query->where('nama_tenant', 'like', "%{$search}%");
            })
            ->when($status !== null, function ($query) use ($status) {
                if ($status === '1' || $status === '0') {
                    return $query->where('is_open', $status);
                }
                return $query;
            })
            ->get();

        return view('pelanggan.kantin.show', compact('kantin', 'tenants'));
    }

    public function showTenant(Tenant $tenant, Request $request)
    {
        $tenant->load('kantin');

        $search = $request->input('search');

        $menus = $tenant->menus()
            ->when($search, function ($query, $search) {
                return $query->where('nama_menu', 'like', "%{$search}%");
            })
            ->get();

        return view('pelanggan.tenant.show', compact('tenant', 'menus'));
    }
}
