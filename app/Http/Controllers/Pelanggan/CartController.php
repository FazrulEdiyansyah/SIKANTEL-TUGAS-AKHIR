<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;

class CartController extends Controller
{
    private function getCart()
    {
        return session('cart', []);
    }

    private function saveCart($cart)
    {
        session(['cart' => $cart]);
    }

    public function index()
    {
        $cart = $this->getCart();
        $tenantId = null;
        $estMin = 10;
        $estMax = 15;
        $estStartStr = '';
        $estEndStr = '';
        $activeOrders = 0;

        if (count($cart) > 0) {
            $firstItem = reset($cart);
            $tenantId = $firstItem['tenant_id'] ?? null;

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
        if (count($cart) > 0) {
            $firstItem = reset($cart);
            if (isset($firstItem['kantin_id']) && $firstItem['kantin_id'] != $menu->tenant->kantin_id) {
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
            foreach ($request->custom_options as $sIndex => $oData) {
                if (is_array($oData)) {
                    foreach ($oData as $oIndex => $qty) {
                        if ($qty > 0 && isset($menu->customizations[$sIndex]['options'][$oIndex])) {
                            $section = $menu->customizations[$sIndex];
                            $option = $section['options'][$oIndex];
                            
                            $selectedOptions[] = [
                                'label' => $section['name'],
                                'value' => $option['name'],
                                'qty' => (int)$qty,
                                'price' => (int)($option['price_adjustment'] ?? 0)
                            ];
                            $extraPrice += ((int)($option['price_adjustment'] ?? 0) * (int)$qty);
                        }
                    }
                } else {
                    $oIndex = $oData;
                    if (isset($menu->customizations[$sIndex]['options'][$oIndex])) {
                        $section = $menu->customizations[$sIndex];
                        $option = $section['options'][$oIndex];
                        
                        $selectedOptions[] = [
                            'label' => $section['name'],
                            'value' => $option['name'],
                            'qty' => 1,
                            'price' => (int)($option['price_adjustment'] ?? 0)
                        ];
                        $extraPrice += (int) ($option['price_adjustment'] ?? 0);
                    }
                }
            }
        }

        $existingKey = null;
        foreach ($cart as $key => $item) {
            if (
                $item['menu_id'] == $menu->id && 
                $item['catatan'] == $request->catatan &&
                json_encode($item['selected_options']) === json_encode($selectedOptions)
            ) {
                $existingKey = $key;
                break;
            }
        }

        if ($existingKey !== null) {
            $cart[$existingKey]['quantity'] += $request->quantity;
        } else {
            $newKey = uniqid();
            $cart[$newKey] = [
                'id'               => $newKey,
                'menu_id'          => $menu->id,
                'tenant_id'        => $menu->tenant_id,
                'kantin_id'        => $menu->tenant->kantin_id,
                'nama_menu'        => $menu->nama_menu,
                'harga'            => $menu->harga + $extraPrice,
                'quantity'         => (int) $request->quantity,
                'foto'             => $menu->foto,
                'selected_options' => $selectedOptions,
                'catatan'          => $request->catatan,
            ];
        }

        $this->saveCart($cart);

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
        $qtyToDecrease = $request->quantity ?? 1;
        $foundKey = null;

        // Cari item pertama dengan menu_id yang cocok (seperti behavior sebelumnya)
        foreach ($cart as $key => $item) {
            if ($item['menu_id'] == $request->menu_id) {
                $foundKey = $key;
                break;
            }
        }

        if ($foundKey !== null) {
            if ($cart[$foundKey]['quantity'] > $qtyToDecrease) {
                $cart[$foundKey]['quantity'] -= $qtyToDecrease;
            } else {
                unset($cart[$foundKey]);
            }
            $this->saveCart($cart);
        }

        if ($request->wantsJson()) {
            return $this->getCartResponse();
        }

        return redirect()->back();
    }

    public function updateQuantity(Request $request)
    {
        $request->validate([
            'cart_key' => 'required|string',
            'quantity' => 'required|integer|min:0'
        ]);

        $cart = $this->getCart();
        $key = $request->cart_key;

        if (isset($cart[$key])) {
            if ($request->quantity <= 0) {
                unset($cart[$key]);
            } else {
                $cart[$key]['quantity'] = $request->quantity;
            }
            $this->saveCart($cart);
        }

        if ($request->wantsJson()) {
            return $this->getCartResponse();
        }

        return redirect()->back();
    }

    public function updateNote(Request $request)
    {
        $request->validate([
            'cart_key' => 'required|string',
            'catatan' => 'nullable|string|max:200'
        ]);

        $cart = $this->getCart();
        $key = $request->cart_key;

        if (isset($cart[$key])) {
            $item = $cart[$key];
            
            // Cek apakah ada item lain dengan menu, opsi, dan catatan baru yang sama
            $existingKey = null;
            foreach ($cart as $otherKey => $otherItem) {
                if (
                    $otherKey != $key &&
                    $otherItem['menu_id'] == $item['menu_id'] &&
                    $otherItem['catatan'] == $request->catatan &&
                    json_encode($otherItem['selected_options']) === json_encode($item['selected_options'])
                ) {
                    $existingKey = $otherKey;
                    break;
                }
            }

            if ($existingKey !== null) {
                $cart[$existingKey]['quantity'] += $item['quantity'];
                unset($cart[$key]);
            } else {
                $cart[$key]['catatan'] = $request->catatan;
            }
            $this->saveCart($cart);
        }

        if ($request->wantsJson()) {
            return $this->getCartResponse();
        }

        return redirect()->back();
    }

    public function remove(Request $request)
    {
        $request->validate([
            'cart_key' => 'required|string'
        ]);

        $cart = $this->getCart();
        if (isset($cart[$request->cart_key])) {
            unset($cart[$request->cart_key]);
            $this->saveCart($cart);
        }

        if ($request->wantsJson()) {
            return $this->getCartResponse();
        }

        return redirect()->back();
    }

    private function getCartResponse()
    {
        $cart = $this->getCart();
        
        $totalQty = 0;
        $totalPrice = 0;
        $menuQty = [];
        $itemNames = [];
        
        $formattedCart = [];

        foreach ($cart as $key => $item) {
            $totalQty += $item['quantity'];
            $totalPrice += ($item['quantity'] * $item['harga']);
            $itemNames[] = $item['nama_menu'];
            
            if (!isset($menuQty[$item['menu_id']])) {
                $menuQty[$item['menu_id']] = 0;
            }
            $menuQty[$item['menu_id']] += $item['quantity'];
            
            $formattedCart[$key] = [
                'id' => $key,
                'menu_id' => $item['menu_id'],
                'nama_menu' => $item['nama_menu'],
                'harga' => $item['harga'],
                'quantity' => $item['quantity'],
                'foto' => $item['foto'],
                'selected_options' => $item['selected_options'],
                'catatan' => $item['catatan'],
                'tenant_id' => $item['tenant_id'],
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

