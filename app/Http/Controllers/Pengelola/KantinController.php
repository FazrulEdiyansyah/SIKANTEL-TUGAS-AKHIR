<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\Kantin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KantinController extends Controller
{
    public function index(Request $request)
    {
        $query = Kantin::withCount('tenants');

        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = strtolower($request->search);
            $q->where(function ($sub) use ($search) {
                $sub->whereRaw('LOWER(nama_kantin) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(lokasi) LIKE ?', ["%{$search}%"]);
            });
        });

        $query->when($request->filled('status') && $request->status !== 'all', function ($q) use ($request) {
            $q->where('status', $request->status);
        });

        $kantins = $query->latest()->paginate(10)->withQueryString();
        return view('pengelola.kantin.index', compact('kantins'));
    }

    public function create()
    {
        return view('pengelola.kantin.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kantin' => 'required|string|max:255',
            'lokasi'      => 'required|string|max:255',
            'status'      => 'required|in:aktif,nonaktif',
            'foto'        => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'nama_kantin.required' => 'Nama Kantin wajib diisi.',
            'lokasi.required'      => 'Lokasi Kantin wajib diisi.',
            'foto.required'        => 'Foto Kantin wajib diisi.',
            'foto.image'           => 'File harus berupa gambar.',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('kantin', 'public');
        }

        Kantin::create([
            'nama_kantin' => $request->nama_kantin,
            'lokasi'      => $request->lokasi,
            'status'      => $request->status,
            'foto'        => $fotoPath,
        ]);

        return redirect()->route('pengelola.kantin.index')->with('success', 'Kantin berhasil ditambahkan!');
    }

    public function show(Kantin $kantin)
    {
        $kantin->load('tenants.user');
        return view('pengelola.kantin.show', compact('kantin'));
    }

    public function edit(Kantin $kantin)
    {
        return view('pengelola.kantin.edit', compact('kantin'));
    }

    public function update(Request $request, Kantin $kantin)
    {
        $request->validate([
            'nama_kantin' => 'required|string|max:255',
            'lokasi'      => 'required|string|max:255',
            'status'      => 'required|in:aktif,nonaktif',
            'foto'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'nama_kantin.required' => 'Nama Kantin wajib diisi.',
            'lokasi.required'      => 'Lokasi Kantin wajib diisi.',
            'foto.image'           => 'File harus berupa gambar.',
        ]);

        $fotoPath = $kantin->foto;
        if ($request->hasFile('foto')) {
            if ($fotoPath) {
                Storage::disk('public')->delete($fotoPath);
            }
            $fotoPath = $request->file('foto')->store('kantin', 'public');
        }

        $kantin->update([
            'nama_kantin' => $request->nama_kantin,
            'lokasi'      => $request->lokasi,
            'status'      => $request->status,
            'foto'        => $fotoPath,
        ]);

        return redirect()->route('pengelola.kantin.index')->with('success', 'Kantin berhasil diperbarui!');
    }
}
