<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Kantin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KantinController extends Controller
{
    public function index(Request $request)
    {
        $query = Kantin::withCount('tenants')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama_kantin', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%");
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $kantins = $query->paginate(10)->withQueryString();
        return view('superadmin.kantin.index', compact('kantins'));
    }

    public function create()
    {
        return view('superadmin.kantin.create');
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
            'status.required'      => 'Status wajib dipilih.',
            'foto.required'        => 'Foto Kantin wajib diunggah.',
            'foto.image'           => 'File harus berupa gambar.',
            'foto.max'             => 'Ukuran foto maksimal 2MB.',
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

        return redirect()->route('superadmin.kantin.index')->with('success', 'Kantin berhasil ditambahkan!');
    }

    public function show(Kantin $kantin)
    {
        $kantin->load('tenants.user');
        return view('superadmin.kantin.show', compact('kantin'));
    }

    public function edit(Kantin $kantin)
    {
        $kantin->load('tenants');
        return view('superadmin.kantin.edit', compact('kantin'));
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
            'status.required'      => 'Status wajib dipilih.',
            'foto.image'           => 'File harus berupa gambar.',
            'foto.max'             => 'Ukuran foto maksimal 2MB.',
        ]);

        if ($request->hasFile('foto')) {
            if ($kantin->foto) {
                Storage::disk('public')->delete($kantin->foto);
            }
            $kantin->foto = $request->file('foto')->store('kantin', 'public');
        }

        $kantin->nama_kantin = $request->nama_kantin;
        $kantin->lokasi = $request->lokasi;
        $kantin->status = $request->status;
        $kantin->save();

        return redirect()->route('superadmin.kantin.index')->with('success', 'Data kantin berhasil diperbarui!');
    }

    public function destroy(Kantin $kantin)
    {
        if ($kantin->tenants()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus kantin yang memiliki tenant aktif.');
        }

        if ($kantin->foto) {
            Storage::disk('public')->delete($kantin->foto);
        }
        
        $kantin->delete();
        return redirect()->route('superadmin.kantin.index')->with('success', 'Kantin berhasil dihapus!');
    }
}
