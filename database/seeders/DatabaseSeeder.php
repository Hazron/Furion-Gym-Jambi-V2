<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. SEED USERS (ADMIN & OWNER)
        User::factory()->create([
            'name' => 'Monic',
            'email' => 'admin@furiongymjambi.com',
            'role' => 'admin',
            'password' => bcrypt('AdminMonicFurionJambi'),
        ]);

        User::factory()->create([
            'name' => 'Deki',
            'email' => 'owner@furiongymjambi.com',
            'role' => 'owner',
            'password' => bcrypt('DekikaryaNurdiMobilindo'),
        ]);

        $paketData = [
            // ==========================================
            // MEMBERSHIP SINGLE (Jenis: Reguler)
            // ==========================================
            [
                'nama_paket' => 'Membership Single 1 Bulan',
                'durasi'     => '1 Bulan',
                'harga'      => 250000,
                'status'     => 'aktif',
                'jenis'      => 'reguler',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'nama_paket' => 'Membership Single 3 Bulan',
                'durasi'     => '3 Bulan',
                'harga'      => 600000,
                'status'     => 'aktif',
                'jenis'      => 'reguler',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'nama_paket' => 'Membership Single 6 Bulan',
                'durasi'     => '6 Bulan',
                'harga'      => 1050000,
                'status'     => 'aktif',
                'jenis'      => 'reguler',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'nama_paket' => 'Membership Single 1 Tahun',
                'durasi'     => '1 Tahun',
                'harga'      => 1900000,
                'status'     => 'aktif',
                'jenis'      => 'reguler',
                'created_at' => now(), 'updated_at' => now(),
            ],

            // ==========================================
            // MEMBERSHIP COUPLE (Jenis: Couple)
            // ==========================================
            [
                'nama_paket' => 'Membership Couple 1 Bulan',
                'durasi'     => '1 Bulan',
                'harga'      => 400000, // Harga paket untuk berdua
                'status'     => 'aktif',
                'jenis'      => 'couple',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'nama_paket' => 'Membership Couple 3 Bulan',
                'durasi'     => '3 Bulan',
                'harga'      => 1000000, // Harga paket untuk berdua
                'status'     => 'aktif',
                'jenis'      => 'couple',
                'created_at' => now(), 'updated_at' => now(),
            ],
        ];

        DB::table('paket_members')->insert($paketData);
    }
}