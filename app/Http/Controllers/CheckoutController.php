<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function process(Request $request)
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return response()->json(['success' => false, 'message' => 'Keranjang kosong']);
        }

        $orderType = $request->order_type ?? 'dine-in';
        $tableNumber = $request->table_number ?? null;

        // Hitung total
        $totalPrice = 0;
        $tenantId = null;
        foreach ($cart as $item) {
            $totalPrice += ($item['quantity'] * $item['harga']);
            $tenantId = $item['tenant_id']; // assuming 1 cart = 1 tenant for now
        }

        // Generate Order ID (Misal: ORD-169876543-1234)
        $orderId = 'ORD-' . time() . '-' . rand(1000, 9999);

        // Save Order to Database
        $order = Order::create([
            'order_id'     => $orderId,
            'user_id'      => auth()->id(),
            'tenant_id'    => $tenantId,
            'total_price'  => $totalPrice,
            'payment_status'=> 'pending',
            'order_status' => 'belum_diproses',
            'order_type'   => $orderType,
            'table_number' => $tableNumber,
        ]);

        // Save Order Items
        foreach ($cart as $item) {
            // item.selected_options is usually a string in old sessions, array in new ones
            // the database can store json, so we json_encode if it's array
            $options = $item['selected_options'] ?? null;
            if (is_array($options)) {
                $options = json_encode($options);
            }

            OrderItem::create([
                'order_id'         => $order->id,
                'menu_id'          => $item['menu_id'],
                'nama_menu'        => $item['nama_menu'],
                'quantity'         => $item['quantity'],
                'harga'            => $item['harga'],
                'selected_options' => $options,
                'catatan'          => $item['catatan'] ?? null,
            ]);
        }

        // Setup Midtrans
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

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
            // Get Snap Payment Page URL
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            
            // Save snap token to order
            $order->update(['snap_token' => $snapToken]);

            // Clear Cart from session
            session()->forget('cart');

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $order->id
            ]);
            
        } catch (\Exception $e) {
            Log::error('Midtrans Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses pembayaran.'
            ], 500);
        }
    }

    public function callback(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {
            $order = Order::where('order_id', $request->order_id)->first();
            
            if ($order) {
                if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                    $order->update([
                        'payment_status' => 'success',
                        'payment_type' => $request->payment_type
                    ]);
                } else if ($request->transaction_status == 'expire' || $request->transaction_status == 'cancel' || $request->transaction_status == 'deny') {
                    $order->update(['payment_status' => 'failed']);
                }
            }
        }

        return response()->json(['success' => true]);
    }

    public function successLocal(Request $request)
    {
        // Simulates the Midtrans Webhook for localhost testing
        $order = Order::find($request->order_id);
        if ($order) {
            $order->update([
                'payment_status' => 'success',
                'payment_type' => $request->payment_type ?? 'qris'
            ]);
        }
        return response()->json(['success' => true]);
    }
}
