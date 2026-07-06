<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kantin;
use App\Models\Tenant;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\Role;

class DummySeeder extends Seeder
{
    public function run()
    {
        // 0. Roles
        $roles = [
            ['name' => 'superadmin', 'description' => 'Super Administrator dengan akses penuh'],
            ['name' => 'pengelola', 'description' => 'Pengelola sistem Kantin'],
            ['name' => 'kaur', 'description' => 'Kepala Urusan (Approval 1)'],
            ['name' => 'kabag', 'description' => 'Kepala Bagian (Approval 2)'],
            ['name' => 'tenant', 'description' => 'Pemilik Tenant/Kantin'],
            ['name' => 'pelanggan', 'description' => 'Pengguna biasa/pembeli'],
        ];
        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r['name']], $r);
        }

        // 1. Superadmin & Pengelola & Approvers
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@telkom.ac.id'],
            ['name' => 'Super Administrator', 'password' => Hash::make('password'), 'role' => 'superadmin']
        );

        $kaur = User::firstOrCreate(
            ['email' => 'kaur@telkom.ac.id'],
            ['name' => 'Kepala Urusan', 'password' => Hash::make('password'), 'role' => 'kaur']
        );

        $kabag = User::firstOrCreate(
            ['email' => 'kabag@telkom.ac.id'],
            ['name' => 'Kepala Bagian', 'password' => Hash::make('password'), 'role' => 'kabag']
        );

        $pengelola = User::firstOrCreate(
            ['email' => 'pengelola@telkom.ac.id'],
            ['name' => 'Pengelola Pusat', 'password' => Hash::make('password'), 'role' => 'pengelola']
        );

        // 2. Kantin
        $kantinFTE = Kantin::create([
            'nama_kantin' => 'Kantin Fakultas Teknik Elektro',
            'lokasi' => 'Gedung Deli, Telkom University',
            'status' => 'aktif'
        ]);

        $kantinFRI = Kantin::create([
            'nama_kantin' => 'Kantin Fakultas Rekayasa Industri',
            'lokasi' => 'Gedung Karang, Telkom University',
            'status' => 'aktif'
        ]);

        // 3. Tenants
        $userTenant1 = User::firstOrCreate(
            ['email' => 'ayam_geprek@tenant.sikantel.ac.id'],
            ['name' => 'Ayam Geprek Bensu', 'password' => Hash::make('password'), 'role' => 'tenant']
        );
        $tenant1 = Tenant::create([
            'user_id' => $userTenant1->id,
            'kantin_id' => $kantinFTE->id,
            'nama_tenant' => 'Ayam Geprek Bensu',
            'jenis_makanan' => 'Makanan Berat',
            'no_telepon' => '081234567890',
            'status' => 'aktif'
        ]);

        $userTenant2 = User::firstOrCreate(
            ['email' => 'soto_lamongan@tenant.sikantel.ac.id'],
            ['name' => 'Soto Ayam Lamongan', 'password' => Hash::make('password'), 'role' => 'tenant']
        );
        $tenant2 = Tenant::create([
            'user_id' => $userTenant2->id,
            'kantin_id' => $kantinFRI->id,
            'nama_tenant' => 'Soto Ayam Lamongan',
            'jenis_makanan' => 'Makanan Berat',
            'no_telepon' => '081298765432',
            'status' => 'aktif'
        ]);

        // 5. Menu
        $menu1 = Menu::create([
            'tenant_id' => $tenant1->id,
            'nama_menu' => 'Ayam Geprek Sambal Bawang',
            'deskripsi' => 'Ayam geprek pedas gurih',
            'harga' => 15000,
            'status' => 'tersedia'
        ]);

        $menu2 = Menu::create([
            'tenant_id' => $tenant2->id,
            'nama_menu' => 'Soto Ayam Kampung',
            'deskripsi' => 'Soto kuah kuning segar',
            'harga' => 18000,
            'status' => 'tersedia'
        ]);

        $menu3 = Menu::create([
            'tenant_id' => $tenant1->id,
            'nama_menu' => 'Es Teh Manis',
            'deskripsi' => 'Teh manis dingin',
            'harga' => 5000,
            'status' => 'tersedia'
        ]);

        // 6. Pelanggan
        $pelanggan1 = User::firstOrCreate(
            ['email' => 'mahasiswa1@student.telkomuniversity.ac.id'],
            ['name' => 'Budi Santoso', 'password' => Hash::make('password'), 'role' => 'pelanggan']
        );

        // 7. Orders Dummy
        // Order 1 (Selesai)
        $order1 = Order::create([
            'order_id' => 'ORD-DUMMY-' . Str::random(5),
            'tenant_id' => $tenant1->id,
            'user_id' => $pelanggan1->id,
            'total_price' => 20000,
            'payment_status' => 'success',
            'order_status' => 'selesai',
            'payment_type' => 'qris',
            'order_type' => 'dine-in',
            'table_number' => '12',
            'snap_token' => 'dummy',
            'created_at' => Carbon::now()->subDays(2)
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'menu_id' => $menu1->id,
            'nama_menu' => $menu1->nama_menu,
            'harga' => 15000,
            'quantity' => 1
        ]);
        OrderItem::create([
            'order_id' => $order1->id,
            'menu_id' => $menu3->id,
            'nama_menu' => $menu3->nama_menu,
            'harga' => 5000,
            'quantity' => 1
        ]);

        // Order 2 (Sedang Diproses)
        $order2 = Order::create([
            'order_id' => 'ORD-DUMMY-' . Str::random(5),
            'tenant_id' => $tenant2->id,
            'user_id' => $pelanggan1->id,
            'total_price' => 18000,
            'payment_status' => 'success',
            'order_status' => 'diproses',
            'payment_type' => 'gopay',
            'order_type' => 'takeaway',
            'snap_token' => 'dummy2',
            'created_at' => Carbon::now()->subHours(1)
        ]);
        OrderItem::create([
            'order_id' => $order2->id,
            'menu_id' => $menu2->id,
            'nama_menu' => $menu2->nama_menu,
            'harga' => 18000,
            'quantity' => 1
        ]);
    }
}
