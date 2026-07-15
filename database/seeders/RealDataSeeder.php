<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kantin;
use App\Models\Tenant;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\PencairanDana;
use App\Models\PencairanDanaDetail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RealDataSeeder extends Seeder
{
    /**
     * Data real untuk SIKANTEL - Sistem Informasi Kantin Telkom University
     * 
     * Spesifikasi:
     * - 3 Kantin (GKU, TULT, Asrama Putri)
     * - 27 Tenant (9 per kantin)
     * - 459 Menu (17 per tenant)
     * - 5 Pelanggan
     * - 240 Pesanan (6 Mei – 5 Juli 2026)
     * - 575 Detail Pesanan
     * - 240 Pembayaran
     * - 24 Laporan Pencairan Dana (bervariasi: per-kantin batch & individual)
     * - Reviews pada pesanan selesai
     */
    public function run(): void
    {
        $this->command->info('🚀 Memulai seeding data real SIKANTEL...');

        // ============================================================
        // 1. USERS - Total 37 user
        // ============================================================
        $this->command->info('👤 Membuat data pengguna...');

        $superadmin = User::create([
            'name' => 'Dr. Ahmad Fauzi, M.T.',
            'email' => 'superadmin@telkomuniversity.ac.id',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'phone_number' => '081234500001',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $pengelola1 = User::create([
            'name' => 'Rina Handayani, S.E.',
            'email' => 'pengelola1@telkomuniversity.ac.id',
            'password' => Hash::make('password'),
            'role' => 'pengelola',
            'phone_number' => '081234500002',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $pengelola2 = User::create([
            'name' => 'Dedi Kurniawan',
            'email' => 'pengelola2@telkomuniversity.ac.id',
            'password' => Hash::make('password'),
            'role' => 'pengelola',
            'phone_number' => '081234500003',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $kaur = User::create([
            'name' => 'Ir. Bambang Sutrisno, M.M.',
            'email' => 'kaur@telkomuniversity.ac.id',
            'password' => Hash::make('password'),
            'role' => 'kaur',
            'phone_number' => '081234500004',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $kabag = User::create([
            'name' => 'Hj. Sri Mulyani, S.E., M.M.',
            'email' => 'kabag@telkomuniversity.ac.id',
            'password' => Hash::make('password'),
            'role' => 'kabag',
            'phone_number' => '081234500005',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // 5 Pelanggan (mahasiswa/karyawan)
        $pelangganData = [
            ['name' => 'Rizky Pratama', 'email' => 'rizky.pratama@student.telkomuniversity.ac.id', 'phone' => '081356781001'],
            ['name' => 'Siti Nurhaliza', 'email' => 'siti.nurhaliza@student.telkomuniversity.ac.id', 'phone' => '081356781002'],
            ['name' => 'Andi Saputra', 'email' => 'andi.saputra@student.telkomuniversity.ac.id', 'phone' => '081356781003'],
            ['name' => 'Dewi Anggraeni', 'email' => 'dewi.anggraeni@student.telkomuniversity.ac.id', 'phone' => '081356781004'],
            ['name' => 'Fajar Ramadhan', 'email' => 'fajar.ramadhan@student.telkomuniversity.ac.id', 'phone' => '081356781005'],
        ];

        $pelangganUsers = [];
        foreach ($pelangganData as $p) {
            $pelangganUsers[] = User::create([
                'name' => $p['name'],
                'email' => $p['email'],
                'password' => Hash::make('password'),
                'role' => 'pelanggan',
                'phone_number' => $p['phone'],
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        // ============================================================
        // 2. KANTIN - 3 Lokasi
        // ============================================================
        $this->command->info('🏢 Membuat data kantin...');

        $kantins = [];
        $kantinData = [
            [
                'nama_kantin' => 'Kantin GKU',
                'lokasi' => 'Gedung Kuliah Umum (GKU), Lantai 1, Telkom University',
                'foto' => 'kantin/kantin_gku_1783329401051.png',
            ],
            [
                'nama_kantin' => 'Kantin TULT',
                'lokasi' => 'Gedung TULT (Telkom University Landmark Tower), Basement, Telkom University',
                'foto' => 'kantin/kantin_tult_1783329410996.png',
            ],
            [
                'nama_kantin' => 'Kantin Asrama Putri',
                'lokasi' => 'Area Asrama Putri, Blok C, Telkom University',
                'foto' => 'kantin/kantin_asrama_putri_1783329420604.png',
            ],
        ];

        foreach ($kantinData as $k) {
            $kantins[] = Kantin::create(array_merge($k, ['status' => 'aktif']));
        }

        // ============================================================
        // 3. TENANT - 27 Tenant (9 per kantin)
        // ============================================================
        $this->command->info('🏪 Membuat data tenant...');

        // Array foto tenant yang sudah di-generate AI
        $tenantPhotos = [
            'tenant/tenant_warung_nasi_bu_tejo_1783329442222.png',
            'tenant/tenant_ayam_geprek_mbok_sri_1783329452274.png',
            'tenant/tenant_kedai_kopi_senja_1783329462324.png',
            'tenant/tenant_mie_ayam_pak_kumis_1783329483118.png',
            'tenant/tenant_sate_madura_haji_1783329500153.png',
            'tenant/tenant_bakso_solo_1783329515532.png',
            'tenant/tenant_es_juice_segar_1783329538185.png',
            'tenant/tenant_nasi_goreng_spesial_1783329549885.png',
            'tenant/tenant_pecel_lele_lamongan_1783329560376.png',
            'tenant/tenant_soto_ayam_lamongan_1783329580152.png',
            'tenant/tenant_roti_bakar_bandung_1783329589275.png',
            'tenant/tenant_dapur_sunda_1783329601670.png',
            'tenant/tenant_dimsum_mantap_1783329622212.png',
            'tenant/tenant_martabak_bangka_1783329631678.png',
        ];

        // Definisi tenant per kantin
        $tenantDefinitions = [
            // === Kantin GKU (9 tenant) ===
            0 => [
                ['nama' => 'Warung Nasi Bu Tejo', 'jenis' => 'Nasi & Lauk', 'email' => 'bu.tejo', 'phone' => '081200010001', 'foto_idx' => 0],
                ['nama' => 'Ayam Geprek Mbok Sri', 'jenis' => 'Ayam Geprek', 'email' => 'mbok.sri', 'phone' => '081200010002', 'foto_idx' => 1],
                ['nama' => 'Kedai Kopi Senja', 'jenis' => 'Kopi & Minuman', 'email' => 'kopi.senja', 'phone' => '081200010003', 'foto_idx' => 2],
                ['nama' => 'Mie Ayam Pak Kumis', 'jenis' => 'Mie & Bakso', 'email' => 'pak.kumis', 'phone' => '081200010004', 'foto_idx' => 3],
                ['nama' => 'Sate Madura H. Ridwan', 'jenis' => 'Sate & Tongseng', 'email' => 'sate.ridwan', 'phone' => '081200010005', 'foto_idx' => 4],
                ['nama' => 'Bakso Solo Pak Dhe', 'jenis' => 'Bakso & Soto', 'email' => 'bakso.solo', 'phone' => '081200010006', 'foto_idx' => 5],
                ['nama' => 'Es Juice Segar 88', 'jenis' => 'Jus & Es', 'email' => 'juice.segar', 'phone' => '081200010007', 'foto_idx' => 6],
                ['nama' => 'Nasi Goreng Spesial Mas Joko', 'jenis' => 'Nasi Goreng', 'email' => 'nasgor.joko', 'phone' => '081200010008', 'foto_idx' => 7],
                ['nama' => 'Pecel Lele Lamongan Bu Yati', 'jenis' => 'Pecel Lele & Ayam', 'email' => 'lele.yati', 'phone' => '081200010009', 'foto_idx' => 8],
            ],
            // === Kantin TULT (9 tenant) ===
            1 => [
                ['nama' => 'Soto Ayam Lamongan Cak Har', 'jenis' => 'Soto & Rawon', 'email' => 'soto.cakhar', 'phone' => '081200020001', 'foto_idx' => 9],
                ['nama' => 'Roti Bakar Bandung 69', 'jenis' => 'Roti & Snack', 'email' => 'rotibakar.69', 'phone' => '081200020002', 'foto_idx' => 10],
                ['nama' => 'Dapur Sunda Neng Imas', 'jenis' => 'Masakan Sunda', 'email' => 'dapur.imas', 'phone' => '081200020003', 'foto_idx' => 11],
                ['nama' => 'Dimsum Mantap 168', 'jenis' => 'Dimsum & Siomay', 'email' => 'dimsum.168', 'phone' => '081200020004', 'foto_idx' => 12],
                ['nama' => 'Martabak Bangka Ajung', 'jenis' => 'Martabak', 'email' => 'martabak.ajung', 'phone' => '081200020005', 'foto_idx' => 13],
                ['nama' => 'Rice Bowl Kekinian', 'jenis' => 'Rice Bowl', 'email' => 'ricebowl.kekinian', 'phone' => '081200020006', 'foto_idx' => 0],
                ['nama' => 'Warung Padang Sederhana', 'jenis' => 'Masakan Padang', 'email' => 'padang.sederhana', 'phone' => '081200020007', 'foto_idx' => 1],
                ['nama' => 'Kedai Teh Tarik Abang', 'jenis' => 'Teh & Minuman', 'email' => 'teh.abang', 'phone' => '081200020008', 'foto_idx' => 2],
                ['nama' => 'Bubur Ayam Syarifah', 'jenis' => 'Bubur', 'email' => 'bubur.syarifah', 'phone' => '081200020009', 'foto_idx' => 3],
            ],
            // === Kantin Asrama Putri (9 tenant) ===
            2 => [
                ['nama' => 'Ayam Bakar Wong Solo', 'jenis' => 'Ayam Bakar & Goreng', 'email' => 'ayambakar.ws', 'phone' => '081200030001', 'foto_idx' => 4],
                ['nama' => 'Seblak & Batagor Mang Oding', 'jenis' => 'Seblak & Batagor', 'email' => 'seblak.oding', 'phone' => '081200030002', 'foto_idx' => 5],
                ['nama' => 'Depot Mie Kocok Bandung', 'jenis' => 'Mie Kocok', 'email' => 'miekocok.bdg', 'phone' => '081200030003', 'foto_idx' => 6],
                ['nama' => 'Nasi Uduk Betawi Mpok Ati', 'jenis' => 'Nasi Uduk', 'email' => 'uduk.mpokati', 'phone' => '081200030004', 'foto_idx' => 7],
                ['nama' => 'Kedai Salad & Smoothie Bowl', 'jenis' => 'Healthy Food', 'email' => 'salad.bowl', 'phone' => '081200030005', 'foto_idx' => 8],
                ['nama' => 'Warung Indomie Kang Asep', 'jenis' => 'Indomie & Mie Instant', 'email' => 'indomie.asep', 'phone' => '081200030006', 'foto_idx' => 9],
                ['nama' => 'Kebab Turki Baba Rafi', 'jenis' => 'Kebab & Burger', 'email' => 'kebab.rafi', 'phone' => '081200030007', 'foto_idx' => 10],
                ['nama' => 'Es Cendol Dawet Ibu Yuli', 'jenis' => 'Es & Minuman Tradisional', 'email' => 'cendol.yuli', 'phone' => '081200030008', 'foto_idx' => 11],
                ['nama' => 'Pisang Goreng Crispy Mas Dani', 'jenis' => 'Gorengan & Snack', 'email' => 'pisgor.dani', 'phone' => '081200030009', 'foto_idx' => 12],
            ],
        ];

        $allTenants = []; // id => Tenant model
        $originalJenisByTenant = []; // id => 'Nasi & Lauk'
        $tenantsByKantin = []; // kantin_id => [Tenant, ...]

        foreach ($tenantDefinitions as $kantinIdx => $tenantsInKantin) {
            $tenantsByKantin[$kantins[$kantinIdx]->id] = [];
            foreach ($tenantsInKantin as $td) {
                $userTenant = User::create([
                    'name' => $td['nama'],
                    'email' => $td['email'] . '@tenant.sikantel.ac.id',
                    'password' => Hash::make('password'),
                    'role' => 'tenant',
                    'phone_number' => $td['phone'],
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]);

                $jenisKategori = 'Makanan Berat';
                $j = strtolower($td['jenis']);
                if (str_contains($j, 'minuman') || str_contains($j, 'kopi') || str_contains($j, 'es ') || str_contains($j, 'jus') || str_contains($j, 'teh')) {
                    $jenisKategori = 'Minuman';
                } elseif (str_contains($j, 'roti') || str_contains($j, 'snack') || str_contains($j, 'dimsum') || str_contains($j, 'martabak') || str_contains($j, 'gorengan') || str_contains($j, 'seblak')) {
                    $jenisKategori = 'Makanan Ringan';
                }

                $tenant = Tenant::create([
                    'user_id' => $userTenant->id,
                    'kantin_id' => $kantins[$kantinIdx]->id,
                    'nama_tenant' => $td['nama'],
                    'jenis_makanan' => $jenisKategori,
                    'no_telepon' => $td['phone'],
                    'foto' => $tenantPhotos[$td['foto_idx']],
                    'status' => 'aktif',
                    'is_open' => true,
                ]);

                $allTenants[$tenant->id] = $tenant;
                $originalJenisByTenant[$tenant->id] = $td['jenis'];
                $tenantsByKantin[$kantins[$kantinIdx]->id][] = $tenant;
            }
        }

        $this->command->info('   ✅ ' . count($allTenants) . ' tenant berhasil dibuat');

        // ============================================================
        // 4. MENU - 17 per tenant = 459 total
        // ============================================================
        $this->command->info('🍔 Membuat data menu (459 menu)...');

        $menuTemplates = $this->getMenuTemplates();
        $allMenusByTenant = []; // tenant_id => [Menu, ...]

        foreach ($allTenants as $tenantId => $tenant) {
            $allMenusByTenant[$tenantId] = [];
            $originalJenis = $originalJenisByTenant[$tenantId] ?? 'Nasi & Lauk';
            $templateKey = $this->getMenuTemplateKey($originalJenis);
            $menus = $menuTemplates[$templateKey];

            foreach ($menus as $menuData) {
                $menu = Menu::create([
                    'tenant_id' => $tenantId,
                    'nama_menu' => $menuData['nama'],
                    'deskripsi' => $menuData['deskripsi'],
                    'harga' => $menuData['harga'],
                    'foto' => null, // Menu photos use placeholder via URL
                    'status' => rand(1, 10) <= 9 ? 'tersedia' : 'habis', // 90% tersedia
                    'is_customizable' => $menuData['is_customizable'] ?? false,
                    'customizations' => $menuData['customizations'] ?? null,
                ]);
                $allMenusByTenant[$tenantId][] = $menu;
            }
        }

        $totalMenus = Menu::count();
        $this->command->info("   ✅ {$totalMenus} menu berhasil dibuat");

        // ============================================================
        // 5. ORDERS - 240 pesanan (6 Mei - 5 Juli 2026)
        // ============================================================
        $this->command->info('🛒 Membuat data pesanan (240 pesanan)...');

        $startDate = Carbon::parse('2026-05-06');
        $endDate = Carbon::parse('2026-07-05');
        $totalDays = $startDate->diffInDays($endDate);

        $paymentTypes = ['qris', 'gopay', 'shopeepay', 'bank_transfer'];
        $orderTypes = ['dine-in', 'takeaway'];

        $allOrders = [];
        $orderItemCount = 0;

        for ($i = 0; $i < 240; $i++) {
            // Pilih pelanggan secara acak
            $pelanggan = $pelangganUsers[array_rand($pelangganUsers)];

            // Pilih tenant secara acak
            $tenantIds = array_keys($allMenusByTenant);
            $randomTenantId = $tenantIds[array_rand($tenantIds)];
            $tenantMenus = $allMenusByTenant[$randomTenantId];

            // Generate tanggal acak antara 6 Mei - 5 Juli 2026
            $randomDay = rand(0, $totalDays);
            $orderDate = $startDate->copy()->addDays($randomDay)
                ->setHour(rand(7, 20))
                ->setMinute(rand(0, 59))
                ->setSecond(rand(0, 59));

            // Pilih 1-3 menu secara acak
            $numItems = rand(1, 3);
            $selectedMenus = [];
            $shuffled = $tenantMenus;
            shuffle($shuffled);
            for ($j = 0; $j < min($numItems, count($shuffled)); $j++) {
                $selectedMenus[] = $shuffled[$j];
            }

            // Hitung total harga
            $totalPrice = 0;
            $itemsData = [];
            foreach ($selectedMenus as $menu) {
                $qty = rand(1, 2);
                $totalPrice += $menu->harga * $qty;
                $itemsData[] = [
                    'menu' => $menu,
                    'qty' => $qty,
                ];
                $orderItemCount++;
            }

            // Tentukan status pesanan berdasarkan tanggal
            $isOldOrder = $orderDate->lt(Carbon::parse('2026-07-04'));
            if ($isOldOrder) {
                // Pesanan lama: mayoritas selesai
                $statusRoll = rand(1, 100);
                if ($statusRoll <= 80) {
                    $paymentStatus = 'success';
                    $orderStatus = 'selesai';
                } elseif ($statusRoll <= 90) {
                    $paymentStatus = 'success';
                    $orderStatus = 'selesai';
                } elseif ($statusRoll <= 95) {
                    $paymentStatus = 'failed';
                    $orderStatus = 'belum_diproses';
                } else {
                    $paymentStatus = 'expired';
                    $orderStatus = 'belum_diproses';
                }
            } else {
                // Pesanan baru (hari ini / kemarin): status bervariasi
                $statusRoll = rand(1, 100);
                if ($statusRoll <= 30) {
                    $paymentStatus = 'success';
                    $orderStatus = 'selesai';
                } elseif ($statusRoll <= 50) {
                    $paymentStatus = 'success';
                    $orderStatus = 'siap_diambil';
                } elseif ($statusRoll <= 70) {
                    $paymentStatus = 'success';
                    $orderStatus = 'diproses';
                } elseif ($statusRoll <= 85) {
                    $paymentStatus = 'pending';
                    $orderStatus = 'belum_diproses';
                } else {
                    $paymentStatus = 'success';
                    $orderStatus = 'belum_diproses';
                }
            }

            $orderType = $orderTypes[array_rand($orderTypes)];

            $order = Order::create([
                'order_id' => 'ORD-' . $orderDate->format('Ymd') . '-' . strtoupper(Str::random(5)),
                'user_id' => $pelanggan->id,
                'tenant_id' => $randomTenantId,
                'total_price' => $totalPrice,
                'payment_status' => $paymentStatus,
                'order_status' => $orderStatus,
                'payment_type' => $paymentStatus !== 'pending' ? $paymentTypes[array_rand($paymentTypes)] : null,
                'snap_token' => $paymentStatus === 'success' ? 'tok_' . Str::random(20) : null,
                'order_type' => $orderType,

                'pickup_pin' => ($paymentStatus === 'success' && in_array($orderStatus, ['diproses', 'siap_diambil', 'selesai']))
                    ? str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT) : null,
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            // Order Items
            foreach ($itemsData as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $item['menu']->id,
                    'nama_menu' => $item['menu']->nama_menu,
                    'harga' => $item['menu']->harga,
                    'quantity' => $item['qty'],
                    'selected_options' => null,
                    'catatan' => rand(1, 5) === 1 ? $this->getRandomCatatan() : null,
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);
            }

            $allOrders[] = $order;
        }

        $this->command->info("   ✅ 240 pesanan dengan {$orderItemCount} detail item berhasil dibuat");

        // ============================================================
        // 6. REVIEWS - pada pesanan yang selesai (~60% diberi review)
        // ============================================================
        $this->command->info('⭐ Membuat data ulasan...');

        $reviewComments = $this->getReviewComments();
        $reviewCount = 0;

        foreach ($allOrders as $order) {
            if ($order->payment_status === 'success' && $order->order_status === 'selesai') {
                // 60% kemungkinan diberi review
                if (rand(1, 100) <= 60) {
                    $rating = $this->getWeightedRating();
                    Review::create([
                        'user_id' => $order->user_id,
                        'tenant_id' => $order->tenant_id,
                        'order_id' => $order->id,
                        'rating' => $rating,
                        'comment' => rand(1, 3) <= 2 ? $reviewComments[$rating][array_rand($reviewComments[$rating])] : null,
                        'created_at' => $order->created_at->copy()->addHours(rand(1, 24)),
                        'updated_at' => $order->created_at->copy()->addHours(rand(1, 24)),
                    ]);
                    $reviewCount++;
                }
            }
        }

        $this->command->info("   ✅ {$reviewCount} ulasan berhasil dibuat");

        // ============================================================
        // 7. PENCAIRAN DANA - 24 batch (3 kantin × 8 minggu) bervariasi
        // ============================================================
        $this->command->info('💰 Membuat data pencairan dana...');

        $pencairanStatuses = ['approved', 'approved', 'approved', 'approved_kaur', 'proposed', 'draft', 'rejected_kaur', 'rejected_kabag'];
        $pencairanCount = 0;
        $detailCount = 0;

        // 8 minggu: 6 Mei - 5 Juli 2026
        $weekStarts = [];
        $weekStart = Carbon::parse('2026-05-06');
        for ($w = 0; $w < 8; $w++) {
            $weekStarts[] = $weekStart->copy();
            $weekStart->addWeek();
        }

        foreach ($kantins as $kantinIdx => $kantin) {
            $kantinTenants = $tenantsByKantin[$kantin->id];

            foreach ($weekStarts as $weekIdx => $weekStartDate) {
                $weekEndDate = $weekStartDate->copy()->addDays(6)->endOfDay();
                $batchId = 'REQ-' . $weekStartDate->format('Ymd') . '-' . strtoupper(Str::random(6));

                // Tentukan status berdasarkan umur: minggu lama = lebih mungkin approved
                if ($weekIdx < 4) {
                    // Minggu 1-4 (lama): mayoritas sudah approved
                    $status = rand(1, 10) <= 8 ? 'approved' : $pencairanStatuses[array_rand($pencairanStatuses)];
                } elseif ($weekIdx < 6) {
                    // Minggu 5-6: campur (approved_kaur, proposed, dll)
                    $statusPool = ['approved', 'approved_kaur', 'proposed', 'rejected_kaur'];
                    $status = $statusPool[array_rand($statusPool)];
                } else {
                    // Minggu 7-8 (baru): mostly draft/proposed
                    $statusPool = ['draft', 'proposed', 'approved_kaur'];
                    $status = $statusPool[array_rand($statusPool)];
                }

                // Tentukan pola: beberapa batch per-kantin (semua 9 tenant), beberapa individual
                $isFullBatch = rand(1, 3) <= 2; // 67% full batch

                if ($isFullBatch) {
                    // Full batch: semua 9 tenant dalam 1 batch_id
                    foreach ($kantinTenants as $tenant) {
                        $this->createPencairanForTenant(
                            $tenant, $pengelola1, $kaur, $kabag, $batchId,
                            $weekStartDate, $weekEndDate, $status, $allOrders
                        );
                        $pencairanCount++;
                    }
                } else {
                    // Individual: hanya beberapa tenant
                    $selectedCount = rand(2, 5);
                    $shuffledTenants = $kantinTenants;
                    shuffle($shuffledTenants);
                    $selectedTenants = array_slice($shuffledTenants, 0, $selectedCount);

                    foreach ($selectedTenants as $tenant) {
                        $this->createPencairanForTenant(
                            $tenant, $pengelola1, $kaur, $kabag, $batchId,
                            $weekStartDate, $weekEndDate, $status, $allOrders
                        );
                        $pencairanCount++;
                    }
                }
            }
        }

        $detailCount = PencairanDanaDetail::count();
        $this->command->info("   ✅ {$pencairanCount} pencairan dana dengan {$detailCount} detail berhasil dibuat");

        // ============================================================
        // SUMMARY
        // ============================================================
        $this->command->newLine();
        $this->command->info('============================================');
        $this->command->info('🎉 SEEDING DATA REAL SELESAI!');
        $this->command->info('============================================');
        $this->command->info('👤 Users        : ' . User::count());
        $this->command->info('🏢 Kantin       : ' . Kantin::count());
        $this->command->info('🏪 Tenant       : ' . Tenant::count());
        $this->command->info('🍔 Menu         : ' . Menu::count());
        $this->command->info('🛒 Orders       : ' . Order::count());
        $this->command->info('📦 Order Items  : ' . OrderItem::count());
        $this->command->info('⭐ Reviews      : ' . Review::count());
        $this->command->info('💰 Pencairan    : ' . PencairanDana::count());
        $this->command->info('📋 Detail Cair  : ' . PencairanDanaDetail::count());
        $this->command->info('============================================');
        $this->command->newLine();
        $this->command->info('📌 Semua user menggunakan password: "password"');
        $this->command->info('📌 Login superadmin: superadmin@telkomuniversity.ac.id');
        $this->command->info('📌 Login pengelola: pengelola1@telkomuniversity.ac.id');
        $this->command->info('📌 Login kaur: kaur@telkomuniversity.ac.id');
        $this->command->info('📌 Login kabag: kabag@telkomuniversity.ac.id');
        $this->command->info('📌 Login pelanggan: rizky.pratama@student.telkomuniversity.ac.id');
    }

    /**
     * Membuat record PencairanDana untuk satu tenant
     */
    private function createPencairanForTenant(
        $tenant, $pengelola, $kaur, $kabag, $batchId,
        $weekStartDate, $weekEndDate, $status, $allOrders
    ): void {
        // Cari orders yang selesai untuk tenant ini dalam rentang waktu
        $tenantOrders = collect($allOrders)->filter(function ($order) use ($tenant, $weekStartDate, $weekEndDate) {
            return $order->tenant_id === $tenant->id
                && $order->payment_status === 'success'
                && $order->order_status === 'selesai'
                && $order->created_at->gte($weekStartDate)
                && $order->created_at->lte($weekEndDate);
        });

        $totalPenjualan = $tenantOrders->sum('total_price');

        // Jika tidak ada penjualan, buat nominal dummy realistis
        if ($totalPenjualan == 0) {
            $totalPenjualan = rand(150, 800) * 1000; // Rp 150.000 - Rp 800.000
        }

        $danaTenant = round($totalPenjualan * 0.70, 2);
        $danaTelu = round($totalPenjualan * 0.30, 2);

        $approverName = $kaur->name . ' (Kaur) & ' . $kabag->name . ' (Kabag)';
        $catatanKaur = null;
        $catatanKabag = null;

        if (in_array($status, ['approved_kaur', 'approved'])) {
            $catatanKaur = 'Disetujui. Data sudah sesuai dengan rekap penjualan.';
        }
        if ($status === 'approved') {
            $catatanKabag = 'Disetujui dan siap dicairkan ke rekening tenant.';
        }
        if ($status === 'rejected_kaur') {
            $catatanKaur = 'Ditolak. Harap periksa kembali periode dan nominal penjualan.';
        }
        if ($status === 'rejected_kabag') {
            $catatanKaur = 'Disetujui oleh Kaur.';
            $catatanKabag = 'Ditolak. Nominal tidak sesuai dengan laporan sistem pusat.';
        }

        $createdAt = $weekEndDate->copy()->addDays(rand(1, 3));

        $pencairan = PencairanDana::create([
            'batch_id' => $batchId,
            'pengelola_id' => $pengelola->id,
            'tenant_id' => $tenant->id,
            'approver_name' => $approverName,
            'start_date' => $weekStartDate,
            'end_date' => $weekEndDate,
            'total_penjualan' => $totalPenjualan,
            'dana_tenant' => $danaTenant,
            'dana_telu' => $danaTelu,
            'keterangan' => 'Pencairan dana penjualan periode ' . $weekStartDate->format('d M') . ' - ' . $weekEndDate->format('d M Y'),
            'status' => $status,
            'catatan_kaur' => $catatanKaur,
            'catatan_kabag' => $catatanKabag,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        // Detail pencairan dari order nyata
        foreach ($tenantOrders as $order) {
            PencairanDanaDetail::create([
                'pencairan_dana_id' => $pencairan->id,
                'order_id' => $order->id,
                'total_price' => $order->total_price,
                'dana_tenant' => round($order->total_price * 0.70, 2),
                'dana_telu' => round($order->total_price * 0.30, 2),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }

    /**
     * Rating berbobot: mayoritas bintang 4 & 5
     */
    private function getWeightedRating(): int
    {
        $roll = rand(1, 100);
        if ($roll <= 5) return 1;
        if ($roll <= 10) return 2;
        if ($roll <= 25) return 3;
        if ($roll <= 55) return 4;
        return 5;
    }

    /**
     * Catatan pesanan acak
     */
    private function getRandomCatatan(): string
    {
        $catatan = [
            'Ga pake bawang ya',
            'Sambal pisah',
            'Nasinya agak banyakin',
            'Ga pake sayur',
            'Level pedas 3',
            'Pake telur ceplok',
            'Es batu sedikit aja',
            'Extra sambal',
            'Jangan terlalu asin',
            'Minta tisu lebih ya',
            'Gula dikit aja',
            'Ga pake es',
            'Mie nya yang keriting',
            'Pake kerupuk',
            'Kuahnya banyakin',
        ];
        return $catatan[array_rand($catatan)];
    }

    /**
     * Komentar review berdasarkan rating
     */
    private function getReviewComments(): array
    {
        return [
            1 => [
                'Porsinya kecil banget, ga worth it.',
                'Rasanya hambar, mengecewakan.',
                'Nunggunya kelamaan, udah dingin pas dateng.',
                'Kurang higienis menurut saya.',
            ],
            2 => [
                'Lumayan tapi masih banyak yang perlu diperbaiki.',
                'Harganya agak mahal untuk porsi segitu.',
                'Rasa standar aja sih, kurang nampol.',
                'Pelayanannya lambat banget.',
            ],
            3 => [
                'Oke lah buat harga segitu.',
                'Rasanya standar, ga istimewa tapi ga buruk.',
                'Tempatnya lumayan bersih.',
                'Cukup mengenyangkan sih.',
                'Biasa aja, nothing special.',
            ],
            4 => [
                'Enak! Porsinya juga pas.',
                'Recommended sih, harga terjangkau rasa oke.',
                'Lumayan enak, pasti balik lagi.',
                'Pelayanannya ramah, makanannya enak.',
                'Worth it banget buat mahasiswa.',
                'Suka sama sambalnya, mantap!',
                'Makanannya fresh dan enak.',
            ],
            5 => [
                'Mantap banget! Best food di kantin ini.',
                'Enak parah, tiap hari beli di sini.',
                'Porsi banyak, rasa juara. TOP!',
                'Paling enak se-kantin, ga boong!',
                'Wajib coba! Rasanya ga pernah mengecewakan.',
                'Favorite saya, udah langganan dari semester lalu.',
                'Gila sih ini enak banget, auto repeat order.',
                '10/10, ga ada lawan.',
            ],
        ];
    }

    /**
     * Mapping jenis makanan ke template key
     */
    private function getMenuTemplateKey(string $jenisMakanan): string
    {
        $mapping = [
            'Nasi & Lauk' => 'nasi_lauk',
            'Ayam Geprek' => 'ayam_geprek',
            'Kopi & Minuman' => 'kopi_minuman',
            'Mie & Bakso' => 'mie_bakso',
            'Sate & Tongseng' => 'sate',
            'Bakso & Soto' => 'bakso_soto',
            'Jus & Es' => 'jus_es',
            'Nasi Goreng' => 'nasi_goreng',
            'Pecel Lele & Ayam' => 'pecel_lele',
            'Soto & Rawon' => 'soto',
            'Roti & Snack' => 'roti_snack',
            'Masakan Sunda' => 'masakan_sunda',
            'Dimsum & Siomay' => 'dimsum',
            'Martabak' => 'martabak',
            'Rice Bowl' => 'rice_bowl',
            'Masakan Padang' => 'masakan_padang',
            'Teh & Minuman' => 'teh_minuman',
            'Bubur' => 'bubur',
            'Ayam Bakar & Goreng' => 'ayam_bakar',
            'Seblak & Batagor' => 'seblak',
            'Mie Kocok' => 'mie_kocok',
            'Nasi Uduk' => 'nasi_uduk',
            'Healthy Food' => 'healthy',
            'Indomie & Mie Instant' => 'indomie',
            'Kebab & Burger' => 'kebab',
            'Es & Minuman Tradisional' => 'es_tradisional',
            'Gorengan & Snack' => 'gorengan',
        ];
        return $mapping[$jenisMakanan] ?? 'nasi_lauk';
    }

    /**
     * Template menu per kategori (17 menu per kategori)
     */
    private function getMenuTemplates(): array
    {
        return [
            'nasi_lauk' => [
                ['nama' => 'Nasi Putih + Ayam Goreng', 'deskripsi' => 'Nasi putih hangat dengan ayam goreng kremes renyah', 'harga' => 15000],
                ['nama' => 'Nasi Putih + Ayam Bakar', 'deskripsi' => 'Nasi putih dengan ayam bakar bumbu kecap manis', 'harga' => 18000],
                ['nama' => 'Nasi Putih + Ikan Goreng', 'deskripsi' => 'Nasi putih dengan ikan nila goreng garing', 'harga' => 16000],
                ['nama' => 'Nasi Putih + Rendang', 'deskripsi' => 'Nasi putih dengan rendang daging sapi empuk', 'harga' => 22000],
                ['nama' => 'Nasi Putih + Telur Dadar', 'deskripsi' => 'Nasi putih dengan telur dadar tebal', 'harga' => 10000],
                ['nama' => 'Nasi Putih + Tempe Goreng', 'deskripsi' => 'Nasi putih dengan tempe goreng tepung renyah', 'harga' => 8000],
                ['nama' => 'Nasi Putih + Tongkol Balado', 'deskripsi' => 'Nasi putih dengan ikan tongkol sambal balado', 'harga' => 14000],
                ['nama' => 'Nasi Campur Komplit', 'deskripsi' => 'Nasi dengan ayam, tahu, tempe, sambal, lalapan', 'harga' => 20000],
                ['nama' => 'Nasi Putih + Tahu Tempe', 'deskripsi' => 'Nasi putih dengan tahu dan tempe goreng', 'harga' => 9000],
                ['nama' => 'Nasi Putih + Cumi Goreng Tepung', 'deskripsi' => 'Nasi putih dengan cumi goreng tepung crispy', 'harga' => 20000],
                ['nama' => 'Nasi Putih + Perkedel', 'deskripsi' => 'Nasi putih dengan perkedel kentang goreng', 'harga' => 10000],
                ['nama' => 'Nasi Putih + Semur Daging', 'deskripsi' => 'Nasi putih dengan semur daging sapi kecap', 'harga' => 22000],
                ['nama' => 'Sayur Asem', 'deskripsi' => 'Sayur asem segar khas Sunda', 'harga' => 5000],
                ['nama' => 'Sayur Lodeh', 'deskripsi' => 'Sayur lodeh santan dengan labu dan tempe', 'harga' => 5000],
                ['nama' => 'Es Teh Manis', 'deskripsi' => 'Teh manis dingin segar', 'harga' => 3000],
                ['nama' => 'Es Jeruk', 'deskripsi' => 'Jeruk peras segar dengan es batu', 'harga' => 5000],
                ['nama' => 'Air Mineral', 'deskripsi' => 'Air mineral kemasan gelas', 'harga' => 3000],
            ],
            'ayam_geprek' => [
                ['nama' => 'Ayam Geprek Original', 'deskripsi' => 'Ayam geprek dengan sambal bawang original', 'harga' => 15000, 'is_customizable' => true, 'customizations' => [
                    ['name' => 'Level Pedas', 'is_required' => true, 'options' => [
                        ['name' => 'Level 1 (Tidak Pedas)', 'price_adjustment' => 0],
                        ['name' => 'Level 3 (Sedang)', 'price_adjustment' => 0],
                        ['name' => 'Level 5 (Pedas)', 'price_adjustment' => 0],
                        ['name' => 'Level 7 (Sangat Pedas)', 'price_adjustment' => 2000],
                        ['name' => 'Level 10 (Gila Pedas)', 'price_adjustment' => 3000],
                    ]],
                ]],
                ['nama' => 'Ayam Geprek Sambal Matah', 'deskripsi' => 'Ayam geprek dengan sambal matah Bali', 'harga' => 17000],
                ['nama' => 'Ayam Geprek Sambal Ijo', 'deskripsi' => 'Ayam geprek dengan sambal hijau khas Padang', 'harga' => 17000],
                ['nama' => 'Ayam Geprek Mozarella', 'deskripsi' => 'Ayam geprek dengan topping keju mozarella leleh', 'harga' => 22000],
                ['nama' => 'Ayam Geprek Sambal Terasi', 'deskripsi' => 'Ayam geprek pedas dengan sambal terasi', 'harga' => 16000],
                ['nama' => 'Ayam Geprek Crispy', 'deskripsi' => 'Ayam geprek extra crispy double coating', 'harga' => 18000],
                ['nama' => 'Paket Ayam Geprek + Nasi + Es Teh', 'deskripsi' => 'Paket hemat ayam geprek lengkap', 'harga' => 20000],
                ['nama' => 'Ayam Geprek Sambal Bajak', 'deskripsi' => 'Ayam geprek dengan sambal bajak manis pedas', 'harga' => 17000],
                ['nama' => 'Ayam Geprek Saus Keju', 'deskripsi' => 'Ayam geprek disiram saus keju creamy', 'harga' => 20000],
                ['nama' => 'Nasi Putih', 'deskripsi' => 'Nasi putih hangat porsi standar', 'harga' => 4000],
                ['nama' => 'Nasi Jumbo', 'deskripsi' => 'Nasi putih porsi jumbo', 'harga' => 6000],
                ['nama' => 'Tahu Crispy (3 pcs)', 'deskripsi' => 'Tahu goreng crispy renyah', 'harga' => 5000],
                ['nama' => 'Tempe Goreng (3 pcs)', 'deskripsi' => 'Tempe goreng tepung garing', 'harga' => 5000],
                ['nama' => 'Es Teh Manis', 'deskripsi' => 'Teh manis dingin', 'harga' => 3000],
                ['nama' => 'Es Teh Tarik', 'deskripsi' => 'Teh tarik ala mamak', 'harga' => 7000],
                ['nama' => 'Es Jeruk', 'deskripsi' => 'Jeruk peras segar', 'harga' => 5000],
                ['nama' => 'Air Mineral', 'deskripsi' => 'Air mineral kemasan gelas', 'harga' => 3000],
            ],
            'kopi_minuman' => [
                ['nama' => 'Kopi Susu Gula Aren', 'deskripsi' => 'Es kopi susu dengan gula aren asli', 'harga' => 15000],
                ['nama' => 'Americano', 'deskripsi' => 'Espresso dengan air panas, bold dan strong', 'harga' => 12000],
                ['nama' => 'Cappuccino', 'deskripsi' => 'Espresso dengan steamed milk dan foam', 'harga' => 15000],
                ['nama' => 'Latte', 'deskripsi' => 'Espresso dengan susu creamy', 'harga' => 15000],
                ['nama' => 'Matcha Latte', 'deskripsi' => 'Green tea matcha dengan susu segar', 'harga' => 18000],
                ['nama' => 'Chocolate', 'deskripsi' => 'Coklat susu premium dingin', 'harga' => 15000],
                ['nama' => 'Taro Latte', 'deskripsi' => 'Taro creamy dengan susu segar', 'harga' => 16000],
                ['nama' => 'Red Velvet Latte', 'deskripsi' => 'Red velvet creamy dengan cream cheese', 'harga' => 18000],
                ['nama' => 'Thai Tea', 'deskripsi' => 'Teh Thailand klasik dengan susu', 'harga' => 12000],
                ['nama' => 'Vanilla Latte', 'deskripsi' => 'Espresso dengan susu dan sirup vanilla', 'harga' => 17000],
                ['nama' => 'Caramel Macchiato', 'deskripsi' => 'Espresso dengan susu dan karamel', 'harga' => 18000],
                ['nama' => 'V60 Manual Brew', 'deskripsi' => 'Single origin kopi diseduh manual V60', 'harga' => 20000],
                ['nama' => 'Roti Bakar Coklat Keju', 'deskripsi' => 'Roti bakar topping coklat dan keju', 'harga' => 12000],
                ['nama' => 'Croissant Butter', 'deskripsi' => 'Croissant renyah premium', 'harga' => 15000],
                ['nama' => 'Kentang Goreng', 'deskripsi' => 'French fries crispy', 'harga' => 12000],
                ['nama' => 'Pisang Goreng Keju Coklat', 'deskripsi' => 'Pisang goreng crispy dengan topping', 'harga' => 10000],
                ['nama' => 'Air Mineral', 'deskripsi' => 'Air mineral kemasan botol', 'harga' => 5000],
            ],
            'mie_bakso' => [
                ['nama' => 'Mie Ayam Original', 'deskripsi' => 'Mie ayam dengan topping ayam cincang gurih', 'harga' => 12000],
                ['nama' => 'Mie Ayam Bakso', 'deskripsi' => 'Mie ayam dengan tambahan bakso sapi', 'harga' => 15000],
                ['nama' => 'Mie Ayam Pangsit', 'deskripsi' => 'Mie ayam dengan pangsit goreng renyah', 'harga' => 14000],
                ['nama' => 'Mie Ayam Ceker', 'deskripsi' => 'Mie ayam dengan ceker ayam empuk', 'harga' => 16000],
                ['nama' => 'Mie Ayam Komplit', 'deskripsi' => 'Mie ayam dengan bakso, pangsit, dan ceker', 'harga' => 20000],
                ['nama' => 'Mie Yamin', 'deskripsi' => 'Mie kering manis ala Bandung', 'harga' => 13000],
                ['nama' => 'Mie Ayam Jumbo', 'deskripsi' => 'Mie ayam porsi jumbo double topping', 'harga' => 18000],
                ['nama' => 'Bakso Urat', 'deskripsi' => 'Bakso urat sapi kuah kaldu segar', 'harga' => 15000],
                ['nama' => 'Bakso Telur', 'deskripsi' => 'Bakso besar isi telur ayam', 'harga' => 12000],
                ['nama' => 'Bakso Campur', 'deskripsi' => 'Campuran bakso halus, urat, tahu, siomay', 'harga' => 18000],
                ['nama' => 'Mie Goreng Jawa', 'deskripsi' => 'Mie goreng bumbu Jawa manis gurih', 'harga' => 12000],
                ['nama' => 'Pangsit Goreng (5 pcs)', 'deskripsi' => 'Pangsit goreng renyah isi ayam', 'harga' => 8000],
                ['nama' => 'Tahu Bakso (3 pcs)', 'deskripsi' => 'Tahu isi bakso goreng', 'harga' => 6000],
                ['nama' => 'Nasi Putih', 'deskripsi' => 'Nasi putih hangat', 'harga' => 4000],
                ['nama' => 'Es Teh Manis', 'deskripsi' => 'Teh manis dingin segar', 'harga' => 3000],
                ['nama' => 'Es Jeruk', 'deskripsi' => 'Jeruk peras segar', 'harga' => 5000],
                ['nama' => 'Air Mineral', 'deskripsi' => 'Air mineral kemasan gelas', 'harga' => 3000],
            ],
            'sate' => [
                ['nama' => 'Sate Ayam (10 tusuk)', 'deskripsi' => 'Sate ayam bumbu kacang khas Madura', 'harga' => 15000],
                ['nama' => 'Sate Ayam (15 tusuk)', 'deskripsi' => 'Sate ayam porsi besar', 'harga' => 22000],
                ['nama' => 'Sate Kambing (10 tusuk)', 'deskripsi' => 'Sate kambing muda empuk bumbu kecap', 'harga' => 25000],
                ['nama' => 'Sate Kulit (10 tusuk)', 'deskripsi' => 'Sate kulit ayam crispy empuk', 'harga' => 12000],
                ['nama' => 'Sate Telur Puyuh (10 tusuk)', 'deskripsi' => 'Sate telur puyuh bumbu kacang', 'harga' => 10000],
                ['nama' => 'Sate Usus (10 tusuk)', 'deskripsi' => 'Sate usus ayam crispy', 'harga' => 10000],
                ['nama' => 'Tongseng Ayam', 'deskripsi' => 'Tongseng ayam kuah santan pedas', 'harga' => 18000],
                ['nama' => 'Tongseng Kambing', 'deskripsi' => 'Tongseng kambing kuah santan rempah', 'harga' => 25000],
                ['nama' => 'Gulai Kambing', 'deskripsi' => 'Gulai kambing kuah kuning kental', 'harga' => 25000],
                ['nama' => 'Sop Kambing', 'deskripsi' => 'Sop kambing kuah bening segar', 'harga' => 22000],
                ['nama' => 'Lontong / Nasi Putih', 'deskripsi' => 'Pilihan lontong atau nasi putih', 'harga' => 4000],
                ['nama' => 'Paket Sate Ayam + Lontong + Es Teh', 'deskripsi' => 'Paket hemat sate komplit', 'harga' => 20000],
                ['nama' => 'Acar Timun', 'deskripsi' => 'Acar timun bawang segar', 'harga' => 3000],
                ['nama' => 'Sambal Kacang Extra', 'deskripsi' => 'Tambahan sambal kacang', 'harga' => 3000],
                ['nama' => 'Es Teh Manis', 'deskripsi' => 'Teh manis dingin', 'harga' => 3000],
                ['nama' => 'Es Jeruk', 'deskripsi' => 'Jeruk peras segar', 'harga' => 5000],
                ['nama' => 'Air Mineral', 'deskripsi' => 'Air mineral kemasan gelas', 'harga' => 3000],
            ],
            'bakso_soto' => [
                ['nama' => 'Bakso Solo Original', 'deskripsi' => 'Bakso Solo asli kuah kaldu sapi', 'harga' => 15000],
                ['nama' => 'Bakso Urat Jumbo', 'deskripsi' => 'Bakso urat sapi besar kuah gurih', 'harga' => 18000],
                ['nama' => 'Bakso Beranak', 'deskripsi' => 'Bakso besar isi bakso kecil di dalamnya', 'harga' => 20000],
                ['nama' => 'Bakso Mercon', 'deskripsi' => 'Bakso pedas isi cabai rawit', 'harga' => 16000],
                ['nama' => 'Bakso Campur Spesial', 'deskripsi' => 'Bakso campur + tahu bakso + siomay', 'harga' => 22000],
                ['nama' => 'Soto Ayam', 'deskripsi' => 'Soto ayam kuah kuning segar', 'harga' => 15000],
                ['nama' => 'Soto Daging', 'deskripsi' => 'Soto daging sapi kuah bening', 'harga' => 20000],
                ['nama' => 'Mie Ayam Bakso', 'deskripsi' => 'Mie ayam dengan bakso', 'harga' => 15000],
                ['nama' => 'Bakso Tahu Goreng (3 pcs)', 'deskripsi' => 'Tahu goreng isi bakso', 'harga' => 8000],
                ['nama' => 'Siomay (5 pcs)', 'deskripsi' => 'Siomay ayam ikan saus kacang', 'harga' => 10000],
                ['nama' => 'Pangsit Goreng (5 pcs)', 'deskripsi' => 'Pangsit goreng renyah', 'harga' => 8000],
                ['nama' => 'Mie Goreng Bakso', 'deskripsi' => 'Mie goreng dengan irisan bakso', 'harga' => 14000],
                ['nama' => 'Nasi Putih', 'deskripsi' => 'Nasi putih hangat', 'harga' => 4000],
                ['nama' => 'Kerupuk (1 porsi)', 'deskripsi' => 'Kerupuk renyah', 'harga' => 2000],
                ['nama' => 'Es Teh Manis', 'deskripsi' => 'Teh manis dingin', 'harga' => 3000],
                ['nama' => 'Es Jeruk', 'deskripsi' => 'Jeruk peras segar', 'harga' => 5000],
                ['nama' => 'Air Mineral', 'deskripsi' => 'Air mineral kemasan gelas', 'harga' => 3000],
            ],
            'jus_es' => [
                ['nama' => 'Jus Alpukat', 'deskripsi' => 'Jus alpukat creamy dengan susu coklat', 'harga' => 10000],
                ['nama' => 'Jus Mangga', 'deskripsi' => 'Jus mangga harum manis segar', 'harga' => 8000],
                ['nama' => 'Jus Jeruk', 'deskripsi' => 'Jus jeruk peras segar', 'harga' => 8000],
                ['nama' => 'Jus Strawberry', 'deskripsi' => 'Jus strawberry asli manis segar', 'harga' => 10000],
                ['nama' => 'Jus Jambu', 'deskripsi' => 'Jus jambu merah segar', 'harga' => 8000],
                ['nama' => 'Jus Semangka', 'deskripsi' => 'Jus semangka merah segar', 'harga' => 8000],
                ['nama' => 'Jus Wortel', 'deskripsi' => 'Jus wortel sehat bergizi', 'harga' => 8000],
                ['nama' => 'Jus Naga', 'deskripsi' => 'Jus buah naga merah segar', 'harga' => 10000],
                ['nama' => 'Jus Tomat', 'deskripsi' => 'Jus tomat segar', 'harga' => 7000],
                ['nama' => 'Es Campur', 'deskripsi' => 'Es campur buah lengkap dengan sirup dan susu', 'harga' => 10000],
                ['nama' => 'Es Teler', 'deskripsi' => 'Es teler alpukat kelapa nangka', 'harga' => 12000],
                ['nama' => 'Es Doger', 'deskripsi' => 'Es doger khas Bandung', 'harga' => 8000],
                ['nama' => 'Smoothie Bowl', 'deskripsi' => 'Smoothie bowl buah segar', 'harga' => 15000],
                ['nama' => 'Es Kelapa Muda', 'deskripsi' => 'Air kelapa muda segar', 'harga' => 8000],
                ['nama' => 'Jus Alpukat Coklat', 'deskripsi' => 'Alpukat blended dengan coklat dan susu', 'harga' => 12000],
                ['nama' => 'Es Cincau', 'deskripsi' => 'Es cincau hijau dengan gula merah', 'harga' => 7000],
                ['nama' => 'Air Mineral', 'deskripsi' => 'Air mineral kemasan gelas', 'harga' => 3000],
            ],
            'nasi_goreng' => [
                ['nama' => 'Nasi Goreng Spesial', 'deskripsi' => 'Nasi goreng spesial dengan telur, ayam, dan kerupuk', 'harga' => 15000],
                ['nama' => 'Nasi Goreng Biasa', 'deskripsi' => 'Nasi goreng kampung sederhana', 'harga' => 10000],
                ['nama' => 'Nasi Goreng Seafood', 'deskripsi' => 'Nasi goreng dengan udang, cumi, dan ikan', 'harga' => 20000],
                ['nama' => 'Nasi Goreng Kambing', 'deskripsi' => 'Nasi goreng dengan daging kambing', 'harga' => 22000],
                ['nama' => 'Nasi Goreng Pete', 'deskripsi' => 'Nasi goreng dengan pete dan sambal terasi', 'harga' => 15000],
                ['nama' => 'Nasi Goreng Rendang', 'deskripsi' => 'Nasi goreng dengan topping rendang', 'harga' => 18000],
                ['nama' => 'Nasi Goreng Sosis', 'deskripsi' => 'Nasi goreng dengan sosis dan telur', 'harga' => 13000],
                ['nama' => 'Nasi Goreng Ayam', 'deskripsi' => 'Nasi goreng dengan ayam suwir', 'harga' => 14000],
                ['nama' => 'Mie Goreng Spesial', 'deskripsi' => 'Mie goreng bumbu spesial', 'harga' => 13000],
                ['nama' => 'Kwetiau Goreng', 'deskripsi' => 'Kwetiau goreng dengan telur dan sayuran', 'harga' => 13000],
                ['nama' => 'Bihun Goreng', 'deskripsi' => 'Bihun goreng dengan sayuran', 'harga' => 12000],
                ['nama' => 'Telur Ceplok', 'deskripsi' => 'Tambahan telur ceplok', 'harga' => 4000],
                ['nama' => 'Telur Dadar', 'deskripsi' => 'Tambahan telur dadar', 'harga' => 4000],
                ['nama' => 'Kerupuk (1 porsi)', 'deskripsi' => 'Kerupuk udang renyah', 'harga' => 2000],
                ['nama' => 'Es Teh Manis', 'deskripsi' => 'Teh manis dingin segar', 'harga' => 3000],
                ['nama' => 'Es Jeruk', 'deskripsi' => 'Jeruk peras segar', 'harga' => 5000],
                ['nama' => 'Air Mineral', 'deskripsi' => 'Air mineral kemasan gelas', 'harga' => 3000],
            ],
            'pecel_lele' => [
                ['nama' => 'Pecel Lele Goreng', 'deskripsi' => 'Ikan lele goreng garing dengan sambal lalapan', 'harga' => 13000],
                ['nama' => 'Pecel Lele Bakar', 'deskripsi' => 'Ikan lele bakar bumbu kecap', 'harga' => 15000],
                ['nama' => 'Ayam Goreng Lamongan', 'deskripsi' => 'Ayam goreng bumbu kuning khas Lamongan', 'harga' => 15000],
                ['nama' => 'Ayam Penyet', 'deskripsi' => 'Ayam goreng dipenyet dengan sambal', 'harga' => 16000],
                ['nama' => 'Ayam Bakar Madu', 'deskripsi' => 'Ayam bakar bumbu madu manis gurih', 'harga' => 18000],
                ['nama' => 'Ikan Nila Goreng', 'deskripsi' => 'Ikan nila goreng garing', 'harga' => 15000],
                ['nama' => 'Bebek Goreng', 'deskripsi' => 'Bebek goreng crispy empuk', 'harga' => 22000],
                ['nama' => 'Lele Penyet', 'deskripsi' => 'Ikan lele goreng dipenyet sambal pedas', 'harga' => 14000],
                ['nama' => 'Paket Komplit Lele', 'deskripsi' => 'Lele + nasi + es teh + lalapan', 'harga' => 18000],
                ['nama' => 'Nasi Putih', 'deskripsi' => 'Nasi putih hangat', 'harga' => 4000],
                ['nama' => 'Lalapan + Sambal', 'deskripsi' => 'Lalapan timun, kemangi, sambal', 'harga' => 3000],
                ['nama' => 'Tahu Goreng (3 pcs)', 'deskripsi' => 'Tahu goreng garing', 'harga' => 5000],
                ['nama' => 'Tempe Goreng (3 pcs)', 'deskripsi' => 'Tempe goreng crispy', 'harga' => 5000],
                ['nama' => 'Terong Goreng', 'deskripsi' => 'Terong goreng tepung', 'harga' => 5000],
                ['nama' => 'Es Teh Manis', 'deskripsi' => 'Teh manis dingin', 'harga' => 3000],
                ['nama' => 'Es Jeruk', 'deskripsi' => 'Jeruk peras segar', 'harga' => 5000],
                ['nama' => 'Air Mineral', 'deskripsi' => 'Air mineral kemasan gelas', 'harga' => 3000],
            ],
            'soto' => [
                ['nama' => 'Soto Ayam Original', 'deskripsi' => 'Soto ayam kuah kuning bening segar', 'harga' => 15000],
                ['nama' => 'Soto Ayam Spesial', 'deskripsi' => 'Soto ayam dengan telur rebus dan perkedel', 'harga' => 18000],
                ['nama' => 'Soto Daging Sapi', 'deskripsi' => 'Soto daging sapi empuk kuah gurih', 'harga' => 22000],
                ['nama' => 'Rawon', 'deskripsi' => 'Rawon daging sapi kuah hitam kluwek', 'harga' => 22000],
                ['nama' => 'Soto Babat', 'deskripsi' => 'Soto babat sapi kuah bening', 'harga' => 18000],
                ['nama' => 'Soto Mie', 'deskripsi' => 'Soto mie Bogor dengan daging dan risol', 'harga' => 18000],
                ['nama' => 'Soto Betawi', 'deskripsi' => 'Soto Betawi santan kental gurih', 'harga' => 20000],
                ['nama' => 'Empal Gentong', 'deskripsi' => 'Empal gentong khas Cirebon', 'harga' => 20000],
                ['nama' => 'Nasi Putih', 'deskripsi' => 'Nasi putih hangat', 'harga' => 4000],
                ['nama' => 'Perkedel (2 pcs)', 'deskripsi' => 'Perkedel kentang goreng', 'harga' => 5000],
                ['nama' => 'Telur Rebus', 'deskripsi' => 'Telur rebus matang', 'harga' => 4000],
                ['nama' => 'Sate Kerang (5 tusuk)', 'deskripsi' => 'Sate kerang bumbu kecap', 'harga' => 8000],
                ['nama' => 'Emping Melinjo', 'deskripsi' => 'Emping goreng renyah', 'harga' => 3000],
                ['nama' => 'Kerupuk Udang', 'deskripsi' => 'Kerupuk udang renyah', 'harga' => 3000],
                ['nama' => 'Es Teh Manis', 'deskripsi' => 'Teh manis dingin', 'harga' => 3000],
                ['nama' => 'Es Jeruk', 'deskripsi' => 'Jeruk peras segar', 'harga' => 5000],
                ['nama' => 'Air Mineral', 'deskripsi' => 'Air mineral kemasan gelas', 'harga' => 3000],
            ],
            'roti_snack' => [
                ['nama' => 'Roti Bakar Coklat', 'deskripsi' => 'Roti bakar isi coklat leleh', 'harga' => 10000],
                ['nama' => 'Roti Bakar Keju', 'deskripsi' => 'Roti bakar isi keju cheddar', 'harga' => 10000],
                ['nama' => 'Roti Bakar Coklat Keju', 'deskripsi' => 'Roti bakar isi coklat dan keju', 'harga' => 12000],
                ['nama' => 'Roti Bakar Strawberry', 'deskripsi' => 'Roti bakar isi selai strawberry', 'harga' => 10000],
                ['nama' => 'Roti Bakar Kacang', 'deskripsi' => 'Roti bakar isi selai kacang', 'harga' => 10000],
                ['nama' => 'Roti Bakar Ovomaltine', 'deskripsi' => 'Roti bakar isi ovomaltine crunchy', 'harga' => 14000],
                ['nama' => 'Roti Bakar Green Tea', 'deskripsi' => 'Roti bakar isi green tea cream', 'harga' => 14000],
                ['nama' => 'Roti Bakar Red Velvet', 'deskripsi' => 'Roti bakar isi red velvet cream', 'harga' => 14000],
                ['nama' => 'Pisang Bakar Coklat Keju', 'deskripsi' => 'Pisang bakar topping coklat dan keju', 'harga' => 12000],
                ['nama' => 'Pisang Nugget', 'deskripsi' => 'Pisang nugget crispy', 'harga' => 12000],
                ['nama' => 'French Fries', 'deskripsi' => 'Kentang goreng crispy', 'harga' => 12000],
                ['nama' => 'Sosis Bakar', 'deskripsi' => 'Sosis bakar bumbu BBQ', 'harga' => 8000],
                ['nama' => 'Susu Coklat', 'deskripsi' => 'Susu coklat dingin', 'harga' => 8000],
                ['nama' => 'Susu Strawberry', 'deskripsi' => 'Susu strawberry dingin', 'harga' => 8000],
                ['nama' => 'Es Teh Manis', 'deskripsi' => 'Teh manis dingin', 'harga' => 3000],
                ['nama' => 'Milo Dinosaur', 'deskripsi' => 'Milo ice dengan topping milo powder', 'harga' => 12000],
                ['nama' => 'Air Mineral', 'deskripsi' => 'Air mineral kemasan gelas', 'harga' => 3000],
            ],
            'masakan_sunda' => [
                ['nama' => 'Nasi Timbel Komplit', 'deskripsi' => 'Nasi timbel dengan ayam goreng, lalapan, sambal, tahu tempe', 'harga' => 22000],
                ['nama' => 'Nasi Liwet Sunda', 'deskripsi' => 'Nasi liwet dengan ikan asin, sambal, dan lalapan', 'harga' => 18000],
                ['nama' => 'Ayam Goreng Sunda', 'deskripsi' => 'Ayam goreng bumbu kuning khas Sunda', 'harga' => 16000],
                ['nama' => 'Gurame Goreng', 'deskripsi' => 'Ikan gurame goreng garing', 'harga' => 25000],
                ['nama' => 'Pepes Ikan Mas', 'deskripsi' => 'Pepes ikan mas bumbu wangi', 'harga' => 18000],
                ['nama' => 'Pepes Tahu', 'deskripsi' => 'Pepes tahu bumbu kemangi', 'harga' => 8000],
                ['nama' => 'Karedok', 'deskripsi' => 'Sayuran segar dengan bumbu kacang karedok', 'harga' => 10000],
                ['nama' => 'Lotek', 'deskripsi' => 'Sayuran rebus dengan bumbu kacang lotek', 'harga' => 10000],
                ['nama' => 'Sayur Asem', 'deskripsi' => 'Sayur asem segar khas Sunda', 'harga' => 8000],
                ['nama' => 'Sop Buntut', 'deskripsi' => 'Sop buntut sapi kuah bening segar', 'harga' => 28000],
                ['nama' => 'Empal Daging', 'deskripsi' => 'Empal daging sapi goreng manis', 'harga' => 18000],
                ['nama' => 'Sambel Dadak', 'deskripsi' => 'Sambal oncom khas Sunda', 'harga' => 5000],
                ['nama' => 'Tahu Sumedang (5 pcs)', 'deskripsi' => 'Tahu Sumedang goreng renyah', 'harga' => 8000],
                ['nama' => 'Nasi Putih', 'deskripsi' => 'Nasi putih hangat', 'harga' => 4000],
                ['nama' => 'Es Teh Manis', 'deskripsi' => 'Teh manis dingin', 'harga' => 3000],
                ['nama' => 'Es Jeruk', 'deskripsi' => 'Jeruk peras segar', 'harga' => 5000],
                ['nama' => 'Air Mineral', 'deskripsi' => 'Air mineral kemasan gelas', 'harga' => 3000],
            ],
            'dimsum' => [
                ['nama' => 'Hakao (4 pcs)', 'deskripsi' => 'Dimsum hakao udang kukus', 'harga' => 12000],
                ['nama' => 'Siomay Ayam (4 pcs)', 'deskripsi' => 'Siomay ayam kukus lembut', 'harga' => 10000],
                ['nama' => 'Lumpia Udang (3 pcs)', 'deskripsi' => 'Lumpia goreng isi udang renyah', 'harga' => 12000],
                ['nama' => 'Ceker Ayam Saus Tiram', 'deskripsi' => 'Ceker ayam empuk saus tiram', 'harga' => 12000],
                ['nama' => 'Bakpao Ayam (2 pcs)', 'deskripsi' => 'Bakpao isi ayam lembut', 'harga' => 10000],
                ['nama' => 'Pangsit Goreng (5 pcs)', 'deskripsi' => 'Pangsit goreng crispy', 'harga' => 10000],
                ['nama' => 'Tahu Udang (3 pcs)', 'deskripsi' => 'Tahu isi udang kukus', 'harga' => 10000],
                ['nama' => 'Xiao Long Bao (4 pcs)', 'deskripsi' => 'Dumpling kuah isi daging', 'harga' => 15000],
                ['nama' => 'Paket Dimsum A (8 pcs)', 'deskripsi' => 'Campuran hakao, siomay, lumpia', 'harga' => 20000],
                ['nama' => 'Paket Dimsum B (12 pcs)', 'deskripsi' => 'Paket besar aneka dimsum', 'harga' => 28000],
                ['nama' => 'Pempek Kapal Selam', 'deskripsi' => 'Pempek besar isi telur', 'harga' => 10000],
                ['nama' => 'Pempek Lenjer (3 pcs)', 'deskripsi' => 'Pempek lenjer dengan cuko', 'harga' => 8000],
                ['nama' => 'Mie Goreng Hong Kong', 'deskripsi' => 'Mie goreng gaya Hong Kong', 'harga' => 15000],
                ['nama' => 'Nasi Goreng Hongkong', 'deskripsi' => 'Nasi goreng ala Hong Kong', 'harga' => 15000],
                ['nama' => 'Es Teh Manis', 'deskripsi' => 'Teh manis dingin', 'harga' => 3000],
                ['nama' => 'Es Lemon Tea', 'deskripsi' => 'Lemon tea segar', 'harga' => 7000],
                ['nama' => 'Air Mineral', 'deskripsi' => 'Air mineral kemasan gelas', 'harga' => 3000],
            ],
            'martabak' => [
                ['nama' => 'Martabak Manis Coklat', 'deskripsi' => 'Martabak manis isi coklat', 'harga' => 15000],
                ['nama' => 'Martabak Manis Keju', 'deskripsi' => 'Martabak manis isi keju cheddar', 'harga' => 15000],
                ['nama' => 'Martabak Manis Coklat Keju', 'deskripsi' => 'Martabak manis coklat dan keju', 'harga' => 18000],
                ['nama' => 'Martabak Manis Kacang', 'deskripsi' => 'Martabak manis isi kacang wijen', 'harga' => 15000],
                ['nama' => 'Martabak Manis Ovomaltine', 'deskripsi' => 'Martabak manis topping ovomaltine', 'harga' => 20000],
                ['nama' => 'Martabak Manis Red Velvet', 'deskripsi' => 'Martabak manis rasa red velvet', 'harga' => 20000],
                ['nama' => 'Martabak Manis Green Tea', 'deskripsi' => 'Martabak manis rasa green tea', 'harga' => 18000],
                ['nama' => 'Martabak Telur Ayam', 'deskripsi' => 'Martabak telur isi daging ayam cincang', 'harga' => 18000],
                ['nama' => 'Martabak Telur Daging', 'deskripsi' => 'Martabak telur isi daging sapi', 'harga' => 22000],
                ['nama' => 'Martabak Telur Spesial', 'deskripsi' => 'Martabak telur isi daging, telur, daun bawang', 'harga' => 25000],
                ['nama' => 'Martabak Mini Mix (4 pcs)', 'deskripsi' => 'Mini martabak aneka rasa', 'harga' => 15000],
                ['nama' => 'Terang Bulan', 'deskripsi' => 'Terang bulan klasik isi kacang', 'harga' => 12000],
                ['nama' => 'Martabak Tipis Crispy', 'deskripsi' => 'Martabak tipis garing renyah', 'harga' => 12000],
                ['nama' => 'Kulit Martabak Goreng', 'deskripsi' => 'Kulit martabak goreng crispy', 'harga' => 5000],
                ['nama' => 'Es Teh Manis', 'deskripsi' => 'Teh manis dingin', 'harga' => 3000],
                ['nama' => 'Es Coklat', 'deskripsi' => 'Susu coklat dingin', 'harga' => 8000],
                ['nama' => 'Air Mineral', 'deskripsi' => 'Air mineral kemasan gelas', 'harga' => 3000],
            ],
            'rice_bowl' => [
                ['nama' => 'Rice Bowl Chicken Katsu', 'deskripsi' => 'Nasi dengan chicken katsu dan saus teriyaki', 'harga' => 18000],
                ['nama' => 'Rice Bowl Beef Teriyaki', 'deskripsi' => 'Nasi dengan daging sapi saus teriyaki', 'harga' => 22000],
                ['nama' => 'Rice Bowl Salted Egg Chicken', 'deskripsi' => 'Nasi dengan ayam crispy saus telur asin', 'harga' => 20000],
                ['nama' => 'Rice Bowl Chicken BBQ', 'deskripsi' => 'Nasi dengan ayam panggang saus BBQ', 'harga' => 18000],
                ['nama' => 'Rice Bowl Sambal Matah', 'deskripsi' => 'Nasi dengan ayam crispy sambal matah', 'harga' => 18000],
                ['nama' => 'Rice Bowl Blackpepper Beef', 'deskripsi' => 'Nasi dengan daging sapi lada hitam', 'harga' => 22000],
                ['nama' => 'Rice Bowl Chicken Mozarella', 'deskripsi' => 'Nasi dengan ayam crispy dan keju mozarella', 'harga' => 22000],
                ['nama' => 'Rice Bowl Rendang', 'deskripsi' => 'Nasi dengan rendang daging empuk', 'harga' => 22000],
                ['nama' => 'Rice Bowl Chicken Geprek', 'deskripsi' => 'Nasi dengan ayam geprek sambal', 'harga' => 16000],
                ['nama' => 'Rice Bowl Egg Mayo', 'deskripsi' => 'Nasi dengan telur mayo dan sayuran', 'harga' => 14000],
                ['nama' => 'Rice Bowl Dori Crispy', 'deskripsi' => 'Nasi dengan ikan dori crispy', 'harga' => 20000],
                ['nama' => 'French Fries', 'deskripsi' => 'Kentang goreng crispy', 'harga' => 10000],
                ['nama' => 'Chicken Wings (4 pcs)', 'deskripsi' => 'Sayap ayam goreng bumbu', 'harga' => 15000],
                ['nama' => 'Salad Bowl', 'deskripsi' => 'Salad segar dengan dressing', 'harga' => 10000],
                ['nama' => 'Es Lemon Tea', 'deskripsi' => 'Lemon tea segar', 'harga' => 7000],
                ['nama' => 'Es Teh Manis', 'deskripsi' => 'Teh manis dingin', 'harga' => 3000],
                ['nama' => 'Air Mineral', 'deskripsi' => 'Air mineral kemasan gelas', 'harga' => 3000],
            ],
            'masakan_padang' => [
                ['nama' => 'Nasi Padang Rendang', 'deskripsi' => 'Nasi dengan rendang daging sapi asli Padang', 'harga' => 22000],
                ['nama' => 'Nasi Padang Ayam Pop', 'deskripsi' => 'Nasi dengan ayam pop khas Padang', 'harga' => 18000],
                ['nama' => 'Nasi Padang Dendeng Balado', 'deskripsi' => 'Nasi dengan dendeng batokok balado', 'harga' => 20000],
                ['nama' => 'Nasi Padang Ayam Bakar', 'deskripsi' => 'Nasi dengan ayam bakar bumbu Padang', 'harga' => 18000],
                ['nama' => 'Nasi Padang Gulai Otak', 'deskripsi' => 'Nasi dengan gulai otak sapi', 'harga' => 18000],
                ['nama' => 'Nasi Padang Gulai Tunjang', 'deskripsi' => 'Nasi dengan gulai tunjang (kikil)', 'harga' => 20000],
                ['nama' => 'Nasi Padang Ikan Bakar', 'deskripsi' => 'Nasi dengan ikan bakar bumbu Padang', 'harga' => 20000],
                ['nama' => 'Nasi Padang Telur Dadar', 'deskripsi' => 'Nasi dengan telur dadar Padang', 'harga' => 12000],
                ['nama' => 'Nasi Padang Perkedel', 'deskripsi' => 'Nasi dengan perkedel kentang', 'harga' => 10000],
                ['nama' => 'Gulai Ayam', 'deskripsi' => 'Gulai ayam kuah kuning kental', 'harga' => 18000],
                ['nama' => 'Sayur Nangka', 'deskripsi' => 'Gulai nangka muda', 'harga' => 8000],
                ['nama' => 'Sayur Daun Singkong', 'deskripsi' => 'Daun singkong santan', 'harga' => 6000],
                ['nama' => 'Sambal Hijau', 'deskripsi' => 'Sambal ijo khas Padang', 'harga' => 3000],
                ['nama' => 'Kerupuk Jengkol', 'deskripsi' => 'Kerupuk jengkol goreng', 'harga' => 3000],
                ['nama' => 'Es Teh Manis', 'deskripsi' => 'Teh manis dingin', 'harga' => 3000],
                ['nama' => 'Teh Talua', 'deskripsi' => 'Teh telur khas Padang', 'harga' => 8000],
                ['nama' => 'Air Mineral', 'deskripsi' => 'Air mineral kemasan gelas', 'harga' => 3000],
            ],
            'teh_minuman' => [
                ['nama' => 'Teh Tarik', 'deskripsi' => 'Teh tarik ala mamak', 'harga' => 8000],
                ['nama' => 'Thai Tea', 'deskripsi' => 'Teh Thailand klasik manis', 'harga' => 10000],
                ['nama' => 'Teh Susu', 'deskripsi' => 'Teh susu creamy', 'harga' => 8000],
                ['nama' => 'Teh Manis Hangat', 'deskripsi' => 'Teh manis sajian hangat', 'harga' => 4000],
                ['nama' => 'Teh Manis Dingin', 'deskripsi' => 'Teh manis dingin menyegarkan', 'harga' => 5000],
                ['nama' => 'Teh Lemon', 'deskripsi' => 'Teh lemon segar asam manis', 'harga' => 7000],
                ['nama' => 'Teh Bunga / Chamomile', 'deskripsi' => 'Teh bunga herbal menenangkan', 'harga' => 10000],
                ['nama' => 'Green Tea Latte', 'deskripsi' => 'Green tea matcha dengan susu', 'harga' => 12000],
                ['nama' => 'Milo Dingin', 'deskripsi' => 'Milo susu coklat dingin', 'harga' => 8000],
                ['nama' => 'Milo Dinosaur', 'deskripsi' => 'Milo ice topping milo powder', 'harga' => 12000],
                ['nama' => 'Susu Coklat', 'deskripsi' => 'Susu coklat segar', 'harga' => 8000],
                ['nama' => 'Susu Vanilla', 'deskripsi' => 'Susu vanilla segar', 'harga' => 8000],
                ['nama' => 'Roti Panggang Mentega', 'deskripsi' => 'Roti panggang mentega klasik', 'harga' => 7000],
                ['nama' => 'Roti Panggang Srikaya', 'deskripsi' => 'Roti panggang isi selai srikaya', 'harga' => 8000],
                ['nama' => 'Gorengan (3 pcs)', 'deskripsi' => 'Campuran bakwan, tahu, tempe goreng', 'harga' => 5000],
                ['nama' => 'Pisang Goreng', 'deskripsi' => 'Pisang goreng tepung', 'harga' => 5000],
                ['nama' => 'Air Mineral', 'deskripsi' => 'Air mineral kemasan gelas', 'harga' => 3000],
            ],
            'bubur' => [
                ['nama' => 'Bubur Ayam Original', 'deskripsi' => 'Bubur ayam dengan cakue, kerupuk, dan kecap', 'harga' => 12000],
                ['nama' => 'Bubur Ayam Spesial', 'deskripsi' => 'Bubur ayam dengan telur puyuh dan sate usus', 'harga' => 16000],
                ['nama' => 'Bubur Ayam Komplet', 'deskripsi' => 'Bubur ayam lengkap semua topping', 'harga' => 18000],
                ['nama' => 'Bubur Telur', 'deskripsi' => 'Bubur dengan telur setengah matang', 'harga' => 10000],
                ['nama' => 'Bubur Daging', 'deskripsi' => 'Bubur dengan irisan daging sapi', 'harga' => 18000],
                ['nama' => 'Bubur Polos', 'deskripsi' => 'Bubur polos tanpa topping', 'harga' => 6000],
                ['nama' => 'Bubur Seafood', 'deskripsi' => 'Bubur dengan topping udang dan cumi', 'harga' => 20000],
                ['nama' => 'Tambah Cakue (2 pcs)', 'deskripsi' => 'Cakue goreng renyah', 'harga' => 4000],
                ['nama' => 'Tambah Telur Puyuh (3 pcs)', 'deskripsi' => 'Telur puyuh rebus', 'harga' => 4000],
                ['nama' => 'Tambah Sate Usus (3 tusuk)', 'deskripsi' => 'Sate usus ayam goreng', 'harga' => 5000],
                ['nama' => 'Tambah Ayam Suwir', 'deskripsi' => 'Ekstra ayam suwir', 'harga' => 5000],
                ['nama' => 'Kerupuk (1 porsi)', 'deskripsi' => 'Kerupuk renyah', 'harga' => 2000],
                ['nama' => 'Gorengan Mix (3 pcs)', 'deskripsi' => 'Bakwan dan tahu goreng', 'harga' => 5000],
                ['nama' => 'Bubur Kacang Hijau', 'deskripsi' => 'Bubur kacang hijau hangat manis', 'harga' => 7000],
                ['nama' => 'Es Teh Manis', 'deskripsi' => 'Teh manis dingin', 'harga' => 3000],
                ['nama' => 'Kopi Tubruk', 'deskripsi' => 'Kopi tubruk tradisional', 'harga' => 5000],
                ['nama' => 'Air Mineral', 'deskripsi' => 'Air mineral kemasan gelas', 'harga' => 3000],
            ],
            'ayam_bakar' => [
                ['nama' => 'Ayam Bakar Original', 'deskripsi' => 'Ayam bakar bumbu kecap manis', 'harga' => 18000],
                ['nama' => 'Ayam Bakar Madu', 'deskripsi' => 'Ayam bakar glaze madu', 'harga' => 20000],
                ['nama' => 'Ayam Bakar Taliwang', 'deskripsi' => 'Ayam bakar pedas khas Lombok', 'harga' => 20000],
                ['nama' => 'Ayam Bakar Padang', 'deskripsi' => 'Ayam bakar bumbu Padang rempah', 'harga' => 18000],
                ['nama' => 'Ayam Goreng Kremes', 'deskripsi' => 'Ayam goreng dengan kremesan renyah', 'harga' => 16000],
                ['nama' => 'Ayam Goreng Lengkuas', 'deskripsi' => 'Ayam goreng bumbu lengkuas khas', 'harga' => 16000],
                ['nama' => 'Ayam Goreng Butter', 'deskripsi' => 'Ayam goreng saus mentega', 'harga' => 18000],
                ['nama' => 'Paket Ayam Bakar Komplit', 'deskripsi' => 'Ayam bakar + nasi + es teh + lalapan', 'harga' => 22000],
                ['nama' => 'Nasi Putih', 'deskripsi' => 'Nasi putih hangat', 'harga' => 4000],
                ['nama' => 'Lalapan Komplit', 'deskripsi' => 'Timun, kemangi, kubis, sambal', 'harga' => 3000],
                ['nama' => 'Tahu Goreng (3 pcs)', 'deskripsi' => 'Tahu goreng garing', 'harga' => 5000],
                ['nama' => 'Tempe Goreng (3 pcs)', 'deskripsi' => 'Tempe goreng crispy', 'harga' => 5000],
                ['nama' => 'Sambel Terasi', 'deskripsi' => 'Sambal terasi pedas', 'harga' => 3000],
                ['nama' => 'Sayur Asem', 'deskripsi' => 'Sayur asem segar', 'harga' => 5000],
                ['nama' => 'Es Teh Manis', 'deskripsi' => 'Teh manis dingin', 'harga' => 3000],
                ['nama' => 'Es Jeruk', 'deskripsi' => 'Jeruk peras segar', 'harga' => 5000],
                ['nama' => 'Air Mineral', 'deskripsi' => 'Air mineral kemasan gelas', 'harga' => 3000],
            ],
            'seblak' => [
                ['nama' => 'Seblak Original', 'deskripsi' => 'Seblak kerupuk basah pedas original', 'harga' => 10000],
                ['nama' => 'Seblak Komplit', 'deskripsi' => 'Seblak dengan bakso, ceker, telur, mie', 'harga' => 18000],
                ['nama' => 'Seblak Ceker', 'deskripsi' => 'Seblak pedas dengan ceker ayam', 'harga' => 14000],
                ['nama' => 'Seblak Bakso', 'deskripsi' => 'Seblak pedas dengan bakso sapi', 'harga' => 14000],
                ['nama' => 'Seblak Mie', 'deskripsi' => 'Seblak pedas dengan mie', 'harga' => 12000],
                ['nama' => 'Seblak Tulang', 'deskripsi' => 'Seblak pedas dengan tulang ayam', 'harga' => 14000],
                ['nama' => 'Batagor Original', 'deskripsi' => 'Batagor ikan tenggiri saus kacang', 'harga' => 10000],
                ['nama' => 'Batagor Jumbo', 'deskripsi' => 'Batagor porsi besar lengkap', 'harga' => 15000],
                ['nama' => 'Siomay Bandung', 'deskripsi' => 'Siomay Bandung lengkap saus kacang', 'harga' => 12000],
                ['nama' => 'Cilok Goreng', 'deskripsi' => 'Cilok goreng bumbu rujak', 'harga' => 8000],
                ['nama' => 'Cilok Kuah', 'deskripsi' => 'Cilok kuah bumbu kacang', 'harga' => 8000],
                ['nama' => 'Cuanki', 'deskripsi' => 'Cuanki kuah kaldu hangat', 'harga' => 10000],
                ['nama' => 'Cimol Crispy', 'deskripsi' => 'Cimol goreng bumbu tabur', 'harga' => 8000],
                ['nama' => 'Tahu Gejrot', 'deskripsi' => 'Tahu goreng saus gejrot pedas', 'harga' => 8000],
                ['nama' => 'Es Teh Manis', 'deskripsi' => 'Teh manis dingin', 'harga' => 3000],
                ['nama' => 'Es Jeruk', 'deskripsi' => 'Jeruk peras segar', 'harga' => 5000],
                ['nama' => 'Air Mineral', 'deskripsi' => 'Air mineral kemasan gelas', 'harga' => 3000],
            ],
            'mie_kocok' => [
                ['nama' => 'Mie Kocok Original', 'deskripsi' => 'Mie kocok kuah kaldu sapi Bandung', 'harga' => 15000],
                ['nama' => 'Mie Kocok Spesial', 'deskripsi' => 'Mie kocok dengan kikil dan daging sapi', 'harga' => 20000],
                ['nama' => 'Mie Kocok Kikil', 'deskripsi' => 'Mie kocok extra kikil sapi', 'harga' => 18000],
                ['nama' => 'Mie Kocok Bakso', 'deskripsi' => 'Mie kocok dengan bakso sapi', 'harga' => 17000],
                ['nama' => 'Bihun Kocok', 'deskripsi' => 'Bihun kuah kaldu sapi', 'harga' => 14000],
                ['nama' => 'Kwetiau Kuah', 'deskripsi' => 'Kwetiau kuah kaldu sapi', 'harga' => 14000],
                ['nama' => 'Mie Kocok Jumbo', 'deskripsi' => 'Mie kocok porsi jumbo', 'harga' => 22000],
                ['nama' => 'Mie Goreng Bandung', 'deskripsi' => 'Mie goreng khas Bandung', 'harga' => 14000],
                ['nama' => 'Kwetiau Goreng', 'deskripsi' => 'Kwetiau goreng seafood', 'harga' => 14000],
                ['nama' => 'Bihun Goreng', 'deskripsi' => 'Bihun goreng pedas', 'harga' => 12000],
                ['nama' => 'Sop Kikil', 'deskripsi' => 'Sop kikil sapi kuah bening', 'harga' => 18000],
                ['nama' => 'Nasi Putih', 'deskripsi' => 'Nasi putih hangat', 'harga' => 4000],
                ['nama' => 'Kerupuk Kulit (1 porsi)', 'deskripsi' => 'Kerupuk kulit sapi renyah', 'harga' => 3000],
                ['nama' => 'Telur Rebus', 'deskripsi' => 'Telur rebus', 'harga' => 4000],
                ['nama' => 'Es Teh Manis', 'deskripsi' => 'Teh manis dingin', 'harga' => 3000],
                ['nama' => 'Es Jeruk', 'deskripsi' => 'Jeruk peras segar', 'harga' => 5000],
                ['nama' => 'Air Mineral', 'deskripsi' => 'Air mineral kemasan gelas', 'harga' => 3000],
            ],
            'nasi_uduk' => [
                ['nama' => 'Nasi Uduk Ayam Goreng', 'deskripsi' => 'Nasi uduk dengan ayam goreng rempah', 'harga' => 16000],
                ['nama' => 'Nasi Uduk Ayam Bakar', 'deskripsi' => 'Nasi uduk dengan ayam bakar', 'harga' => 18000],
                ['nama' => 'Nasi Uduk Empal', 'deskripsi' => 'Nasi uduk dengan empal daging sapi', 'harga' => 18000],
                ['nama' => 'Nasi Uduk Telur Balado', 'deskripsi' => 'Nasi uduk dengan telur balado', 'harga' => 12000],
                ['nama' => 'Nasi Uduk Komplit', 'deskripsi' => 'Nasi uduk dengan ayam, telur, tempe, orek', 'harga' => 20000],
                ['nama' => 'Nasi Uduk Ikan Teri', 'deskripsi' => 'Nasi uduk dengan ikan teri kacang', 'harga' => 12000],
                ['nama' => 'Nasi Uduk Semur Daging', 'deskripsi' => 'Nasi uduk dengan semur daging sapi', 'harga' => 20000],
                ['nama' => 'Nasi Uduk Orek Tempe', 'deskripsi' => 'Nasi uduk dengan orek tempe manis', 'harga' => 10000],
                ['nama' => 'Nasi Uduk Tahu Tempe', 'deskripsi' => 'Nasi uduk dengan tahu tempe goreng', 'harga' => 10000],
                ['nama' => 'Tambah Ayam Goreng', 'deskripsi' => 'Tambahan ayam goreng rempah', 'harga' => 10000],
                ['nama' => 'Tambah Telur Balado', 'deskripsi' => 'Tambahan telur balado', 'harga' => 5000],
                ['nama' => 'Tambah Perkedel', 'deskripsi' => 'Tambahan perkedel kentang', 'harga' => 4000],
                ['nama' => 'Kerupuk Udang', 'deskripsi' => 'Kerupuk udang renyah', 'harga' => 3000],
                ['nama' => 'Sambal Kacang', 'deskripsi' => 'Sambal kacang khas Betawi', 'harga' => 2000],
                ['nama' => 'Es Teh Manis', 'deskripsi' => 'Teh manis dingin', 'harga' => 3000],
                ['nama' => 'Es Jeruk', 'deskripsi' => 'Jeruk peras segar', 'harga' => 5000],
                ['nama' => 'Air Mineral', 'deskripsi' => 'Air mineral kemasan gelas', 'harga' => 3000],
            ],
            'healthy' => [
                ['nama' => 'Salad Bowl Classic', 'deskripsi' => 'Mix salad segar dengan dressing olive oil', 'harga' => 15000],
                ['nama' => 'Salad Bowl Caesar', 'deskripsi' => 'Caesar salad dengan croutons dan parmesan', 'harga' => 18000],
                ['nama' => 'Salad Bowl Chicken', 'deskripsi' => 'Salad segar dengan grilled chicken breast', 'harga' => 22000],
                ['nama' => 'Smoothie Bowl Acai', 'deskripsi' => 'Acai smoothie bowl dengan granola', 'harga' => 22000],
                ['nama' => 'Smoothie Bowl Berry', 'deskripsi' => 'Mixed berry smoothie bowl', 'harga' => 20000],
                ['nama' => 'Overnight Oats', 'deskripsi' => 'Oats overnight dengan buah segar', 'harga' => 15000],
                ['nama' => 'Sandwich Chicken Pesto', 'deskripsi' => 'Roti gandum dengan ayam pesto', 'harga' => 18000],
                ['nama' => 'Wrap Tuna Mayo', 'deskripsi' => 'Tortilla wrap dengan tuna mayo', 'harga' => 16000],
                ['nama' => 'Wrap Chicken Caesar', 'deskripsi' => 'Tortilla wrap chicken caesar', 'harga' => 18000],
                ['nama' => 'Granola Bowl', 'deskripsi' => 'Granola dengan yogurt dan buah', 'harga' => 18000],
                ['nama' => 'Green Juice', 'deskripsi' => 'Jus sayuran hijau (bayam, apel, seledri)', 'harga' => 12000],
                ['nama' => 'Infused Water', 'deskripsi' => 'Air infused lemon mint', 'harga' => 8000],
                ['nama' => 'Cold Pressed Juice', 'deskripsi' => 'Cold pressed juice buah segar', 'harga' => 15000],
                ['nama' => 'Yogurt Cup', 'deskripsi' => 'Greek yogurt dengan topping buah', 'harga' => 12000],
                ['nama' => 'Banana Smoothie', 'deskripsi' => 'Smoothie pisang oat', 'harga' => 12000],
                ['nama' => 'Protein Shake', 'deskripsi' => 'Protein shake chocolate', 'harga' => 18000],
                ['nama' => 'Air Mineral', 'deskripsi' => 'Air mineral kemasan botol', 'harga' => 5000],
            ],
            'indomie' => [
                ['nama' => 'Indomie Goreng', 'deskripsi' => 'Indomie goreng original', 'harga' => 7000],
                ['nama' => 'Indomie Kuah', 'deskripsi' => 'Indomie kuah soto', 'harga' => 7000],
                ['nama' => 'Indomie Goreng Telur', 'deskripsi' => 'Indomie goreng dengan telur ceplok', 'harga' => 10000],
                ['nama' => 'Indomie Goreng Sosis', 'deskripsi' => 'Indomie goreng dengan sosis', 'harga' => 11000],
                ['nama' => 'Indomie Goreng Kornet', 'deskripsi' => 'Indomie goreng dengan kornet daging', 'harga' => 12000],
                ['nama' => 'Indomie Goreng Rendang', 'deskripsi' => 'Indomie goreng rasa rendang', 'harga' => 8000],
                ['nama' => 'Indomie Goreng Aceh', 'deskripsi' => 'Indomie goreng bumbu Aceh pedas', 'harga' => 12000],
                ['nama' => 'Indomie Nyemek', 'deskripsi' => 'Indomie setengah kuah setengah goreng', 'harga' => 8000],
                ['nama' => 'Indomie Double (2 bungkus)', 'deskripsi' => 'Indomie goreng double porsi', 'harga' => 12000],
                ['nama' => 'Indomie Seblak', 'deskripsi' => 'Indomie kuah bumbu seblak pedas', 'harga' => 12000],
                ['nama' => 'Indomie Goreng Cheese', 'deskripsi' => 'Indomie goreng topping keju', 'harga' => 12000],
                ['nama' => 'Indomie Kuah Bakso', 'deskripsi' => 'Indomie kuah dengan bakso', 'harga' => 12000],
                ['nama' => 'Nasi Goreng Indomie', 'deskripsi' => 'Nasi goreng bumbu Indomie', 'harga' => 12000],
                ['nama' => 'Telur Ceplok', 'deskripsi' => 'Tambahan telur ceplok', 'harga' => 4000],
                ['nama' => 'Es Teh Manis', 'deskripsi' => 'Teh manis dingin', 'harga' => 3000],
                ['nama' => 'Es Jeruk', 'deskripsi' => 'Jeruk peras segar', 'harga' => 5000],
                ['nama' => 'Air Mineral', 'deskripsi' => 'Air mineral kemasan gelas', 'harga' => 3000],
            ],
            'kebab' => [
                ['nama' => 'Kebab Original', 'deskripsi' => 'Kebab daging sapi original', 'harga' => 15000],
                ['nama' => 'Kebab Jumbo', 'deskripsi' => 'Kebab daging sapi porsi besar', 'harga' => 20000],
                ['nama' => 'Kebab Keju', 'deskripsi' => 'Kebab daging sapi extra keju', 'harga' => 18000],
                ['nama' => 'Kebab Chicken', 'deskripsi' => 'Kebab isi ayam', 'harga' => 14000],
                ['nama' => 'Kebab Double Cheese', 'deskripsi' => 'Kebab double keju mozarella', 'harga' => 22000],
                ['nama' => 'Burger Beef Original', 'deskripsi' => 'Burger daging sapi klasik', 'harga' => 18000],
                ['nama' => 'Burger Beef Cheese', 'deskripsi' => 'Burger daging sapi dengan keju', 'harga' => 20000],
                ['nama' => 'Burger Chicken', 'deskripsi' => 'Burger ayam crispy', 'harga' => 15000],
                ['nama' => 'Burger Double Beef', 'deskripsi' => 'Burger double patty daging sapi', 'harga' => 25000],
                ['nama' => 'Hotdog Original', 'deskripsi' => 'Hotdog sosis sapi original', 'harga' => 12000],
                ['nama' => 'Hotdog Cheese', 'deskripsi' => 'Hotdog sosis sapi extra keju', 'harga' => 15000],
                ['nama' => 'French Fries', 'deskripsi' => 'Kentang goreng crispy', 'harga' => 10000],
                ['nama' => 'Onion Rings', 'deskripsi' => 'Bawang goreng tepung crispy', 'harga' => 10000],
                ['nama' => 'Nugget (5 pcs)', 'deskripsi' => 'Chicken nugget crispy', 'harga' => 10000],
                ['nama' => 'Es Lemon Tea', 'deskripsi' => 'Lemon tea segar', 'harga' => 7000],
                ['nama' => 'Es Teh Manis', 'deskripsi' => 'Teh manis dingin', 'harga' => 3000],
                ['nama' => 'Air Mineral', 'deskripsi' => 'Air mineral kemasan gelas', 'harga' => 3000],
            ],
            'es_tradisional' => [
                ['nama' => 'Es Cendol', 'deskripsi' => 'Es cendol gula merah santan', 'harga' => 8000],
                ['nama' => 'Es Dawet', 'deskripsi' => 'Es dawet ayu Banjarnegara', 'harga' => 8000],
                ['nama' => 'Es Doger', 'deskripsi' => 'Es doger khas Bandung', 'harga' => 8000],
                ['nama' => 'Es Cincau', 'deskripsi' => 'Es cincau hijau gula merah', 'harga' => 7000],
                ['nama' => 'Es Campur', 'deskripsi' => 'Es campur buah lengkap', 'harga' => 10000],
                ['nama' => 'Es Teler', 'deskripsi' => 'Es teler alpukat kelapa nangka', 'harga' => 12000],
                ['nama' => 'Es Kolak Pisang', 'deskripsi' => 'Kolak pisang dingin dengan santan', 'harga' => 8000],
                ['nama' => 'Es Kelapa Muda', 'deskripsi' => 'Kelapa muda segar', 'harga' => 8000],
                ['nama' => 'Es Blewah', 'deskripsi' => 'Es blewah sirup manis', 'harga' => 7000],
                ['nama' => 'Es Timun Selasih', 'deskripsi' => 'Es timun dengan biji selasih', 'harga' => 7000],
                ['nama' => 'Bajigur', 'deskripsi' => 'Minuman hangat gula aren santan', 'harga' => 8000],
                ['nama' => 'Bandrek', 'deskripsi' => 'Minuman hangat jahe gula aren', 'harga' => 8000],
                ['nama' => 'Wedang Jahe', 'deskripsi' => 'Jahe hangat manis', 'harga' => 5000],
                ['nama' => 'Wedang Ronde', 'deskripsi' => 'Wedang ronde hangat dengan kacang', 'harga' => 10000],
                ['nama' => 'Sekoteng', 'deskripsi' => 'Sekoteng hangat isi kacang', 'harga' => 8000],
                ['nama' => 'Es Kopyor', 'deskripsi' => 'Es degan kopyor segar', 'harga' => 12000],
                ['nama' => 'Air Mineral', 'deskripsi' => 'Air mineral kemasan gelas', 'harga' => 3000],
            ],
            'gorengan' => [
                ['nama' => 'Pisang Goreng Crispy (3 pcs)', 'deskripsi' => 'Pisang goreng tepung crispy renyah', 'harga' => 8000],
                ['nama' => 'Pisang Goreng Coklat Keju', 'deskripsi' => 'Pisang goreng topping coklat dan keju', 'harga' => 12000],
                ['nama' => 'Pisang Goreng Madu', 'deskripsi' => 'Pisang goreng disiram madu', 'harga' => 10000],
                ['nama' => 'Pisang Goreng Susu', 'deskripsi' => 'Pisang goreng topping susu kental', 'harga' => 10000],
                ['nama' => 'Tahu Isi (3 pcs)', 'deskripsi' => 'Tahu isi sayuran goreng', 'harga' => 6000],
                ['nama' => 'Bakwan Sayur (3 pcs)', 'deskripsi' => 'Bakwan sayuran goreng renyah', 'harga' => 5000],
                ['nama' => 'Tempe Mendoan (3 pcs)', 'deskripsi' => 'Tempe mendoan tipis renyah', 'harga' => 6000],
                ['nama' => 'Risol Mayo (3 pcs)', 'deskripsi' => 'Risol isi mayo dan daging', 'harga' => 8000],
                ['nama' => 'Pastel (3 pcs)', 'deskripsi' => 'Pastel isi bihun sayuran', 'harga' => 8000],
                ['nama' => 'Lumpia Goreng (3 pcs)', 'deskripsi' => 'Lumpia goreng isi rebung', 'harga' => 8000],
                ['nama' => 'Onde-Onde (3 pcs)', 'deskripsi' => 'Onde-onde kacang hijau', 'harga' => 6000],
                ['nama' => 'Donat Kentang (2 pcs)', 'deskripsi' => 'Donat kentang empuk', 'harga' => 6000],
                ['nama' => 'Cireng (5 pcs)', 'deskripsi' => 'Cireng goreng bumbu rujak', 'harga' => 7000],
                ['nama' => 'Paket Gorengan Mix', 'deskripsi' => 'Campuran aneka gorengan', 'harga' => 10000],
                ['nama' => 'Es Teh Manis', 'deskripsi' => 'Teh manis dingin', 'harga' => 3000],
                ['nama' => 'Es Jeruk', 'deskripsi' => 'Jeruk peras segar', 'harga' => 5000],
                ['nama' => 'Air Mineral', 'deskripsi' => 'Air mineral kemasan gelas', 'harga' => 3000],
            ],
        ];
    }
}
