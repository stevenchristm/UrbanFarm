<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Setting;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat user admin
        $existing = DB::table('users')->where('email', 'admin@urban.com')->first();
        if (!$existing) {
            DB::table('users')->insert([
                'nama'       => 'Admin',
                'name'       => 'Admin',
                'email'      => 'admin@urban.com',
                'password'   => Hash::make('admin123'),
                'role'       => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Default settings teks untuk setiap halaman
        $defaults = [
            // Dashboard
            'dashboard_subtitle' => 'Pusat kendali ekosistem urban Anda aktif.',

            // Area Lahan
            'lahan_title'       => 'Area Lahan Strategis',
            'lahan_subtitle'    => 'Pusat kendali pintar. Kelola dan pantau seluruh parameter lingkungan agrikultur Anda secara presisi tinggi.',

            // Alur Kerja
            'jadwal_title'      => 'Pusat Kendali Agronomi',
            'jadwal_subtitle'   => 'Algoritma UrbanFarm Master Agronomist telah mensinkronisasi profil pertumbuhan Anda dengan cuaca mikro saat ini.',

            // Edukasi Bibit
            'katalog_title'     => 'Eksplorasi Biodiversitas',
            'katalog_subtitle'  => 'Pelajari karakteristik dan siklus spesifik tanaman pangan untuk mengoptimalkan hasil panen Anda bersama asisten AI.',

            // Komunitas
            'komunitas_title'   => 'Komunitas Petani Digital',
            'komunitas_subtitle'=> 'Terhubung, belajar, dan berkolaborasi dengan jaringan agronomis modern yang menggunakan ekosistem UrbanFarm.',

            // Asisten AI
            'ai_title'          => 'Pakar Botani AI',
            'ai_subtitle'       => 'Konsultasi cerdas mengenai kesehatan tanaman, cuaca, dan strategi budidaya.',

            // Sidebar Pro Tips
            'sidebar_protips'   => 'Tanyakan AI Agronomist untuk jadwal pemupukan yang paling optimal.',
        ];

        foreach ($defaults as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
