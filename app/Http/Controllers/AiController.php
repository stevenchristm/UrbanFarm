<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Penjadwalan;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AiController extends Controller
{
    public function index(): View
    {
        // Ambil riwayat chat user yang sedang login
        $history = Chat::where('user_id', Auth::id())->latest()->take(10)->get()->reverse();
        return view('ai.chat', compact('history'));
    }

    /**
     * Handle chat messages and image analysis with Gemini AI.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function chat(Request $request): JsonResponse
    {
        // Validasi: Pesan atau Gambar harus ada
        $request->validate([
            'message' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
        ]);

        $weatherContext = $this->getWeatherContext($request);

        $scheduleContext = $this->getScheduleContext();

        // 4. Masukkan $weatherContext & $scheduleContext ke dalam System Prompt Gemini
        $systemPrompt = "Anda adalah Pakar Pertanian UrbanFarm yang cerdas.
DATA CUACA REAL-TIME: {$weatherContext}
DATA PENJADWALAN USER: {$scheduleContext}

Tugas Anda:
1. Membantu petani dengan masalah tanaman, hama, dan nutrisi.
2. Memberikan saran pencegahan berdasarkan DATA CUACA di atas (Predictive Analytics). 
   - Jika user bertanya tentang cuaca, hubungkan dengan dampaknya ke tanaman.
   - Gunakan data lokasi yang tertera di konteks cuaca untuk memberikan jawaban yang spesifik.
3. Jika ada foto daun, lakukan analisa penyakit secara visual.

Jawablah dengan gaya bahasa yang ramah dan solutif.";

        $userMessage = $request->input('message') ?? '';
        $groqApiKey = config('services.groq.key');
        $geminiApiKey = env('GEMINI_API_KEY');
        $imagePath = null;

        $userPrompt = "Pertanyaan user: " . ($userMessage ?: "(User mengirim gambar untuk dianalisa)");

        if ($request->hasFile('image')) {
            // === HYBRID: MENGGUNAKAN GEMINI UNTUK GAMBAR (VISION) ===
            $file = $request->file('image');
            $imagePath = $file->store('chats', 'public');

            // Konversi ke Base64
            $imageData = base64_encode(file_get_contents($file->path()));
            $parts = [];
            $parts[] = [
                "inline_data" => [
                    "mime_type" => $file->getMimeType(),
                    "data" => $imageData
                ]
            ];
            $parts[] = ["text" => $systemPrompt . "\n\n" . $userPrompt];

            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $geminiApiKey;

            try {
                $response = Http::withoutVerifying()
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->timeout(60)
                    ->retry(3, 100)
                    ->post($url, [
                        "contents" => [
                            ["parts" => $parts]
                        ]
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $aiReply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, respons kosong.';
                } else {
                    return response()->json(['response' => "Error dari Google Gemini: " . $response->body()], 500);
                }
            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
                if (str_contains(strtolower($errorMessage), 'timed out') || str_contains(strtolower($errorMessage), 'timeout')) {
                    $errorMessage = "Koneksi ke Google Gemini API terputus (Timeout). Silakan ulangi.";
                }
                return response()->json(['response' => "Error Sistem: " . $errorMessage], 500);
            }

        } else {
            // === HYBRID: MENGGUNAKAN GROQ UNTUK TEKS SAJA ===
            $messages = [
                ["role" => "system", "content" => $systemPrompt],
                ["role" => "user", "content" => $userPrompt]
            ];

            $url = "https://api.groq.com/openai/v1/chat/completions";

            try {
                $response = Http::withoutVerifying()
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $groqApiKey
                    ])
                    ->timeout(60)
                    ->retry(3, 100)
                    ->post($url, [
                        "model" => "llama-3.3-70b-versatile",
                        "messages" => $messages,
                        "temperature" => 0.6
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $aiReply = $data['choices'][0]['message']['content'] ?? 'Maaf, respons kosong.';
                } else {
                    return response()->json(['response' => "Error dari Groq AI: " . $response->body()], 500);
                }
            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
                if (str_contains(strtolower($errorMessage), 'timed out') || str_contains(strtolower($errorMessage), 'timeout')) {
                    $errorMessage = "Koneksi ke Groq API terputus (Timeout). Silakan ulangi.";
                }
                return response()->json(['response' => "Error Sistem: " . $errorMessage], 500);
            }
        }

        // Simpan ke database
        Chat::create([
            'user_id' => Auth::id(),
            'message' => $userMessage,
            'image' => $imagePath,
            'response' => $aiReply
        ]);

        return response()->json(['response' => $aiReply]);
    }

    /**
     * Get weather context for AI prompt.
     */
    private function getWeatherContext(Request $request): string
    {
        $weatherKey = config('services.openweather.key');
        $lat = $request->input('lat');
        $lon = $request->input('lon');
        $city = "Malang";

        if ($lat && $lon) {
            $weatherUrl = "https://api.openweathermap.org/data/2.5/forecast?lat={$lat}&lon={$lon}&appid={$weatherKey}&units=metric&lang=id";
        } else {
            $weatherUrl = "https://api.openweathermap.org/data/2.5/forecast?q={$city}&appid={$weatherKey}&units=metric&lang=id";
        }

        try {
            $responseWeather = Http::get($weatherUrl);
            if ($responseWeather->successful()) {
                $data = $responseWeather->json();
                $detectedLocation = $data['city']['name'] ?? $city;
                $current = $data['list'][0];
                $temp = $current['main']['temp'];
                $desc = $current['weather'][0]['description'];
                
                $forecastRain = false;
                for ($i = 0; $i < 3; $i++) {
                    if (isset($data['list'][$i]) && str_contains(strtolower($data['list'][$i]['weather'][0]['main']), 'rain')) {
                        $forecastRain = true;
                        break;
                    }
                }

                $rainNotice = $forecastRain ? "WARNING: Ada prediksi hujan dalam beberapa jam kedepan." : "Tidak ada prediksi hujan dalam waktu dekat.";
                return "Cuaca di {$detectedLocation}: {$temp}°C, {$desc}. {$rainNotice}";
            }
        } catch (\Exception $e) {
            // Fallback
        }
        return "Data cuaca tidak tersedia.";
    }

    /**
     * Get plant schedule context for AI prompt.
     */
    private function getScheduleContext(): string
    {
        $schedules = Penjadwalan::with('details')
            ->where('user_id', Auth::id())
            ->get();
        
        if ($schedules->isEmpty()) {
            return "User belum memiliki jadwal tanam aktif.";
        }

        $context = "Jadwal Tanam User Saat Ini:\n";
        foreach ($schedules as $j) {
            /** @var \App\Models\Penjadwalan $j */
            $dtTanam = Carbon::parse($j->tanggal_tanam)->startOfDay();
            $hariKe = (int) $dtTanam->diffInDays(Carbon::today()) + 1;
            $currentTasks = $j->details()->where('hari_ke', $hariKe)->pluck('kegiatan')->implode(', ');
            $context .= "- Tanaman: {$j->nama_tanaman}, Hari ke: {$hariKe}. Tugas hari ini: " . ($currentTasks ?: "Tidak ada tugas khusus") . ".\n";
        }
        return $context;
    }

    public function clear(): JsonResponse
    {
        Chat::where('user_id', Auth::id())->delete();
        return response()->json(['message' => 'Riwayat chat berhasil dihapus.']);
    }
}