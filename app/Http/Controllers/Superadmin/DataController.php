<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Order;

class DataController extends Controller
{
    public function orders(Request $request)
    {
        $query = Order::with('tenant', 'user', 'items')->latest();

        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = strtolower($request->search);
            $q->where(function ($sub) use ($search) {
                $sub->whereRaw('LOWER(order_id) LIKE ?', ["%{$search}%"])
                    ->orWhereHas('user', function($q) use ($search) {
                        $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                    })
                    ->orWhereHas('tenant', function($q) use ($search) {
                        $q->whereRaw('LOWER(nama_tenant) LIKE ?', ["%{$search}%"]);
                    });
            });
        });

        $query->when($request->filled('status') && $request->status !== 'all', function ($q) use ($request) {
            $q->where('order_status', $request->status);
        });

        $orders = $query->paginate(10)->withQueryString();
        return view('superadmin.orders.index', compact('orders'));
    }

    public function showOrder(Order $order)
    {
        $order->load(['tenant', 'user', 'items.menu']);
        return view('superadmin.orders.show', compact('order'));
    }
}

