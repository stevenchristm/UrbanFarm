<?php

namespace App\Http\Controllers;

use App\Models\KatalogTanaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class KatalogController extends Controller
{
    public function index() {
        $tanaman = KatalogTanaman::all(); 
        return view('katalog.index', compact('tanaman'));
    }

    public function create() {
        return view('katalog.create');
    }

    public function store(Request $request) {
        $data = $request->all();

        // Logika Upload Gambar
        if ($request->hasFile('gambar_tanaman')) {
            $file = $request->file('gambar_tanaman');
            $nama_file = time() . "_" . $file->getClientOriginalName();
            $file->move(public_path('assets/img/bibit'), $nama_file);
            $data['foto_tanaman'] = $nama_file;
        }

        KatalogTanaman::create($data);
        return redirect()->route('katalog.index')->with('success', 'Bibit edukasi baru berhasil ditambah!');
    }

    // Ubah $id menjadi $katalog agar sinkron dengan Resource Route
    public function edit($katalog)
    {
        // Gunakan $katalog sebagai ID untuk mencari data
        $tanaman = KatalogTanaman::findOrFail($katalog);
        return view('katalog.edit', compact('tanaman'));
    }

    public function update(Request $request, $katalog)
    {
        $tanaman = KatalogTanaman::findOrFail($katalog);
        $data = $request->all();

        // Logika Update Gambar (Hapus gambar lama jika ada upload baru)
        if ($request->hasFile('gambar_tanaman')) {
            // Hapus file lama
            if ($tanaman->foto_tanaman && File::exists(public_path('assets/img/bibit/' . $tanaman->foto_tanaman))) {
                File::delete(public_path('assets/img/bibit/' . $tanaman->foto_tanaman));
            }

            $file = $request->file('gambar_tanaman');
            $nama_file = time() . "_" . $file->getClientOriginalName();
            $file->move(public_path('assets/img/bibit'), $nama_file);
            $data['foto_tanaman'] = $nama_file;
        }

        $tanaman->update($data);

        return redirect()->route('katalog.index')->with('success', 'Data berhasil diubah');
    }

    public function getAiLifecycle($id)
    {
        $tanaman = KatalogTanaman::findOrFail($id);
        $cacheKey = 'ai_lifecycle_tanaman_' . $id;

        try {
            $data = \Illuminate\Support\Facades\Cache::remember($cacheKey, 60*24*30, function() use ($tanaman) {
                $groqKey = config('services.groq.key');
                $prompt = "Buatkan siklus hidup (lifecycle) yang akurat dan realistis untuk tanaman {$tanaman->nama_tanaman}. 
                Format WAJIB JSON persis seperti ini:
                {
                    \"plant\": \"{$tanaman->nama_tanaman}\",
                    \"total_days\": [total durasi panen dalam hari],
                    \"stages\": [
                        {\"phase\": \"Perkecambahan\", \"days\": [durasi fase ini], \"action\": \"[tips/aksi perawatan spesifik untuk fase ini]\"},
                        {\"phase\": \"Vegetatif\", \"days\": [durasi fase ini], \"action\": \"[tips/aksi perawatan spesifik untuk fase ini]\"},
                        {\"phase\": \"Pembungaan/Pembuahan\", \"days\": [durasi fase ini], \"action\": \"[tips/aksi perawatan spesifik untuk fase ini]\"},
                        {\"phase\": \"Pemanenan\", \"days\": [durasi fase ini], \"action\": \"[tips/aksi perawatan spesifik untuk fase ini]\"}
                    ]
                }
                Hanya kembalikan JSON object murni tanpa markdown blocks.";

                $url = "https://api.groq.com/openai/v1/chat/completions";

                $response = Http::withoutVerifying()
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $groqKey
                    ])
                    ->timeout(60)
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
                    $decoded = json_decode(trim($jsonText), true);
                    
                    if ($decoded) {
                        return $decoded;
                    }
                    throw new \Exception('Response JSON invalid: ' . $jsonText);
                }
                
                throw new \Exception('API Error: ' . $response->body());
            });

            return response()->json($data);

        } catch (\Exception $e) {
            // Hapus cache jika terjadi error tapi sudah terlanjur tersimpan null
            \Illuminate\Support\Facades\Cache::forget($cacheKey);
            return response()->json(['error' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }
}