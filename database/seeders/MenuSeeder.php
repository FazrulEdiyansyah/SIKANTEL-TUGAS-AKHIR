<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();
        if (!$tenant) {
            echo "No tenant found.\n";
            return;
        }

        for ($i = 1; $i <= 10; $i++) {
            Menu::create([
                'tenant_id' => $tenant->id,
                'nama_menu' => 'Menu Spesial ' . $i,
                'deskripsi' => 'Ini adalah deskripsi sangat lezat untuk menu dummy ke-' . $i . '. Dibuat dengan bahan berkualitas.',
                'harga' => rand(10, 50) * 1000,
                'status' => rand(0, 1) ? 'tersedia' : 'habis',
            ]);
        }
    }
}
