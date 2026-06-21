<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        
        $tenantId = null;
        if (count($cart) > 0) {
            $firstItem = reset($cart);
            $tenantId = $firstItem['tenant_id'] ?? null;
        }

        return view('pelanggan.checkout.index', compact('cart', 'tenantId'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'menu_id'        => 'required|exists:menus,id',
            'quantity'       => 'required|integer|min:1',
            'custom_options' => 'nullable|array',
            'catatan'        => 'nullable|string|max:200',
        ]);

        $menu = Menu::findOrFail($request->menu_id);
        
        $cart = session()->get('cart', []);

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

        // Generate a unique key based on customizations and notes
        $cartKey = $menu->id . '-' . md5(json_encode($request->custom_options) . ($request->catatan ?? ''));

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $request->quantity;
        } else {
            $cart[$cartKey] = [
                'menu_id'          => $menu->id,
                'nama_menu'        => $menu->nama_menu,
                'harga'            => $menu->harga + $extraPrice,
                'foto'             => $menu->foto,
                'quantity'         => $request->quantity,
                'selected_options' => $selectedOptions,
                'catatan'          => $request->catatan,
                'tenant_id'        => $menu->tenant_id,
            ];
        }

        session()->put('cart', $cart);

        if ($request->wantsJson()) {
            return $this->getCartResponse();
        }

        return redirect()->back()->with('success_cart', 'Menu berhasil ditambahkan ke keranjang!');
    }

    public function decrease(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
        ]);

        $cart = session()->get('cart', []);

        foreach ($cart as $key => $item) {
            if ($item['menu_id'] == $request->menu_id) {
                if ($cart[$key]['quantity'] > 1) {
                    $cart[$key]['quantity'] -= 1;
                } else {
                    unset($cart[$key]);
                }
                break;
            }
        }

        session()->put('cart', $cart);

        if ($request->wantsJson()) {
            return $this->getCartResponse();
        }

        return redirect()->back();
    }

    public function updateQuantity(Request $request)
    {
        $request->validate([
            'cart_key' => 'required|string',
            'action' => 'required|in:increase,decrease'
        ]);

        $cart = session()->get('cart', []);
        
        if (isset($cart[$request->cart_key])) {
            if ($request->action === 'increase') {
                $cart[$request->cart_key]['quantity'] += 1;
            } else {
                if ($cart[$request->cart_key]['quantity'] > 1) {
                    $cart[$request->cart_key]['quantity'] -= 1;
                } else {
                    unset($cart[$request->cart_key]);
                }
            }
            session()->put('cart', $cart);
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

        $cart = session()->get('cart', []);
        
        if (isset($cart[$request->cart_key])) {
            unset($cart[$request->cart_key]);
            session()->put('cart', $cart);
        }

        if ($request->wantsJson()) {
            return $this->getCartResponse();
        }

        return redirect()->back();
    }

    private function getCartResponse()
    {
        $cart = session()->get('cart', []);
        
        $totalQty = 0;
        $totalPrice = 0;
        $menuQty = [];
        $itemNames = [];

        foreach ($cart as $item) {
            $totalQty += $item['quantity'];
            $totalPrice += ($item['quantity'] * $item['harga']);
            $itemNames[] = $item['nama_menu'];
            
            if (!isset($menuQty[$item['menu_id']])) {
                $menuQty[$item['menu_id']] = 0;
            }
            $menuQty[$item['menu_id']] += $item['quantity'];
        }

        return response()->json([
            'success' => true,
            'totalQty' => $totalQty,
            'totalPrice' => $totalPrice,
            'itemNames' => implode(', ', array_unique($itemNames)),
            'menuQty' => (object) $menuQty // casting to object ensures it parses as JSON object even if empty
        ]);
    }
}
