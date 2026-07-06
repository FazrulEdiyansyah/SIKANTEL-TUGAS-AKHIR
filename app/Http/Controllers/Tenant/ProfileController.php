<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $tenant = $user->tenant;
        return view('tenant.profile.index', compact('user', 'tenant'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = auth()->user();
        $user->name = $request->name;
        
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }
        
        $user->save();

        // Update Tenant Photo
        $tenant = $user->tenant;
        if ($tenant && $request->hasFile('foto')) {
            if ($tenant->foto && Storage::disk('public')->exists($tenant->foto)) {
                Storage::disk('public')->delete($tenant->foto);
            }
            $path = $request->file('foto')->store('tenant_photos', 'public');
            $tenant->foto = $path;
            $tenant->save();
        }

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}
