@extends('layouts.app')

@section('title', 'Rekomendasi AI')

@section('styles')
<style>
    .tanaman-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 2rem;
    }

    .recommendation-card {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08), 0 4px 10px rgba(0, 0, 0, 0.03);
        padding: 0;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .recommendation-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12), 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .match-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        z-index: 10;
        padding: 6px 12px;
        border-radius: 50px;
        font-weight: 800;
        font-size: 0.75rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .env-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-glass);
        margin-top: 1.5rem;
    }

    .stat-label {
        font-size: 0.7rem;
        color: var(--text-muted);
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .stat-val {
        font-size: 1rem;
        font-weight: 700;
        color: var(--primary-emerald);
    }

    .video-modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.9);
        backdrop-filter: blur(15px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
</style>
@endsection

@section('content')
<div class="page-header animate-up">
    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1>🧬 Analitik Kecocokan AI</h1>
            <p>Parameter sistem telah dipetakan terhadap basis data botani UrbanFarm.</p>
        </div>
        <div style="padding: 12px 24px; border-radius: 16px; border: 1px solid #cbd5e1; background: #ffffff; box-shadow: 0 4px 10px rgba(0,0,0,0.03);">
            <span style="font-size: 0.75rem; color: #64748b; display: block; font-weight: 600;">Lahan Aktif:</span>
            <span style="font-weight: 800; color: #10b981; font-size: 1.1rem;">{{ $lahan->nama_lahan }}</span>
            <span style="margin-left: 10px; color: #475569; font-weight: 600; font-size: 0.9rem;">🌡️ {{ $lahan->suhu_lahan }}°C / ☀️ {{ $lahan->cahaya_lahan }}h</span>
        </div>
    </div>
</div>

<div class="tanaman-grid animate-up" style="animation-delay: 0.1s;">
    @forelse($tanamanCocok as $t)
    <div class="recommendation-card">
        <div style="position: relative; height: 220px; background: #f8fafc; overflow: hidden; border-bottom: 1px solid #cbd5e1;">
            @php
                $matchColor = '#ef4444'; // Merah untuk 0-30%
                if($t->skor_kecocokan >= 76) {
                    $matchColor = '#10b981'; // Hijau untuk 76-100%
                } elseif($t->skor_kecocokan >= 31) {
                    $matchColor = '#f59e0b'; // Kuning untuk 31-75%
                }
            @endphp
            <div class="match-badge" style="background: {{ $matchColor }}; color: white; border: 2px solid #ffffff;">
                MATCH {{ $t->skor_kecocokan }}%
            </div>
            @if($t->foto_tanaman)
                <img src="{{ asset('assets/img/bibit/' . $t->foto_tanaman) }}" alt="{{ $t->nama_tanaman }}" style="width: 100%; height: 100%; object-fit: cover;">
            @else
                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 4rem;">🌱</div>
            @endif
        </div>

        <div style="padding: 1.5rem; flex-grow: 1; display: flex; flex-direction: column;">
            <h3 style="margin-bottom: 10px; color: #1e293b; font-weight: 800; font-size: 1.25rem;">{{ $t->nama_tanaman }}</h3>
            <p style="font-size: 0.9rem; color: #64748b; line-height: 1.6; flex-grow: 1;">
                {{ $t->deskripsi_edukasi ?? 'Tanaman ini sangat sesuai untuk optimasi ketahanan pangan di lahan Anda.' }}
            </p>

            <div class="env-stats">
                <div>
                    <div class="stat-label">Suhu Ideal</div>
                    <div class="stat-val">{{ $t->suhu_min }}-{{ $t->suhu_max }}°C</div>
                </div>
                <div>
                    <div class="stat-label">Kebutuhan Cahaya</div>
                    <div class="stat-val">{{ $t->cahaya_jam }} Jam</div>
                </div>
            </div>

            @if($t->video_id)
                <button onclick="openVideo('{{ $t->video_id }}')" class="w-full mt-6 flex justify-center items-center gap-2 px-6 py-3 bg-emerald-500 hover:bg-emerald-400 text-white font-semibold rounded-xl shadow-[0_4px_15px_rgba(16,185,129,0.3)] hover:shadow-[0_4px_25px_rgba(16,185,129,0.5)] transition-all duration-300">
                    <i data-lucide="youtube" class="w-5 h-5"></i>
                    <span>Panduan Tanam</span>
                </button>
            @else
                <button class="w-full mt-6 flex justify-center items-center gap-2 px-6 py-3 bg-slate-100 text-slate-400 font-semibold rounded-xl border border-slate-200" disabled>
                    <span>Video Segera Hadir</span>
                </button>
            @endif
        </div>
    </div>
    @empty
    <div class="glass-card animate-up" style="grid-column: 1 / -1; text-align: center; padding: 4rem; border-color: var(--accent-red);">
        <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: var(--accent-red); margin-bottom: 1.5rem; display: block;"></i>
        <h3 style="margin-bottom: 10px;">Tidak Ada Kecocokan Optimal</h3>
        <p style="color: var(--text-secondary);">Parameter suhu ({{ $lahan->suhu_lahan }}°C) saat ini berada di luar batas toleransi bibit pangan yang tersedia.</p>
    </div>
    @endforelse
</div>

<div style="margin-top: 2rem;">
    <a href="{{ route('lahan.index') }}" class="inline-flex justify-center items-center gap-2 px-6 py-3 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold rounded-xl shadow-sm transition-all duration-300">
        <i data-lucide="arrow-left" class="w-5 h-5"></i>
        <span>Kembali ke Lahan</span>
    </a>
</div>

<div id="videoModal" class="video-modal" onclick="closeVideo()">
    <div style="width: 90%; max-width: 900px; aspect-ratio: 16/9; background: black; border-radius: 24px; overflow: hidden; border: 1px solid var(--primary-emerald); position: relative; box-shadow: 0 0 50px rgba(16, 185, 129, 0.2);" onclick="event.stopPropagation()">
        <iframe id="youtubeFrame" src="" style="width: 100%; height: 100%; border: none;" allowfullscreen allow="autoplay"></iframe>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openVideo(videoId) {
        document.getElementById('youtubeFrame').src = "https://www.youtube.com/embed/" + videoId + "?autoplay=1";
        document.getElementById('videoModal').style.display = "flex";
    }

    function closeVideo() {
        document.getElementById('youtubeFrame').src = "";
        document.getElementById('videoModal').style.display = "none";
    }
</script>
@endsection