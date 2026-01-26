<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Sample Services
        $s1 = \App\Models\Service::create([
            'name' => 'Layanan Pelanggan (CS)',
            'prefix' => 'A',
            'color_class' => 'primary',
            'hex_color' => '#0d6efd',
            'description' => 'Layanan bantuan umum'
        ]);

        $s2 = \App\Models\Service::create([
            'name' => 'Pembayaran (Kasir)',
            'prefix' => 'B',
            'color_class' => 'success',
            'hex_color' => '#198754',
            'description' => 'Layanan pembayaran tagihan'
        ]);

        // Sample Counters
        \App\Models\Counter::create([
            'service_id' => $s1->id,
            'name' => 'Loket 1',
            'status' => 'active'
        ]);

        \App\Models\Counter::create([
            'service_id' => $s1->id,
            'name' => 'Loket 2',
            'status' => 'active'
        ]);

        \App\Models\Counter::create([
            'service_id' => $s2->id,
            'name' => 'Loket 3',
            'status' => 'active'
        ]);

        // Admin User
        User::create([
            'name' => 'Admin Antrian',
            'email' => 'admin@antrian.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        // Staff User
        User::create([
            'name' => 'Staf Loket 1',
            'email' => 'staff@antrian.com',
            'password' => bcrypt('password'),
            'role' => 'staff'
        ]);
    }
}
