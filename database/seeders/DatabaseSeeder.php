<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Space;
use App\Models\KatalogTanaman;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'budi123@gmail.com'],
            [
                'nama' => 'budi',
                'password' => Hash::make('password123'),
            ]
        );
        Space::create([
            'id_user' => $user->id_user,
            'nama_lahan' => 'Kebun Belakang',
            'luas_lahan' => 50,
            'suhu_lahan' => 28,
            'cahaya_lahan' => 8,
            'lokasi_lahan' => 'Belakang',
        ]);

        // 2. Proses Import CSV
        $filePath = base_path('Crop_recommendation.csv'); // Letakkan file CSV di folder root Laravel
        $file = fopen($filePath, 'r');
        $header = fgetcsv($file); // Skip header

        $tempData = [];

        while (($row = fgetcsv($file)) !== FALSE) {
            $label = $row[7]; // Kolom 'label'
            $temp = (float)$row[3]; // Kolom 'temperature'

            if (!isset($tempData[$label])) {
                $tempData[$label] = ['min' => $temp, 'max' => $temp];
            } else {
                $tempData[$label]['min'] = min($tempData[$label]['min'], $temp);
                $tempData[$label]['max'] = max($tempData[$label]['max'], $temp);
            }
        }
        fclose($file);

        // 3. Simpan ke Database
        foreach ($tempData as $nama => $range) {
            KatalogTanaman::create([
                'nama_tanaman' => ucfirst($nama),
                'suhu_min' => round($range['min'], 1),
                'suhu_max' => round($range['max'], 1),
                'cahaya_jam' => rand(6, 10), // Asumsi kebutuhan cahaya matahari
                'deskripsi_edukasi' => "Berdasarkan dataset Crop Recommendation, tanaman ini tumbuh optimal pada rentang suhu " . round($range['min'], 1) . "°C hingga " . round($range['max'], 1) . "°C.",
            ]);
        }
    }
}