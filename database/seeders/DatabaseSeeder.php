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
       // Admin
        User::factory()->create([
            'name' => 'Admin Utama',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Petugas
        User::factory()->create([
            'name' => 'Petugas Lab',
            'email' => 'petugas@example.com',
            'password' => bcrypt('password'),
            'role' => 'petugas',
        ]);

        // Peminjam
        User::factory()->create([
            'name' => 'Siswa 1',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 'peminjam',
        ]);
    }
}
