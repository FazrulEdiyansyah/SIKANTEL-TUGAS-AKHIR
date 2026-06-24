<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PencairanDana;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;

class PencairanDanaSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();
        $pengelola = User::where('role', 'pengelola')->first();

        if (!$tenant || !$pengelola) {
            $this->command->warn('Tenant atau Pengelola tidak ditemukan. Pastikan DummySeeder sudah dijalankan.');
            return;
        }

        $statuses = [
            'draft',
            'proposed',
            'approved_kaur',
            'approved',
            'rejected_kaur',
            'rejected_kabag'
        ];

        foreach ($statuses as $status) {
            for ($i = 1; $i <= 3; $i++) {
                $totalPenjualan = rand(50, 200) * 10000; // 500k to 2M
                $danaTenant = $totalPenjualan * 0.70;
                $danaTelu = $totalPenjualan * 0.30;
                
                $start = Carbon::now()->subMonths(rand(1, 6))->startOfMonth();
                $end = $start->copy()->endOfMonth();

                $catatanKaur = $status === 'rejected_kaur' ? 'Harap cek ulang periode tanggal. Penjualan tidak sesuai.' : null;
                $catatanKabag = $status === 'rejected_kabag' ? 'Nominal tidak sesuai dengan laporan sistem pusat.' : null;

                PencairanDana::create([
                    'tenant_id' => $tenant->id,
                    'pengelola_id' => $pengelola->id,
                    'start_date' => $start,
                    'end_date' => $end,
                    'total_penjualan' => $totalPenjualan,
                    'dana_tenant' => $danaTenant,
                    'dana_telu' => $danaTelu,
                    'keterangan' => 'Laporan dummy otomatis - Status: ' . $status . ' (#' . $i . ')',
                    'status' => $status,
                    'catatan_kaur' => $catatanKaur,
                    'catatan_kabag' => $catatanKabag,
                ]);
            }
        }
        
        $this->command->info('Berhasil membuat dummy data Pencairan Dana (18 data total).');
    }
}
