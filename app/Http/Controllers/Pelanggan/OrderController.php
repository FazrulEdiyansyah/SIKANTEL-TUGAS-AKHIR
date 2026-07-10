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

        return back()->with('success', 'Pesanan berhasil dibatalkan.');
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
}
