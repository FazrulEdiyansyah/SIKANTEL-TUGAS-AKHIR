<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('superadmin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('superadmin.roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:255'
        ]);

        Role::create($request->all());
        return redirect()->route('superadmin.roles.index')->with('success', 'Role berhasil ditambahkan');
    }

    public function edit(Role $role)
    {
        return view('superadmin.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:255'
        ]);

        // If role name is updated, we also need to update users with that role
        $oldName = $role->name;
        $newName = $request->name;

        $role->update($request->all());

        if ($oldName !== $newName) {
            \App\Models\User::where('role', $oldName)->update(['role' => $newName]);
        }

        return redirect()->route('superadmin.roles.index')->with('success', 'Role berhasil diperbarui');
    }

    public function destroy(Role $role)
    {
        if (in_array($role->name, ['superadmin', 'pengelola', 'tenant', 'pelanggan'])) {
            return back()->with('error', 'Role default sistem tidak dapat dihapus');
        }
        
        \App\Models\User::where('role', $role->name)->update(['role' => 'pelanggan']); // fallback
        $role->delete();

        return redirect()->route('superadmin.roles.index')->with('success', 'Role berhasil dihapus');
    }
}
