<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Penjadwalan;
use App\Models\LogPerawatan;
use App\Events\TaskNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class SendTaskNotifications extends Command
{
    protected $signature = 'app:send-task-notifications';
    protected $description = 'Send realtime task notifications based on schedule';

    public function handle()
    {
        $semuaJadwal = Penjadwalan::with('details')->get();
        $now = Carbon::now();
        $currentTime = (int)$now->format('Hi');
        $todayStr = $now->format('Y-m-d');

        foreach ($semuaJadwal as $j) {
            if ($j->tanggal_tanam) {
                $dtTanam = Carbon::parse($j->tanggal_tanam)->startOfDay();
                $dtSekarang = Carbon::today();
                $j->hariKe = (int) $dtTanam->diffInDays($dtSekarang) + 1;
            } else {
                $j->hariKe = 1;
            }

            $details = $j->details()->where('hari_ke', $j->hariKe)->get();
            $daftarTugas = [];

            if ($details->isNotEmpty()) {
                foreach ($details as $idx => $d) {
                    $start_num = 0;
                    $end_num = 2359;
                    if ($d->kategori == 'Penyiraman') {
                        $start_num = ($idx == 0) ? 700 : 1600;
                        $end_num = ($idx == 0) ? 900 : 1800;
                    }
                    $daftarTugas[] = [
                        'step' => $idx + 1,
                        'name' => $d->kegiatan,
                        'kategori' => $d->kategori,
                        'start_num' => $start_num,
                        'end_num' => $end_num
                    ];
                }
            } else {
                $daftarTugas = [
                    ['step' => 1, 'name' => "Siram Pagi & Nutrisi", 'kategori' => 'Penyiraman', 'start_num' => 700, 'end_num' => 900],
                    ['step' => 2, 'name' => "Cek Kelembaban Media", 'kategori' => 'Lainnya', 'start_num' => 1300, 'end_num' => 1500],
                    ['step' => 3, 'name' => "Siram Sore & Cek Hama", 'kategori' => 'Penyiraman', 'start_num' => 1700, 'end_num' => 1900],
                ];
            }

            $logSelesai = LogPerawatan::where('penjadwalan_id', $j->id)
                ->whereDate('tanggal_selesai', Carbon::today())
                ->pluck('step')
                ->toArray();

            foreach ($daftarTugas as $tugas) {
                $isDone = in_array($tugas['step'], $logSelesai);
                if ($isDone) continue;

                // --- NOTIFIKASI 1: Tugas dimulai (muncul saat time window aktif) ---
                if ($currentTime >= $tugas['start_num'] && $currentTime <= $tugas['end_num']) {
                    $cacheKey = "notif_start_{$j->id}_{$tugas['step']}_{$todayStr}";
                    if (!Cache::has($cacheKey)) {
                        $message = "Waktunya melakukan \"{$tugas['name']}\" pada tanaman {$j->nama_tanaman}. Jangan sampai terlewat!";
                        broadcast(new TaskNotification($j->user_id, '🌱 Tugas Dimulai!', $message, '/semua-jadwal'));
                        Cache::put($cacheKey, true, Carbon::now()->endOfDay());
                        $this->info("START notif → User {$j->user_id}: {$tugas['name']} ({$j->nama_tanaman})");
                    }
                }

                // --- NOTIFIKASI 2: Tugas terlewat/overdue (muncul 30 menit setelah end time) ---
                $overdueThreshold = $tugas['end_num'] > 0 ? $tugas['end_num'] + 30 : -1;
                // Adjust +30 overflow (e.g. 1830 → 1900+30 would be fine, but 2359+30 would overflow day)
                if ($tugas['end_num'] > 0 && $overdueThreshold <= 2359 && $currentTime >= $overdueThreshold) {
                    $cacheKey = "notif_overdue_{$j->id}_{$tugas['step']}_{$todayStr}";
                    if (!Cache::has($cacheKey)) {
                        $message = "Tugas \"{$tugas['name']}\" pada tanaman {$j->nama_tanaman} sudah terlewat! Segera lakukan sebelum terlambat.";
                        broadcast(new TaskNotification($j->user_id, '⚠️ Tugas Terlewat!', $message, '/semua-jadwal'));
                        Cache::put($cacheKey, true, Carbon::now()->endOfDay());
                        $this->info("OVERDUE notif → User {$j->user_id}: {$tugas['name']} ({$j->nama_tanaman})");
                    }
                }
            }
        }
    }
}
