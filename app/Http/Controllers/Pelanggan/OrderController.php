<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['tenant', 'items'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pelanggan.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with(['tenant', 'items'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return view('pelanggan.orders.show', compact('order'));
    }

    public function cancel($id)
    {
        $order = Order::where('user_id', auth()->id())
            ->where('payment_status', 'pending')
            ->findOrFail($id);

        // Setup Midtrans
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);

        try {
            // Cancel transaction in Midtrans
            \Midtrans\Transaction::cancel($order->order_id);
        } catch (\Exception $e) {
            // It might already be expired/cancelled on Midtrans side, ignore error
            \Illuminate\Support\Facades\Log::error('Midtrans cancel error: ' . $e->getMessage());
        }

        $order->update(['payment_status' => 'failed']);

        return back()->with('success', 'Pesanan berhasil dibatalkan.');
    }
}
