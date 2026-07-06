<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Kantin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::with(['kantin', 'user'])->get();
        return view('pengelola.tenant.index', compact('tenants'));
    }

    public function create()
    {
        $kantins = Kantin::where('status', 'aktif')->get();
        return view('pengelola.tenant.create', compact('kantins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tenant'   => 'required|string|max:255|unique:tenants,nama_tenant',
            'kantin_id'     => 'required|exists:kantins,id',
            'jenis_makanan' => 'required|in:Makanan Berat,Makanan Ringan,Minuman',
            'no_telepon'    => 'required|string|max:20',
            'status'        => 'required|in:aktif,nonaktif',
            'foto'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'nama_tenant.required'   => 'Nama Tenant wajib diisi.',
            'nama_tenant.unique'     => 'Nama Tenant ini sudah digunakan.',
            'kantin_id.required'     => 'Silakan pilih kantin.',
            'jenis_makanan.required' => 'Jenis Tenant wajib dipilih.',
            'jenis_makanan.in'       => 'Jenis Tenant tidak valid.',
            'no_telepon.required'    => 'Nomor telepon wajib diisi.',
            'foto.image'             => 'File harus berupa gambar.',
        ]);

        try {
            DB::beginTransaction();

            $words = explode(' ', strtolower(preg_replace('/[^a-zA-Z0-9 ]/', '', $request->nama_tenant)));
            
            $abbreviatedWords = [];
            $currentLength = 0;
            foreach ($words as $word) {
                if ($currentLength < 15) {
                    $abbreviatedWords[] = $word;
                    $currentLength += strlen($word);
                } else {
                    $abbreviatedWords[] = substr($word, 0, 1);
                    $currentLength += 1;
                }
            }

            $cleanName = '';
            $count = count($abbreviatedWords);
            for ($i = 0; $i < $count; $i++) {
                $cleanName .= $abbreviatedWords[$i];
                if ($i < $count - 1) {
                    // 50% kesempatan untuk menambahkan titik antar kata
                    if (rand(1, 100) <= 50) {
                        $cleanName .= '.';
                    }
                }
            }
            
            // Memastikan panjang tidak berlebihan dan membuang titik di akhir jika ada
            $cleanName = rtrim(substr($cleanName, 0, 25), '.');

            // Username untuk login tanpa titik
            $uniqueName = str_replace('.', '', $cleanName);
            $uniqueEmail = $cleanName . '@tenant.sikantel.ac.id';

            $counter = 1;
            while (User::where('email', $uniqueEmail)->orWhere('name', $uniqueName)->exists()) {
                $uniqueName = $cleanName . $counter;
                $uniqueEmail = $cleanName . $counter . '@tenant.sikantel.ac.id';
                $counter++;
            }

            // Create User Account for Tenant
            $user = User::create([
                'name'     => $uniqueName,
                'email'    => $uniqueEmail,
                'password' => Hash::make('password'),
                'role'     => 'tenant',
            ]);

            // Upload Foto
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('tenant', 'public');
            }

            // Create Tenant Profile
            Tenant::create([
                'user_id'       => $user->id,
                'kantin_id'     => $request->kantin_id,
                'nama_tenant'   => $request->nama_tenant,
                'jenis_makanan' => $request->jenis_makanan,
                'no_telepon'    => $request->no_telepon,
                'status'        => $request->status,
                'foto'          => $fotoPath,
            ]);

            DB::commit();

            return redirect()->route('pengelola.tenant.index')->with('success', 'Tenant beserta akun login berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.')->withInput();
        }
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['kantin', 'user', 'menus']);
        return view('pengelola.tenant.show', compact('tenant'));
    }

    public function edit(Tenant $tenant)
    {
        $kantins = Kantin::where('status', 'aktif')->get();
        return view('pengelola.tenant.edit', compact('tenant', 'kantins'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $request->validate([
            'nama_tenant'   => 'required|string|max:255|unique:tenants,nama_tenant,' . $tenant->id,
            'kantin_id'     => 'required|exists:kantins,id',
            'jenis_makanan' => 'required|in:Makanan Berat,Makanan Ringan,Minuman',
            'no_telepon'    => 'required|string|max:20',
            'status'        => 'required|in:aktif,nonaktif',
            'foto'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'nama_tenant.required'   => 'Nama Tenant wajib diisi.',
            'nama_tenant.unique'     => 'Nama Tenant ini sudah digunakan.',
            'kantin_id.required'     => 'Silakan pilih kantin.',
            'jenis_makanan.required' => 'Jenis Tenant wajib dipilih.',
            'jenis_makanan.in'       => 'Jenis Tenant tidak valid.',
            'no_telepon.required'    => 'Nomor telepon wajib diisi.',
            'foto.image'             => 'File harus berupa gambar.',
        ]);

        try {
            DB::beginTransaction();

            // Upload Foto if provided
            if ($request->hasFile('foto')) {
                // Hapus foto lama jika ada
                if ($tenant->foto) {
                    Storage::disk('public')->delete($tenant->foto);
                }
                $tenant->foto = $request->file('foto')->store('tenant', 'public');
            }

            $tenant->kantin_id = $request->kantin_id;
            $tenant->nama_tenant = $request->nama_tenant;
            $tenant->jenis_makanan = $request->jenis_makanan;
            $tenant->no_telepon = $request->no_telepon;
            $tenant->status = $request->status;
            $tenant->save();

            DB::commit();

            return redirect()->route('pengelola.tenant.index')->with('success', 'Data tenant berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.')->withInput();
        }
    }
}
