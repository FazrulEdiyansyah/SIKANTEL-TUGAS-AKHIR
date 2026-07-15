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

    public function updateTable(Request $request, $id)
    {
        $order = Order::where('user_id', auth()->id())->findOrFail($id);

        if ($request->action == 'takeaway') {
            $order->update([
                'order_type' => 'takeaway',
                'table_number' => null
            ]);
            return back()->with('success', 'Pesanan berhasil diubah menjadi Bawa Pulang / Ambil Sendiri.');
        } else {
            $request->validate([
                'table_number' => 'required|string|max:50'
            ]);
            $order->update([
                'table_number' => $request->table_number
            ]);
            return back()->with('success', 'Nomor meja berhasil disimpan.');
        }
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

        // Cancel ALL orders that share this transaction ID
        Order::where('order_id', $order->order_id)->update(['payment_status' => 'failed']);

        return redirect()->route('pelanggan.orders.index')->with('success', 'Pesanan berhasil dibatalkan.');
    }

    public function statusAPI($id)
    {
        $order = Order::where('user_id', auth()->id())->findOrFail($id);
        return response()->json([
            'order_status' => $order->order_status,
            'payment_status' => $order->payment_status
        ]);
    }

    public function reorder($id)
    {
        $order = Order::with('items.menu')->where('user_id', auth()->id())->findOrFail($id);
        
        $cart = \App\Models\Cart::firstOrCreate(['user_id' => auth()->id()]);
        
        // Hapus isi keranjang sebelumnya
        $cart->items()->delete();

        foreach ($order->items as $item) {
            \App\Models\CartItem::create([
                'cart_id' => $cart->id,
                'menu_id' => $item->menu_id,
                'tenant_id' => $order->tenant_id,
                'nama_menu' => $item->nama_menu,
                'harga' => $item->harga,
                'quantity' => $item->quantity,
                'foto' => $item->menu ? $item->menu->foto : null,
                'selected_options' => is_string($item->selected_options) ? json_decode($item->selected_options, true) : $item->selected_options,
                'catatan' => $item->catatan,
            ]);
        }
        
        return redirect()->route('pelanggan.checkout')->with('success', 'Keranjang berhasil diperbarui dari pesanan sebelumnya!');
    }

    public function pay($id)
    {
        $order = Order::where('user_id', auth()->id())
            ->where('payment_status', 'pending')
            ->findOrFail($id);

        if ($order->snap_token) {
            return back()->with('auto_trigger_snap', $order->snap_token);
        }

        // Setup Midtrans
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $totalPrice = Order::where('order_id', $order->order_id)->sum('total_price');

        $params = array(
            'transaction_details' => array(
                'order_id' => $order->order_id,
                'gross_amount' => $totalPrice,
            ),
            'customer_details' => array(
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ),
        );

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            Order::where('order_id', $order->order_id)->update(['snap_token' => $snapToken]);
            return back()->with('auto_trigger_snap', $snapToken);
        } catch (\Exception $e) {
            // Jika Midtrans menolak order_id karena duplikat atau alasan lain, buat order_id baru
            $newOrderId = 'ORD-' . time() . '-' . rand(1000, 9999);
            Order::where('order_id', $order->order_id)->update(['order_id' => $newOrderId]);
            
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
