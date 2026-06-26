<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Kantin;
use App\Models\Tenant;
use App\Models\Order;
use App\Models\PencairanDana;

class DataController extends Controller
{
    public function kantin()
    {
        $kantins = Kantin::latest()->paginate(10);
        return view('superadmin.kantin.index', compact('kantins'));
    }

    public function tenant()
    {
        $tenants = Tenant::with('kantin', 'user')->latest()->paginate(10);
        return view('superadmin.tenant.index', compact('tenants'));
    }

    public function orders(Request $request)
    {
        $query = Order::with('tenant', 'user', 'items')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('order_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('tenant', function($q) use ($search) {
                      $q->where('nama_tenant', 'like', "%{$search}%");
                  });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('order_status', $request->status);
        }

        $orders = $query->paginate(15)->withQueryString();
        return view('superadmin.orders.index', compact('orders'));
    }

    public function showOrder(Order $order)
    {
        $order->load(['tenant', 'user', 'items.menu']);
        return view('superadmin.orders.show', compact('order'));
    }

    public function pencairan()
    {
        $pencairans = PencairanDana::with('tenant')->latest()->paginate(15);
        return view('superadmin.pencairan.index', compact('pencairans'));
    }
}
