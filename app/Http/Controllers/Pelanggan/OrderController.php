<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['tenant', 'items.menu'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pelanggan.orders.index', compact('orders'));
    }

    public function show($orderId)
    {
        $orders = Order::with(['tenant', 'items.menu', 'review'])
            ->where('user_id', auth()->id())
            ->where('order_id', $orderId)
            ->get();

        if ($orders->isEmpty()) {
            abort(404);
        }

        return view('pelanggan.orders.show', compact('orders', 'orderId'));
    }

    public function cancel($orderId)
    {
        $orders = Order::where('user_id', auth()->id())
            ->where('payment_status', 'pending')
            ->where('order_id', $orderId)
            ->get();

        if ($orders->isEmpty()) {
            abort(404);
        }

        // Setup Midtrans
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);

        try {
            // Cancel transaction in Midtrans
            \Midtrans\Transaction::cancel($orderId);
        } catch (\Exception $e) {
            // It might already be expired/cancelled on Midtrans side, ignore error
            \Illuminate\Support\Facades\Log::error('Midtrans cancel error: ' . $e->getMessage());
        }

        // Cancel ALL orders that share this transaction ID
        Order::where('order_id', $orderId)->update(['payment_status' => 'failed']);

        return redirect()->route('pelanggan.orders.index')->with('success', 'Pesanan berhasil dibatalkan.');
    }

    public function statusAPI($orderId)
    {
        $orders = Order::where('user_id', auth()->id())
            ->where('order_id', $orderId)
            ->get();
            
        if ($orders->isEmpty()) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $statuses = [];
        foreach ($orders as $order) {
            $statuses[$order->id] = [
                'order_status' => $order->order_status,
                'payment_status' => $order->payment_status
            ];
        }

        return response()->json([
            'payment_status' => $orders->first()->payment_status,
            'tenant_statuses' => $statuses
        ]);
    }

    public function reorder($orderId)
    {
        $orders = Order::with('items.menu.tenant')->where('user_id', auth()->id())->where('order_id', $orderId)->get();
        
        if ($orders->isEmpty()) abort(404);

        $newCart = [];

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $newKey = uniqid();
                $newCart[$newKey] = [
                    'id'               => $newKey,
                    'menu_id'          => $item->menu_id,
                    'tenant_id'        => $order->tenant_id,
                    'kantin_id'        => $item->menu && $item->menu->tenant ? $item->menu->tenant->kantin_id : null,
                    'nama_menu'        => $item->nama_menu,
                    'harga'            => $item->harga,
                    'quantity'         => $item->quantity,
                    'foto'             => $item->menu ? $item->menu->foto : null,
                    'selected_options' => is_string($item->selected_options) ? json_decode($item->selected_options, true) : $item->selected_options,
                    'catatan'          => $item->catatan,
                ];
            }
        }
        
        // Simpan ke session cart (replace cart lama)
        session(['cart' => $newCart]);
        
        return redirect()->route('pelanggan.checkout')->with('success', 'Keranjang berhasil diperbarui dari pesanan sebelumnya!');
    }

    public function pay($orderId)
    {
        $orders = Order::where('user_id', auth()->id())
            ->where('payment_status', 'pending')
            ->where('order_id', $orderId)
            ->get();

        if ($orders->isEmpty()) abort(404);

        $firstOrder = $orders->first();

        if ($firstOrder->snap_token) {
            return back()->with('auto_trigger_snap', $firstOrder->snap_token);
        }

        // Setup Midtrans
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $totalPrice = $orders->sum('total_price');

        $params = array(
            'transaction_details' => array(
                'order_id' => $orderId,
                'gross_amount' => $totalPrice,
            ),
            'customer_details' => array(
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ),
        );

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            Order::where('order_id', $orderId)->update(['snap_token' => $snapToken]);
            return back()->with('auto_trigger_snap', $snapToken);
        } catch (\Exception $e) {
            // Jika Midtrans menolak order_id karena duplikat atau alasan lain, buat order_id baru
            $newOrderId = 'ORD-' . time() . '-' . rand(1000, 9999);
            Order::where('order_id', $orderId)->update(['order_id' => $newOrderId]);
            
            $params['transaction_details']['order_id'] = $newOrderId;
            try {
                $snapToken = \Midtrans\Snap::getSnapToken($params);
                Order::where('order_id', $newOrderId)->update(['snap_token' => $snapToken]);
                return back()->with('auto_trigger_snap', $snapToken);
            } catch (\Exception $e2) {
                \Illuminate\Support\Facades\Log::error('Midtrans Retry Error: ' . $e2->getMessage());
                return back()->with('error', 'Gagal memproses pembayaran dengan Midtrans. Coba beberapa saat lagi.');
            }
        }
    }
}
