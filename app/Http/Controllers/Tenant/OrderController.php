<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant->id;

        $orders = Order::with(['user', 'items.menu'])
            ->where('tenant_id', $tenantId)
            ->whereIn('status', ['success', 'preparing', 'ready', 'completed'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('tenant.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $tenantId = auth()->user()->tenant->id;
        
        $order = Order::with(['user', 'items.menu'])
            ->where('tenant_id', $tenantId)
            ->findOrFail($id);

        return view('tenant.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:preparing,ready,completed'
        ]);

        $tenantId = auth()->user()->tenant->id;
        $order = Order::where('tenant_id', $tenantId)->findOrFail($id);

        $order->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}
