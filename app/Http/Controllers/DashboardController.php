<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Space; // Kita pakai Space sebagai sumber data utama
use App\Models\KatalogTanaman;
use App\Models\Penjadwalan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\PenjadwalanDetail;
use App\Models\LogPerawatan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Display the user dashboard with statistics, recommendations, and task progress.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $userId = Auth::id();

        // 1. Ambil data dari model Space (pastikan di Model Space sudah ada 'id_user' atau 'user_id')
        // Sesuaikan 'id_user' dengan nama kolom di tabel spaces kamu
        $userLahan = Space::query()->where('id_user', $userId)->get();
        /** @var \Illuminate\Database\Eloquent\Collection $userLahan */

        // Statistik Dasar
        $totalLahan = $userLahan->count();
        $totalUser = User::query()->count();
        $totalTanaman = KatalogTanaman::query()->count();

        // Pastikan nama kolom di database adalah 'luas_lahan', jika di migrasi namanya 'luas_space', ganti di sini
        $totalLuas = $userLahan->sum('luas_lahan');

        // Bibit & Katalog
        $bibitDefault = KatalogTanaman::query()->latest()->take(6)->get();
        $semuaKatalog = KatalogTanaman::query()->get();
        $katalog = $semuaKatalog->keyBy('nama_tanaman');

        // Data untuk Grafik & Modal
        $labels = $userLahan->pluck('nama_lahan');
        $luasValues = $userLahan->pluck('luas_lahan');
        $suhuValues = $userLahan->pluck('suhu_lahan');

        // REKOMENDASI UNTUK GRAFIK (Algoritma Skor AI)
        $rekomendasi = $this->getRekomendasiLahan($userLahan, $semuaKatalog);
        $rekomendasiLahan = $rekomendasi['labels'];
        $rekomendasiDetails = $rekomendasi['details'];

        $jadwalUser = Penjadwalan::query()->where('user_id', $userId)->get();
        /** @var \Illuminate\Database\Eloquent\Collection $jadwalUser */
        
        // --- HITUNG DISTRIBUSI VARIAETAS TANAMAN UNTUK GRAFIK ---
        $plantGroups = $jadwalUser->countBy('nama_tanaman');
        $plantLabels = $plantGroups->keys();
        $plantCounts = $plantGroups->values();

        // Statistik Dasar
        $katalogRaw = $semuaKatalog->keyBy('nama_tanaman');
        $lahan = $userLahan->keyBy('nama_lahan');

        $jamSekarang = date('H');
        $saranWaktu = ($jamSekarang < 11) ? 'Pagi (07:00 - 09:00)' : 'Sore (16:00 - 18:00)';

        // --- PROSES TUGAS & PROGRES ---
        $taskStats = $this->calculateTaskStatistics($jadwalUser, $katalogRaw);
        $totalTugasSelesai = $taskStats['selesai'];
        $totalTugasSisa = $taskStats['sisa'];

        // --- FETCH LIVE WEATHER DATA ---
        $weatherKey = config('services.openweather.key');
        $weather = [
            'temp' => 28, 
            'desc' => 'Partly cloudy', 
            'humidity' => 68, 
            'wind' => 6,
            'icon' => '02d'
        ];

        try {
            $weatherResponse = Http::timeout(5)->get("https://api.openweathermap.org/data/2.5/weather?q=Malang&appid={$weatherKey}&units=metric&lang=id");
            if ($weatherResponse->successful()) {
                $wData = $weatherResponse->json();
                $weather = [
                    'temp' => round($wData['main']['temp']),
                    'desc' => ucfirst($wData['weather'][0]['description']),
                    'humidity' => $wData['main']['humidity'],
                    'wind' => round($wData['wind']['speed'] * 3.6), // m/s to km/h
                    'icon' => $wData['weather'][0]['icon']
                ];
            }
        } catch (\Exception $e) {
            Log::warning('Dashboard Weather Fetch Failed: ' . $e->getMessage());
        }

        return view('dashboard', compact(
            'weather',
            'jadwalUser',
            'katalog',
            'lahan',
            'saranWaktu',
            'totalLahan',
            'totalUser',
            'totalTanaman',
            'totalLuas',
            'bibitDefault',
            'semuaKatalog',
            'katalogRaw',
            'labels',
            'luasValues',
            'suhuValues',
            'plantLabels',
            'plantCounts',
            'totalTugasSelesai',
            'totalTugasSisa',
            'rekomendasiLahan',
            'rekomendasiDetails'
        ));
    }

    public function simpanTanam(Request $request)
    {
        $request->validate([
            'lahan' => 'required',
            'tanaman' => 'required',
            'tanggal' => 'required|date',
        ]);

        $penjadwalan = Penjadwalan::query()->create([
            'user_id' => Auth::id(),
            'nama_lahan' => $request->lahan,
            'nama_tanaman' => $request->tanaman,
            'tanggal_tanam' => $request->tanggal,
        ]);

        // --- GENERATE AI SCHEDULE ---
        try {
            $tanaman = KatalogTanaman::query()->where('nama_tanaman', $request->tanaman)->first();
            $weatherKey = env('OPENWEATHER_API_KEY');
            $groqKey = env('GROQ_API_KEY');

            // 1. Ambil Cuaca (Default Malang)
            $weatherUrl = "https://api.openweathermap.org/data/2.5/weather?q=Malang&appid={$weatherKey}&units=metric&lang=id";
            $weatherResponse = Http::get($weatherUrl);
            $weatherData = $weatherResponse->json();

            $temp = $weatherData['main']['temp'] ?? 27;
            $humidity = $weatherData['main']['humidity'] ?? 70;
            $desc = $weatherData['weather'][0]['description'] ?? 'cerah';

            // 2. Siapkan Prompt (Dengan Null-Safety)
            $suhuMin = $tanaman->suhu_min ?? 20;
            $suhuMax = $tanaman->suhu_max ?? 30;
            $cahayaJam = $tanaman->cahaya_jam ?? 12;
            $humidityAvg = $tanaman->humidity_avg ?? 70;

            $prompt = "Rancanglah jadwal perawatan harian otomatis untuk tanaman {$request->tanaman} mulai dari hari ke-1 hingga masa panen asli tanaman tersebut (jangan dipukul rata 90 hari, sesuaikan dengan siklus hidup aslinya). 
            Gunakan data spesifikasi dari katalog sebagai acuan utama (suhu ideal {$suhuMin}°C - {$suhuMax}°C, kebutuhan cahaya {$cahayaJam} jam/hari, dan kelembapan {$humidityAvg}%). 
            Sesuaikan instruksi kegiatan secara dinamis dengan membandingkannya terhadap kondisi cuaca real-time saat ini (suhu {$temp}°C, kelembapan {$humidity}%, dan kondisi {$desc}) agar rekomendasi penyiraman dan pemupukan menjadi akurat. 
            Hasilkan output hanya dalam format JSON array yang berisi kolom: hari_ke, kegiatan, deskripsi, dan kategori agar dapat langsung disimpan ke dalam database sistem.";

            // 3. Panggil Groq AI
            $url = "https://api.groq.com/openai/v1/chat/completions";
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $groqKey
            ])
                ->timeout(120)
                ->post($url, [
                "model" => "llama-3.3-70b-versatile",
                "messages" => [
                    ["role" => "user", "content" => $prompt]
                ],
                "temperature" => 0.5
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $aiText = $result['choices'][0]['message']['content'] ?? '';

                // Bersihkan Markdown JSON jika ada
                $jsonText = preg_replace('/```json\s*|```/', '', $aiText);
                $tasks = json_decode(trim($jsonText), true);

                if (is_array($tasks)) {
                    $maxHari = 0;
                    foreach ($tasks as $task) {
                        PenjadwalanDetail::query()->create([
                            'penjadwalan_id' => $penjadwalan->id,
                            'hari_ke' => $task['hari_ke'],
                            'kegiatan' => $task['kegiatan'],
                            'deskripsi' => $task['deskripsi'],
                            'kategori' => $task['kategori'],
                        ]);
                        if ($task['hari_ke'] > $maxHari) {
                            $maxHari = $task['hari_ke'];
                        }
                    }

                    // Update durasi_panen di tabel penjadwalans berdasarkan hari terakhir dari AI
                    if ($maxHari > 0) {
                        $penjadwalan->update(['durasi_panen' => $maxHari]);
                    }
                }
            }
        } catch (\Exception $e) {
            // Jika AI gagal, jadwal tetap terbuat tanpa detail (user bisa manual/coba lagi)
            Log::error('AI Schedule Generation Failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Jadwal berhasil dibuat!');
    }

    public function syncKatalogAi()
    {
        try {
            $tanamanList = KatalogTanaman::query()->pluck('nama_tanaman')->toArray();
            $tanamanStr = implode(', ', $tanamanList);
            $groqKey = env('GROQ_API_KEY');

            $prompt = "Berikan estimasi masa panen (dalam satuan hari) untuk daftar tanaman berikut: {$tanamanStr}. 
            Hasilkan output hanya dalam format JSON object dengan format: {\"Nama Tanaman\": durasi_hari}. 
            Pastikan durasi yang diberikan akurat berdasarkan siklus hidup asli tanaman tersebut (contoh: Padi sekitar 110-120 hari). 
            Berikan nilai integer tunggal untuk tiap tanaman.";

            $url = "https://api.groq.com/openai/v1/chat/completions";
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $groqKey
            ])
                ->timeout(120)
                ->post($url, [
                "model" => "llama-3.3-70b-versatile",
                "messages" => [
                    ["role" => "user", "content" => $prompt]
                ],
                "temperature" => 0.5
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $aiText = $result['choices'][0]['message']['content'] ?? '';
                $jsonText = preg_replace('/```json\s*|```/', '', $aiText);
                $harvestData = json_decode(trim($jsonText), true);

                if (is_array($harvestData)) {
                    foreach ($harvestData as $nama => $durasi) {
                        KatalogTanaman::query()->where('nama_tanaman', $nama)->update([
                            'estimasi_hari_panen' => $durasi
                        ]);
                    }
                    return response()->json(['success' => true, 'message' => 'Estimasi panen berhasil disinkronkan dengan AI!']);
                }
            }
            return response()->json(['success' => false, 'message' => 'AI gagal memberikan data yang valid.'], 500);
        } catch (\Exception $e) {
            Log::error('AI Catalog Sync Failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function semuaJadwal()
    {
        $semuaJadwal = Penjadwalan::query()->where('user_id', Auth::id())
            ->orderBy('tanggal_tanam', 'desc')
            ->get();

        return view('jadwal.index', compact('semuaJadwal'));
    }

    public function hapusJadwal($id)
    {
        $jadwal = Penjadwalan::query()->where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $jadwal->delete();
        return back()->with('success', 'Jadwal berhasil dihapus!');
    }

    public function completeTask($id)
    {
        $jadwal = Penjadwalan::query()->find($id);
        if ($jadwal) {
            $jadwal->increment('current_step');
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }
    /**
     * Get recommendations for each user space based on temperature.
     */
    private function getRekomendasiLahan($userLahan, $semuaKatalog): array
    {
        $labels = [];
        $details = [];
        foreach ($userLahan as $lhn) {
            $bestMatches = $semuaKatalog->filter(function($k) use ($lhn) {
                return $lhn->suhu_lahan >= $k->suhu_min && $lhn->suhu_lahan <= $k->suhu_max;
            })->map(function($t) use ($lhn) {
                $item = clone $t;
                $ideal = ($item->suhu_min + $item->suhu_max) / 2;
                $jarak = abs($lhn->suhu_lahan - $ideal);
                $radius = ($item->suhu_max - $item->suhu_min) / 2;
                $score = $radius > 0 ? (1 - ($jarak / $radius)) * 100 : 100;
                $item->skor_kecocokan = (int) round(max(50, $score)); 
                return $item;
            })->sortByDesc('skor_kecocokan')->values()->take(3);

            $labels[] = $bestMatches->first() ? $bestMatches->first()->nama_tanaman : 'Belum Ada yang Cocok';
            $details[] = $bestMatches; 
        }
        return ['labels' => $labels, 'details' => $details];
    }

    /**
     * Calculate task completion and remaining tasks for the user.
     */
    private function calculateTaskStatistics($jadwalUser, $katalogRaw): array
    {
        $totalTugasSelesai = 0;
        $totalTugasSisa = 0;

        foreach ($jadwalUser as $j) {
            /** @var \App\Models\Penjadwalan $j */
            if ($j->tanggal_tanam) {
                $dtTanam = Carbon::parse($j->tanggal_tanam)->startOfDay();
                $dtSekarang = Carbon::now()->startOfDay();
                $hariKe = $dtTanam->diffInDays($dtSekarang) + 1;

                $tugasHariIni = $j->details()->where('hari_ke', $hariKe)->get();
                
                // Hitung log selesai hari ini
                $logSelesaiCount = LogPerawatan::query()->where('penjadwalan_id', $j->id)
                    ->whereDate('tanggal_selesai', Carbon::today())
                    ->count();

                if ($tugasHariIni->isEmpty()) {
                    $totalTugasSisa += max(0, 3 - $logSelesaiCount);
                    $totalTugasSelesai += $logSelesaiCount;
                    
                    $tugasHariIni = collect([
                        (object) [
                            'kegiatan' => 'Penyiraman Rutin',
                            'deskripsi' => 'Lakukan penyiraman rutin sesuai kebutuhan tanaman.',
                            'kategori' => 'Penyiraman',
                            'hari_ke' => $hariKe
                        ],
                        (object) [
                            'kegiatan' => 'Pemantauan Kebun',
                            'deskripsi' => 'Cek kondisi daun dan kelembapan media tanam.',
                            'kategori' => 'Lainnya',
                            'hari_ke' => $hariKe
                        ]
                    ]);
                } else {
                    $totalTugasSelesai += $logSelesaiCount;
                    $totalTugasSisa += max(0, $tugasHariIni->count() - $logSelesaiCount);
                }

                // Map tasks with time and category
                $j->daftar_tugas_hari_ini = $tugasHariIni->map(function ($d, $idx) {
                    $time = '08:00 - 09:00';
                    $start_num = 800;
                    $end_num = 900;

                    if ($d->kategori == 'Penyiraman') {
                        $time = ($idx == 0) ? '07:00 - 09:00' : '16:00 - 18:00';
                        $start_num = ($idx == 0) ? 700 : 1600;
                        $end_num = ($idx == 0) ? 900 : 1800;
                    } elseif ($d->kategori == 'Pemupukan') {
                        $time = '09:00 - 10:00';
                        $start_num = 900;
                        $end_num = 1000;
                    }

                    return [
                        'time' => $time,
                        'name' => $d->kegiatan,
                        'desc' => $d->deskripsi,
                        'category' => $d->kategori,
                        'step' => $idx + 1,
                        'start_num' => $start_num,
                        'end_num' => $end_num
                    ];
                });

                $tanamanInfo = $katalogRaw->get($j->nama_tanaman);
                $estimasiKatalog = $tanamanInfo ? $tanamanInfo->estimasi_hari_panen : 90;
                
                $j->totalHariPanen = $j->durasi_panen ?: $estimasiKatalog;
                $j->hariKe = $hariKe;
                $j->progresPersen = min(round(($hariKe / $j->totalHariPanen) * 100), 100);
                
                $tugasCountReal = $j->daftar_tugas_hari_ini->count();
                $j->tugas_total_count = $tugasCountReal;
                $j->tugas_selesai_count = $logSelesaiCount;
                $j->tugas_persen = min(100, round(($logSelesaiCount / $tugasCountReal) * 100));
            } else {
                $tanamanInfo = $katalogRaw->get($j->nama_tanaman);
                $j->totalHariPanen = $tanamanInfo ? $tanamanInfo->estimasi_hari_panen : 90;
                $j->progresPersen = 0;
                $j->daftar_tugas_hari_ini = collect();
            }
        }

        return ['selesai' => $totalTugasSelesai, 'sisa' => $totalTugasSisa];
    }
}