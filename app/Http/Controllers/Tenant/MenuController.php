<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    private function getTenant()
    {
        return Tenant::where('user_id', Auth::id())->firstOrFail();
    }

    public function index(Request $request)
    {
        $tenant = $this->getTenant();
        
        $query = Menu::where('tenant_id', $tenant->id);

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->where('nama_menu', 'like', '%' . $request->search . '%');
        }

        // Filter status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $menus = $query->latest()->paginate(8)->withQueryString();
        
        $totalMenu = Menu::where('tenant_id', $tenant->id)->count();
        $menuTersedia = Menu::where('tenant_id', $tenant->id)->where('status', 'tersedia')->count();
        $menuHabis = Menu::where('tenant_id', $tenant->id)->where('status', 'habis')->count();

        return view('tenant.menu.index', compact('menus', 'totalMenu', 'menuTersedia', 'menuHabis'));
    }

    public function create()
    {
        return view('tenant.menu.create');
    }

    public function store(Request $request)
    {
        $tenant = $this->getTenant();

        $request->validate([
            'nama_menu' => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:255',
            'harga'     => 'required|numeric|min:0',
            'foto'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status'    => 'required|in:tersedia,habis',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('menu', 'public');
        }

        Menu::create([
            'tenant_id' => $tenant->id,
            'nama_menu' => $request->nama_menu,
            'deskripsi' => $request->deskripsi,
            'harga'     => $request->harga,
            'foto'      => $fotoPath,
            'status'    => $request->status,
        ]);

        return redirect()->route('tenant.menu.index')->with('success', 'Menu berhasil ditambahkan!');
    }

    public function edit(Menu $menu)
    {
        // Ensure tenant owns this menu
        $tenant = $this->getTenant();
        if ($menu->tenant_id !== $tenant->id) {
            abort(403);
        }

        return view('tenant.menu.edit', compact('menu'));
    }

    public function update(Request $request, Menu $menu)
    {
        $tenant = $this->getTenant();
        if ($menu->tenant_id !== $tenant->id) {
            abort(403);
        }

        $request->validate([
            'nama_menu' => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:255',
            'harga'     => 'required|numeric|min:0',
            'foto'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status'    => 'required|in:tersedia,habis',
        ]);

        $fotoPath = $menu->foto;
        if ($request->hasFile('foto')) {
            // Delete old photo if exists
            if ($menu->foto) {
                Storage::disk('public')->delete($menu->foto);
            }
            $fotoPath = $request->file('foto')->store('menu', 'public');
        }

        $menu->update([
            'nama_menu' => $request->nama_menu,
            'deskripsi' => $request->deskripsi,
            'harga'     => $request->harga,
            'foto'      => $fotoPath,
            'status'    => $request->status,
        ]);

        return redirect()->route('tenant.menu.index')->with('success', 'Menu berhasil diperbarui!');
    }

    public function destroy(Menu $menu)
    {
        $tenant = $this->getTenant();
        if ($menu->tenant_id !== $tenant->id) {
            abort(403);
        }

        if ($menu->foto) {
            Storage::disk('public')->delete($menu->foto);
        }

        $menu->delete();

        return redirect()->route('tenant.menu.index')->with('success', 'Menu berhasil dihapus!');
    }

    public function toggleStatus(Menu $menu)
    {
        $tenant = $this->getTenant();
        if ($menu->tenant_id !== $tenant->id) {
            abort(403);
        }

        $menu->update([
            'status' => $menu->status === 'tersedia' ? 'habis' : 'tersedia'
        ]);

        return back()->with('success', 'Status menu berhasil diubah!');
    }
}
