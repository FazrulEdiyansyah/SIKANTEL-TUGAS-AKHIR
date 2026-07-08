<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kantin;
use App\Models\Tenant;
use App\Models\Menu;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('search');
        
        // Handle recent searches in session
        $recentSearches = session()->get('recent_searches', []);

        if (!$query) {
            return view('pelanggan.search.empty', compact('recentSearches'));
        }

        // If there is a query, add it to recent searches
        if (!in_array($query, $recentSearches)) {
            array_unshift($recentSearches, $query);
            // Keep only the last 10 searches
            $recentSearches = array_slice($recentSearches, 0, 10);
            session()->put('recent_searches', $recentSearches);
        }

        // Cari Kantin
        $kantins = Kantin::where('status', 'aktif')
            ->where('nama_kantin', 'ilike', "%{$query}%")
            ->get();

        // Cari Tenant
        $tenants = Tenant::with(['kantin', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->where('status', 'aktif')
            ->where('nama_tenant', 'ilike', "%{$query}%")
            ->orderBy('is_open', 'desc')
            ->get();

        // Cari Menu
        $menus = Menu::with(['tenant.kantin'])
            ->whereHas('tenant', function($q) {
                $q->where('status', 'aktif');
            })
            ->where('nama_menu', 'ilike', "%{$query}%")
            ->orderByRaw("status = 'tersedia' DESC")
            ->get();

        return view('pelanggan.search.index', compact('query', 'kantins', 'tenants', 'menus'));
    }

    public function removeRecent(Request $request)
    {
        $searchToRemove = $request->input('search');
        if ($searchToRemove) {
            $recentSearches = session()->get('recent_searches', []);
            $recentSearches = array_filter($recentSearches, function($item) use ($searchToRemove) {
                return $item !== $searchToRemove;
            });
            session()->put('recent_searches', array_values($recentSearches));
        }

        return redirect()->route('pelanggan.search');
    }
}
