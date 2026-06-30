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
            ->where('payment_status', 'success')
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
            'order_status' => 'required|in:belum_diproses,diproses,siap_diambil,selesai',
            'order_type' => 'nullable|in:dine-in,takeaway'
        ]);

        $tenantId = auth()->user()->tenant->id;
        $order = Order::where('tenant_id', $tenantId)->findOrFail($id);

        $updateData = ['order_status' => $request->order_status];
        
        if ($request->has('order_type')) {
            $updateData['order_type'] = $request->order_type;
            if ($request->order_type == 'takeaway') {
                $updateData['table_number'] = null;
            }
        }

        $order->update($updateData);

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}
