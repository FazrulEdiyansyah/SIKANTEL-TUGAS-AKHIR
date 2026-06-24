<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Pengelola User',
            'email' => 'pengelola@test.com',
            'role' => 'pengelola',
        ]);

        User::factory()->create([
            'name' => 'Tenant User',
            'email' => 'tenant@test.com',
            'role' => 'tenant',
        ]);

        User::factory()->create([
            'name' => 'Pelanggan User',
            'email' => 'pelanggan@test.com',
            'role' => 'pelanggan',
        ]);

        $this->call([
            DummySeeder::class
        ]);
    }
}
