<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kantin;

class RekapPenjualanController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = Kantin::withCount('tenants')
            ->withCount(['orders as pesanan_selesai' => function ($query) use ($startDate, $endDate) {
                $query->where('payment_status', 'success')
                      ->where('order_status', 'selesai');
                if ($startDate && $endDate) {
                    $query->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                }
            }])
            ->withSum(['orders as total_penjualan' => function ($query) use ($startDate, $endDate) {
                $query->where('payment_status', 'success')
                      ->where('order_status', 'selesai');
                if ($startDate && $endDate) {
                    $query->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                }
            }], 'total_price');
        
        if ($request->has('search') && $request->search != '') {
            $query->where('nama_kantin', 'like', '%' . $request->search . '%');
        }

        $kantins = $query->get();
        
        return view('pengelola.rekap-penjualan.index', compact('kantins', 'startDate', 'endDate'));
    }

    public function show(Kantin $kantin, Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = $kantin->tenants()
            ->withCount(['orders as pesanan_selesai' => function ($query) use ($startDate, $endDate) {
                $query->where('payment_status', 'success')
                      ->where('order_status', 'selesai');
                if ($startDate && $endDate) {
                    $query->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                }
            }])
            ->withSum(['orders as total_penjualan' => function ($query) use ($startDate, $endDate) {
                $query->where('payment_status', 'success')
                      ->where('order_status', 'selesai');
                if ($startDate && $endDate) {
                    $query->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                }
            }], 'total_price');
        
        if ($request->has('search') && $request->search != '') {
            $query->where('nama_tenant', 'like', '%' . $request->search . '%');
        }

        $tenants = $query->get();
        
        return view('pengelola.rekap-penjualan.show', compact('kantin', 'tenants', 'startDate', 'endDate'));
    }
}
