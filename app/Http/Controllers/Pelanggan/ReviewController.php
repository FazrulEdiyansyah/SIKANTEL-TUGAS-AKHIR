<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Review;

class ReviewController extends Controller
{
    public function store(Request $request, $orderId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        $order = Order::where('user_id', auth()->id())->findOrFail($orderId);

        // Hanya jika status sudah selesai
        if ($order->order_status !== 'selesai') {
            return back()->with('error', 'Pesanan belum selesai.');
        }

        // Cek jika sudah pernah mereview order ini
        $existing = Review::where('order_id', $orderId)->first();
        if ($existing) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk pesanan ini.');
        }

        Review::create([
            'user_id' => auth()->id(),
            'tenant_id' => $order->tenant_id,
            'order_id' => $order->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Terima kasih atas ulasan Anda!');
    }
}
