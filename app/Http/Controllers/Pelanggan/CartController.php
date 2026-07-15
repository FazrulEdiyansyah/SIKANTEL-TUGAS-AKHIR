<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Cart;
use App\Models\CartItem;

class CartController extends Controller
{
    private function getCart()
    {
        return Cart::firstOrCreate(['user_id' => auth()->id()]);
    }

    public function index()
    {
        $cartModel = Cart::with('items.tenant')->where('user_id', auth()->id())->first();
        $cart = $cartModel ? $cartModel->items->keyBy('id') : collect();
        
        $tenantId = null;
        $estMin = 10;
        $estMax = 15;
        $estStartStr = '';
        $estEndStr = '';
        $activeOrders = 0;

        if ($cart->count() > 0) {
            $firstItem = $cart->first();
            $tenantId = $firstItem->tenant_id ?? null;

            if ($tenantId) {
                $activeOrders = \App\Models\Order::where('tenant_id', $tenantId)
                    ->whereIn('order_status', ['belum_diproses', 'diproses'])
                    ->count();
                
                // Batching: setiap 5 pesanan menambah waktu tunggu sekitar 2-3 menit
                $batchCount = floor($activeOrders / 5);
                $estMin = 10 + ($batchCount * 2);
                $estMax = 15 + ($batchCount * 3);
                
                // Batas maksimal waktu tunggu (cap) psikologis agar tidak terasa terlalu lama
                if ($estMax > 30) {
                    $estMax = 30;
                    $estMin = 25;
                }
                
                $now = \Carbon\Carbon::now();
                $estStartStr = $now->copy()->addMinutes($estMin)->format('H:i');
                $estEndStr = $now->copy()->addMinutes($estMax)->format('H:i');
            }
        }

        return view('pelanggan.checkout.index', compact('cart', 'tenantId', 'estMin', 'estMax', 'estStartStr', 'estEndStr', 'activeOrders'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'menu_id'        => 'required|exists:menus,id',
            'quantity'       => 'required|integer|min:1',
            'custom_options' => 'nullable|array',
            'catatan'        => 'nullable|string|max:200',
        ]);

        $menu = Menu::with('tenant')->findOrFail($request->menu_id);
        $cart = $this->getCart();

        // Cek jika ada item dari kantin berbeda
        $firstItem = $cart->items()->first();
        if ($firstItem) {
            $existingTenant = $firstItem->tenant;
            if ($existingTenant && $existingTenant->kantin_id != $menu->tenant->kantin_id) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Maaf, Anda hanya bisa memesan dari satu kantin dalam satu pesanan. Selesaikan atau kosongkan keranjang Anda terlebih dahulu.'
                    ], 400);
                }
                return redirect()->back()->with('error', 'Maaf, Anda hanya bisa memesan dari satu kantin dalam satu pesanan.');
            }
        }

        $selectedOptions = [];
        $extraPrice = 0;

        if ($request->has('custom_options') && !empty($menu->customizations)) {
            foreach ($request->custom_options as $sIndex => $oIndex) {
                if (isset($menu->customizations[$sIndex]['options'][$oIndex])) {
                    $section = $menu->customizations[$sIndex];
                    $option = $section['options'][$oIndex];
                    
                    $selectedOptions[] = [
                        'label' => $section['name'],
                        'value' => $option['name']
                    ];
                    $extraPrice += (int) ($option['price_adjustment'] ?? 0);
                }
            }
        }

        $existingItem = CartItem::where('cart_id', $cart->id)
            ->where('menu_id', $menu->id)
            ->where('catatan', $request->catatan)
            ->get()
            ->first(function ($item) use ($selectedOptions) {
                return json_encode($item->selected_options) === json_encode($selectedOptions);
            });

        if ($existingItem) {
            $existingItem->quantity += $request->quantity;
            $existingItem->save();
        } else {
            CartItem::create([
                'cart_id'          => $cart->id,
                'menu_id'          => $menu->id,
                'tenant_id'        => $menu->tenant_id,
                'nama_menu'        => $menu->nama_menu,
                'harga'            => $menu->harga + $extraPrice,
                'quantity'         => $request->quantity,
                'foto'             => $menu->foto,
                'selected_options' => $selectedOptions,
                'catatan'          => $request->catatan,
            ]);
        }

        if ($request->wantsJson()) {
            return $this->getCartResponse();
        }

        return redirect()->back()->with('success_cart', 'Menu berhasil ditambahkan ke keranjang!');
    }

    public function decrease(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $cart = $this->getCart();
        $item = CartItem::where('cart_id', $cart->id)->where('menu_id', $request->menu_id)->first();

        $qtyToDecrease = $request->quantity ?? 1;

        if ($item) {
            if ($item->quantity > $qtyToDecrease) {
                $item->quantity -= $qtyToDecrease;
                $item->save();
            } else {
                $item->delete();
            }
        }

        if ($request->wantsJson()) {
            return $this->getCartResponse();
        }

        return redirect()->back();
    }

    public function updateQuantity(Request $request)
    {
        $request->validate([
            'cart_key' => 'required|integer|exists:cart_items,id',
            'quantity' => 'required|integer|min:0'
        ]);

        $cart = $this->getCart();
        $item = CartItem::where('cart_id', $cart->id)->where('id', $request->cart_key)->first();
        
        if ($item) {
            if ($request->quantity <= 0) {
                $item->delete();
            } else {
                $item->quantity = $request->quantity;
                $item->save();
            }
        }

        if ($request->wantsJson()) {
            return $this->getCartResponse();
        }

        return redirect()->back();
    }

    public function updateNote(Request $request)
    {
        $request->validate([
            'cart_key' => 'required|integer|exists:cart_items,id',
            'catatan' => 'nullable|string|max:200'
        ]);

        $cart = $this->getCart();
        $item = CartItem::where('cart_id', $cart->id)->where('id', $request->cart_key)->first();
        
        if ($item) {
            // Check if there is another item with the same menu, options and new note
            $existingItem = CartItem::where('cart_id', $cart->id)
                ->where('menu_id', $item->menu_id)
                ->where('id', '!=', $item->id)
                ->where('catatan', $request->catatan)
                ->get()
                ->first(function ($otherItem) use ($item) {
                    return json_encode($otherItem->selected_options) === json_encode($item->selected_options);
                });

            if ($existingItem) {
                $existingItem->quantity += $item->quantity;
                $existingItem->save();
                $item->delete();
            } else {
                $item->catatan = $request->catatan;
                $item->save();
            }
        }

        if ($request->wantsJson()) {
            return $this->getCartResponse();
        }

        return redirect()->back();
    }

    public function remove(Request $request)
    {
        $request->validate([
            'cart_key' => 'required|integer|exists:cart_items,id'
        ]);

        $cart = $this->getCart();
        CartItem::where('cart_id', $cart->id)->where('id', $request->cart_key)->delete();

        if ($request->wantsJson()) {
            return $this->getCartResponse();
        }

        return redirect()->back();
    }

    private function getCartResponse()
    {
        $cartModel = Cart::with('items')->where('user_id', auth()->id())->first();
        $cartItems = $cartModel ? $cartModel->items : collect();
        
        $totalQty = 0;
        $totalPrice = 0;
        $menuQty = [];
        $itemNames = [];
        
        // Memformat kembali untuk JS yang sudah ada
        $formattedCart = [];

        foreach ($cartItems as $item) {
            $totalQty += $item->quantity;
            $totalPrice += ($item->quantity * $item->harga);
            $itemNames[] = $item->nama_menu;
            
            if (!isset($menuQty[$item->menu_id])) {
                $menuQty[$item->menu_id] = 0;
            }
            $menuQty[$item->menu_id] += $item->quantity;
            
            $formattedCart[$item->id] = [
                'id' => $item->id, // map id to cart_key
                'menu_id' => $item->menu_id,
                'nama_menu' => $item->nama_menu,
                'harga' => $item->harga,
                'quantity' => $item->quantity,
                'foto' => $item->foto,
                'selected_options' => $item->selected_options,
                'catatan' => $item->catatan,
                'tenant_id' => $item->tenant_id,
            ];
        }

        return response()->json([
            'success' => true,
            'totalQty' => $totalQty,
            'totalPrice' => $totalPrice,
            'itemNames' => implode(', ', array_unique($itemNames)),
            'menuQty' => (object) $menuQty, 
            'cart' => (object) $formattedCart
        ]);
    }
}
