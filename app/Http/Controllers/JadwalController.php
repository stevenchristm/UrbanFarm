<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjadwalan;
use App\Models\LogPerawatan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class JadwalController extends Controller
{
    public function index(): View
    {
        // 1. Ambil jadwal hanya milik user yang login
        $semuaJadwal = Penjadwalan::with('details')
            ->where('user_id', Auth::id()) 
            ->get(); 

        $katalogRaw = \App\Models\KatalogTanaman::all()->keyBy('nama_tanaman');

        $now = Carbon::now();
        $currentTime = (int)$now->format('Hi'); 

        foreach ($semuaJadwal as $j) {
            /** @var \App\Models\Penjadwalan $j */
            // 2. Hitung hari bulat
            if ($j->tanggal_tanam) {
                $dtTanam = Carbon::parse($j->tanggal_tanam)->startOfDay();
                $dtSekarang = Carbon::today();
                $j->hariKe = (int) $dtTanam->diffInDays($dtSekarang) + 1;
                
                // Ambil durasi panen asli dari AI (atau default 90 jika data lama)
                $j->totalHariPanen = $j->durasi_panen ?: 90;
                $j->progresPersen = min(round(($j->hariKe / $j->totalHariPanen) * 100), 100);
            } else {
                $j->hariKe = 1;
                $j->totalHariPanen = 90;
                $j->progresPersen = 0;
            }
            
            // 3. Ambil Tugas dari DB atau Fallback Hardcoded
            $details = $j->details()->where('hari_ke', $j->hariKe)->get();
            $daftarTugas = [];

            if ($details->isNotEmpty()) {
                foreach ($details as $idx => $d) {
                    // Gunakan kategori untuk waktu estimasi sederhana
                    $time = "Fleksibel";
                    $start_num = 0;
                    $end_num = 2359;

                    if ($d->kategori == 'Penyiraman') {
                        $time = ($idx == 0) ? '07:00 - 09:00' : '16:00 - 18:00';
                        $start_num = ($idx == 0) ? 700 : 1600;
                        $end_num = ($idx == 0) ? 900 : 1800;
                    }

                    $daftarTugas[] = [
                        'step' => $idx + 1,
                        'name' => $d->kegiatan,
                        'description' => $d->deskripsi,
                        'fase' => $d->fase,
                        'alat_bahan' => $d->alat_bahan,
                        'category' => $d->kategori,
                        'time' => $time,
                        'start_num' => $start_num,
                        'end_num' => $end_num
                    ];
                }
            } else {
                // Fallback untuk data lama
                $daftarTugas = [
                    ['step' => 1, 'name' => "Siram Pagi & Nutrisi", 'time' => '07:00 - 09:00', 'start_num' => 700, 'end_num' => 900, 'category' => 'Penyiraman'],
                    ['step' => 2, 'name' => "Cek Kelembaban Media", 'time' => '13:00 - 15:00', 'start_num' => 1300, 'end_num' => 1500, 'category' => 'Lainnya'],
                    ['step' => 3, 'name' => "Siram Sore & Cek Hama", 'time' => '17:00 - 19:00', 'start_num' => 1700, 'end_num' => 1900, 'category' => 'Penyiraman'],
                ];
            }

            // 4. Ambil log selesai hari ini
            $logSelesai = LogPerawatan::where('penjadwalan_id', $j->id)
                                        ->whereDate('tanggal_selesai', Carbon::today())
                                        ->pluck('step')
                                        ->toArray();

            // 5. Proses status tugas
            foreach ($daftarTugas as $key => $tugas) {
                $isDone = in_array($tugas['step'], $logSelesai);
                $isOverdue = ($currentTime > $tugas['end_num']) && !$isDone && ($tugas['end_num'] > 0);

                $daftarTugas[$key]['is_done'] = $isDone;
                $daftarTugas[$key]['is_overdue'] = $isOverdue;
                $daftarTugas[$key]['is_future'] = $currentTime < $tugas['start_num'];
            }

            $j->daftar_tugas_hari_ini = $daftarTugas;

            // 6. Hitung missedTasksCount
            $totalTasksShouldBeDone = 0;
            if ($j->details()->exists()) {
                $totalTasksShouldBeDone = $j->details()->where('hari_ke', '<', $j->hariKe)->count();
            } else {
                $totalTasksShouldBeDone = ($j->hariKe - 1) * 3;
            }
            
            // Tambahkan tugas hari ini yang sudah overdue
            foreach ($daftarTugas as $tugas) {
                if ($currentTime > $tugas['end_num'] && $tugas['end_num'] > 0) {
                    $totalTasksShouldBeDone++;
                }
            }

            $totalCompletedTasks = LogPerawatan::where('penjadwalan_id', $j->id)->count();
            $j->missedTasksCount = max(0, $totalTasksShouldBeDone - $totalCompletedTasks);
        }

        return view('jadwal.index', compact('semuaJadwal'));
    }

    public function completeTask(Request $request, $id): JsonResponse
    {
        $jadwal = Penjadwalan::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$jadwal) return response()->json(['message' => 'Not Found'], 404);

        LogPerawatan::create([
            'penjadwalan_id' => $id,
            'step' => $request->step,
            'tanggal_selesai' => Carbon::now()
        ]);
        
        $jadwal->increment('current_step');
        return response()->json(['success' => true]);
    }

    public function destroy($id): RedirectResponse
    {
        $jadwal = Penjadwalan::where('id', $id)->where('user_id', Auth::id())->first();
        if ($jadwal) {
            $jadwal->delete();
            return redirect()->back()->with('success', 'Dihapus');
        }
        return redirect()->back();
    }

    public function getAttentionAnalysis(Request $request, $id): JsonResponse
    {
        $jadwal = Penjadwalan::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$jadwal) return response()->json(['message' => 'Not Found'], 404);

        $hariKe = $request->query('hari_ke', 1);
        $missedCount = $request->query('missed_count', 1);
        $tanaman = $jadwal->nama_tanaman;

        if ($missedCount > 5) {
            $prompt = "Dalam 2 kalimat singkat dan tegas, nyatakan bahwa tanaman {$tanaman} (Hari ke-{$hariKe}) kemungkinan besar sudah mati atau sekarat akibat {$missedCount} tugas perawatan yang terlewat. Sebutkan gejala fisik yang paling parah seperti daun layu total, batang membusuk, dll. Jangan gunakan pembukaan seperti 'Sebagai AI' atau 'Berdasarkan'."; 
        } else {
            $prompt = "Dalam 2-3 kalimat singkat dan langsung, jelaskan dampak fisik yang sedang terjadi pada tanaman {$tanaman} (Hari ke-{$hariKe}) akibat {$missedCount} tugas perawatan yang terlewat. Sebutkan gejala spesifik seperti daun menguning, pertumbuhan terhambat, atau rentan hama sesuai jenis kelalaiannya. Jangan gunakan pembukaan seperti 'Sebagai AI' atau 'Berdasarkan'."; 
        }

        $groqKey = env('GROQ_API_KEY');
        if (!$groqKey) {
            return response()->json(['message' => 'API Key tidak dikonfigurasi.'], 500);
        }

        $url = "https://api.groq.com/openai/v1/chat/completions";
        
        $payload = [
            "model" => "llama-3.1-8b-instant",
            "messages" => [
                ["role" => "user", "content" => $prompt]
            ],
            "temperature" => 0.5,
            "max_tokens" => 2048
        ];

        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $groqKey
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($response, true);
            if (isset($result['choices'][0]['message']['content'])) {
                $text = $result['choices'][0]['message']['content'];
                return response()->json(['analysis' => $text]);
            }

            return response()->json(['analysis' => 'AI sedang tidak dapat menganalisis tanaman saat ini.'], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan sistem.'], 500);
        }
    }
}