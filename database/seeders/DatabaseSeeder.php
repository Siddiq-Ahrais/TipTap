<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Superadmin user
        User::firstOrCreate(
            ['email' => 'superadmin@tiptap.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'role' => 'superadmin',
                'divisi' => 'Management',
                'status_pekerjaan' => 'aktif',
                'is_approved' => true,
            ]
        );

        // Create default settings
        Setting::firstOrCreate(
            ['id' => 1],
            [
                'jam_masuk_kantor' => '08:00:00',
                'jam_mulai_pulang' => '17:00:00',
            ]
        );

        // Create test users
        User::factory(5)->create();
    }
}
