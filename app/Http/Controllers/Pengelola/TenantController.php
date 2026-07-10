<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
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
    public function index(Request $request)
    {
        $query = Tenant::with(['kantin', 'user']);

        $query->when($request->filled('search'), function ($q) use ($request) {
            $searchTerm = strtolower($request->search);
            $q->whereRaw('LOWER(nama_tenant) LIKE ?', ['%' . $searchTerm . '%']);
        });

        $query->when($request->filled('kantin_id'), function ($q) use ($request) {
            $q->where('kantin_id', $request->kantin_id);
        });

        $query->when($request->filled('status'), function ($q) use ($request) {
            $q->where('status', $request->status);
        });

        $query->when($request->filled('contract_status'), function ($q) use ($request) {
            if ($request->contract_status === 'expiring') {
                $q->whereNotNull('contract_end_date')
                  ->where('contract_end_date', '>=', now()->startOfDay())
                  ->where('contract_end_date', '<=', now()->addDays(30)->endOfDay());
            } elseif ($request->contract_status === 'expired') {
                $q->whereNotNull('contract_end_date')
                  ->where('contract_end_date', '<', now()->startOfDay());
            }
        });

        $tenants = $query->paginate(10)->withQueryString();
        $kantins = Kantin::where('status', 'aktif')->get();
        
        $totalTenant = Tenant::count();
        $totalTenantAktif = Tenant::where('status', 'aktif')->count();

        return view('pengelola.tenant.index', compact('tenants', 'kantins', 'totalTenant', 'totalTenantAktif'));
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
            'contract_start_date' => 'nullable|date',
            'contract_end_date'   => 'nullable|date|after_or_equal:contract_start_date',
            'bank_name'           => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:100',
            'bank_account_name'   => 'nullable|string|max:255',
            'nik'                 => 'nullable|string|max:50',
            'address'             => 'nullable|string',
            'ktp_document'        => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:2048',
            'contract_document'   => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:2048',
        ], [
            'nama_tenant.required'   => 'Nama Tenant wajib diisi.',
            'nama_tenant.unique'     => 'Nama Tenant ini sudah digunakan.',
            'kantin_id.required'     => 'Silakan pilih kantin.',
            'jenis_makanan.required' => 'Jenis Tenant wajib dipilih.',
            'jenis_makanan.in'       => 'Jenis Tenant tidak valid.',
            'no_telepon.required'    => 'Nomor telepon wajib diisi.',
            'foto.image'             => 'File harus berupa gambar.',
            'ktp_document.max'       => 'Ukuran file dokumen maksimal 2MB.',
            'contract_document.max'  => 'Ukuran file dokumen maksimal 2MB.',
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

            // Upload Dokumen
            $ktpPath = null;
            if ($request->hasFile('ktp_document')) {
                $ktpPath = $request->file('ktp_document')->store('tenant_documents', 'public');
            }

            $contractPath = null;
            if ($request->hasFile('contract_document')) {
                $contractPath = $request->file('contract_document')->store('tenant_documents', 'public');
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
                'contract_start_date' => $request->contract_start_date,
                'contract_end_date'   => $request->contract_end_date,
                'bank_name'           => $request->bank_name,
                'bank_account_number' => $request->bank_account_number,
                'bank_account_name'   => $request->bank_account_name,
                'nik'                 => $request->nik,
                'address'             => $request->address,
                'ktp_document'        => $ktpPath,
                'contract_document'   => $contractPath,
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
            'contract_start_date' => 'nullable|date',
            'contract_end_date'   => 'nullable|date|after_or_equal:contract_start_date',
            'bank_name'           => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:100',
            'bank_account_name'   => 'nullable|string|max:255',
            'nik'                 => 'nullable|string|max:50',
            'address'             => 'nullable|string',
            'ktp_document'        => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:2048',
            'contract_document'   => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:2048',
        ], [
            'nama_tenant.required'   => 'Nama Tenant wajib diisi.',
            'nama_tenant.unique'     => 'Nama Tenant ini sudah digunakan.',
            'kantin_id.required'     => 'Silakan pilih kantin.',
            'jenis_makanan.required' => 'Jenis Tenant wajib dipilih.',
            'jenis_makanan.in'       => 'Jenis Tenant tidak valid.',
            'no_telepon.required'    => 'Nomor telepon wajib diisi.',
            'foto.image'             => 'File harus berupa gambar.',
            'ktp_document.max'       => 'Ukuran file dokumen maksimal 2MB.',
            'contract_document.max'  => 'Ukuran file dokumen maksimal 2MB.',
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

            // Upload Dokumen
            if ($request->hasFile('ktp_document')) {
                if ($tenant->ktp_document) {
                    Storage::disk('public')->delete($tenant->ktp_document);
                }
                $tenant->ktp_document = $request->file('ktp_document')->store('tenant_documents', 'public');
            }

            if ($request->hasFile('contract_document')) {
                if ($tenant->contract_document) {
                    Storage::disk('public')->delete($tenant->contract_document);
                }
                $tenant->contract_document = $request->file('contract_document')->store('tenant_documents', 'public');
            }

            $tenant->kantin_id = $request->kantin_id;
            $tenant->nama_tenant = $request->nama_tenant;
            $tenant->jenis_makanan = $request->jenis_makanan;
            $tenant->no_telepon = $request->no_telepon;
            $tenant->status = $request->status;
            
            $tenant->contract_start_date = $request->contract_start_date;
            $tenant->contract_end_date = $request->contract_end_date;
            $tenant->bank_name = $request->bank_name;
            $tenant->bank_account_number = $request->bank_account_number;
            $tenant->bank_account_name = $request->bank_account_name;
            $tenant->nik = $request->nik;
            $tenant->address = $request->address;

            $tenant->save();

            DB::commit();

            return redirect()->route('pengelola.tenant.index')->with('success', 'Data tenant berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.')->withInput();
        }
    }
}
