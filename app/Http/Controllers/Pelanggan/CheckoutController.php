<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
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

        // Hitung total dan group per tenant
        $totalPrice = 0;
        $tenantOrders = [];
        
        foreach ($cart as $item) {
            $tenantId = $item['tenant_id'];
            if (!isset($tenantOrders[$tenantId])) {
                $tenantOrders[$tenantId] = [
                    'total_price' => 0,
                    'items' => []
                ];
            }
            $tenantOrders[$tenantId]['total_price'] += ($item['quantity'] * $item['harga']);
            $tenantOrders[$tenantId]['items'][] = $item;
            $totalPrice += ($item['quantity'] * $item['harga']);
        }

        // Generate Order ID (Satu ID transaksi Midtrans untuk semua tenant ini)
        $orderId = 'ORD-' . time() . '-' . rand(1000, 9999);

        // Save Order(s) to Database
        foreach ($tenantOrders as $tenantId => $tData) {
            $order = Order::create([
                'order_id'     => $orderId,
                'user_id'      => auth()->id(),
                'tenant_id'    => $tenantId,
                'total_price'  => $tData['total_price'],
                'payment_status'=> 'pending',
                'order_status' => 'belum_diproses',
                'order_type'   => $orderType,
                'table_number' => $tableNumber,
                'pickup_pin'   => str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT),
            ]);

            // Save Order Items untuk tenant ini
            foreach ($tData['items'] as $item) {
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
            
            // Save snap token to all created orders
            Order::where('order_id', $orderId)->update(['snap_token' => $snapToken]);

            // Clear Cart from session
            session()->forget('cart');

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $orderId
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
            $orders = Order::where('order_id', $request->order_id)->get();
            
            if ($orders->count() > 0) {
                if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                    Order::where('order_id', $request->order_id)->update([
                        'payment_status' => 'success',
                        'payment_type' => $request->payment_type
                    ]);
                } else if ($request->transaction_status == 'expire' || $request->transaction_status == 'cancel' || $request->transaction_status == 'deny') {
                    Order::where('order_id', $request->order_id)->update(['payment_status' => 'failed']);
                }
            }
        }

        return response()->json(['success' => true]);
    }

    public function successLocal(Request $request)
    {
        // Simulates the Midtrans Webhook for localhost testing
        $orders = Order::where('order_id', $request->order_id)->get();
        if ($orders->count() > 0) {
            Order::where('order_id', $request->order_id)->update([
                'payment_status' => 'success',
                'payment_type' => $request->payment_type ?? 'qris'
            ]);
        }
        return response()->json(['success' => true]);
    }
}
