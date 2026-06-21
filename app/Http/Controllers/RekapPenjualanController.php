<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kantin;

class RekapPenjualanController extends Controller
{
    public function index(Request $request)
    {
        $query = Kantin::withCount('tenants');
        
        if ($request->has('search') && $request->search != '') {
            $query->where('nama_kantin', 'like', '%' . $request->search . '%');
        }

        $kantins = $query->get();
        
        return view('pengelola.rekap-penjualan.index', compact('kantins'));
    }

    public function show(Kantin $kantin, Request $request)
    {
        $query = $kantin->tenants();
        
        if ($request->has('search') && $request->search != '') {
            $query->where('nama_tenant', 'like', '%' . $request->search . '%');
        }

        $tenants = $query->get();
        
        return view('pengelola.rekap-penjualan.show', compact('kantin', 'tenants'));
    }
}
