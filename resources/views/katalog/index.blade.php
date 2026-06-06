@extends('layouts.app')

@section('title', 'Katalog Tanaman')

@section('styles')
<style>
    /* Dynamic Mesh Background */
    .mesh-bg {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: -1;
        overflow: hidden;
        background-color: #f8fafc;
        pointer-events: none;
    }
    .blob-1 {
        position: absolute; top: -15%; left: -10%; width: 55vw; height: 55vw;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, rgba(16, 185, 129, 0) 70%);
        filter: blur(80px); animation: float 25s infinite alternate ease-in-out;
    }
    .blob-2 {
        position: absolute; bottom: -20%; right: -10%; width: 65vw; height: 65vw;
        background: radial-gradient(circle, rgba(6, 95, 70, 0.12) 0%, rgba(6, 95, 70, 0) 70%);
        filter: blur(100px); animation: float 30s infinite alternate-reverse ease-in-out;
    }
    .blob-3 {
        position: absolute; top: 30%; left: 45%; transform: translate(-50%, -50%); width: 50vw; height: 50vw;
        background: radial-gradient(circle, rgba(14, 165, 233, 0.12) 0%, rgba(167, 243, 208, 0.1) 40%, rgba(167, 243, 208, 0) 70%);
        filter: blur(90px); animation: float 22s infinite alternate ease-in-out;
    }
    @keyframes float {
        0% { transform: translate(0, 0) scale(1) rotate(0deg); }
        50% { transform: translate(3%, 5%) scale(1.05) rotate(2deg); }
        100% { transform: translate(-3%, -2%) scale(0.95) rotate(-2deg); }
    }

    /* Glassmorphism Card Core */
    .glass-card-premium {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: inset 0 0 0 1px rgba(255,255,255,0.1), 0 8px 30px rgba(16, 185, 129, 0.05);
        transition: all 0.5s cubic-bezier(0.2, 0.8, 0.2, 1);
    }
    .glass-card-premium:hover {
        background: rgba(255, 255, 255, 0.25);
        border-color: rgba(255, 255, 255, 0.5);
        box-shadow: inset 0 0 0 1px rgba(255,255,255,0.2), 0 15px 40px rgba(16, 185, 129, 0.15);
        transform: translateY(-5px) scale(1.01);
    }

    .concave-box {
        background: rgba(255, 255, 255, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.4);
        box-shadow: inset 0 4px 10px rgba(0, 0, 0, 0.03), inset 0 -2px 5px rgba(255, 255, 255, 0.8);
    }

    /* Image frame zoom */
    .img-zoom-container { overflow: hidden; border-radius: 1.5rem 1.5rem 0 0; }
    .img-zoom-container img { transition: transform 0.7s cubic-bezier(0.4, 0, 0.2, 1); }
    .glass-card-premium:hover .img-zoom-container img { transform: scale(1.08); }

    /* Shimmer Animation for Button */
    @keyframes shimmer {
        0% { transform: translateX(-150%) skewX(-20deg); }
        100% { transform: translateX(250%) skewX(-20deg); }
    }
    .btn-shimmer { position: relative; overflow: hidden; }
    .btn-shimmer::after {
        content: ''; position: absolute; top: 0; left: 0; width: 30%; height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.6), transparent);
        transform: translateX(-150%) skewX(-20deg);
        animation: shimmer 2s ease-in-out infinite;
    }

    /* Modal Animation */
    .modal-overlay { opacity: 0; transition: opacity 0.3s ease; }
    .modal-card { transform: scale(0.95) translateY(20px); opacity: 0; transition: all 0.4s cubic-bezier(0.2, 0.8, 0.2, 1); }
    .modal-overlay.active { opacity: 1; }
    .modal-overlay.active .modal-card { transform: scale(1) translateY(0); opacity: 1; }
</style>
@endsection

@section('background')
<!-- Mesh Background -->
<div class="mesh-bg">
    <div class="blob-1"></div>
    <div class="blob-2"></div>
    <div class="blob-3"></div>
</div>
@endsection

@section('content')

<div class="relative z-10 flex flex-col gap-10 pb-16">
    <!-- Header -->
    <div class="flex flex-col justify-center items-start animate-slide-up">
        <h1 class="text-4xl md:text-5xl font-extrabold text-slate-800 tracking-tight mb-3 flex items-center gap-4">
            <div class="p-3 bg-white/40 backdrop-blur-md rounded-2xl shadow-sm border border-white/50">
                <i data-lucide="book-open" class="w-8 h-8 md:w-10 md:h-10 text-emerald-600"></i>
            </div>
            {{ \App\Models\Setting::where('key','katalog_title')->value('value') ?? 'Eksplorasi Biodiversitas' }}
        </h1>
        <p class="text-slate-500 font-medium text-lg max-w-2xl">{{ \App\Models\Setting::where('key','katalog_subtitle')->value('value') ?? 'Pelajari karakteristik dan siklus spesifik tanaman pangan untuk mengoptimalkan hasil panen Anda bersama asisten AI.' }}</p>
    </div>

    <!-- Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 animate-slide-up" style="animation-delay: 0.1s;">
        @forelse($tanaman as $t)
        <div class="glass-card-premium rounded-[1.5rem] flex flex-col group relative overflow-visible">
            
            <!-- Image Frame -->
            <div class="img-zoom-container h-52 relative bg-emerald-50/50 border-b border-white/40">
                @if($t->foto_tanaman)
                    <img src="{{ asset('assets/img/bibit/' . $t->foto_tanaman) }}" alt="{{ $t->nama_tanaman }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <i data-lucide="leafy-green" class="w-20 h-20 text-emerald-200"></i>
                    </div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-slate-900/10 to-transparent"></div>
                <h3 class="absolute bottom-4 left-5 right-5 text-2xl font-extrabold text-white tracking-tight drop-shadow-md">{{ $t->nama_tanaman }}</h3>
            </div>

            <div class="p-6 flex flex-col flex-1 relative z-10">
                <!-- Info Badges -->
                <div class="grid grid-cols-1 gap-3 mb-6">
                    <div class="concave-box rounded-xl p-3 flex flex-col items-center justify-center text-center max-w-[160px] mx-auto w-full">
                        <i data-lucide="sun" class="w-5 h-5 text-amber-500 mb-1"></i>
                        <span class="text-sm font-extrabold text-slate-700">{{ $t->cahaya_jam }} Jam</span>
                        <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Sinar Matahari</span>
                    </div>
                </div>

                <!-- Temperature Range -->
                <div class="flex justify-center mb-6">
                    <div class="bg-emerald-50/60 backdrop-blur-sm border border-emerald-100/60 rounded-full px-4 py-2 flex items-center gap-2">
                        <i data-lucide="thermometer" class="w-4 h-4 text-emerald-600"></i>
                        <span class="text-sm font-bold text-slate-600">{{ round($t->suhu_min) }}°C <span class="text-slate-300 mx-1">—</span> {{ round($t->suhu_max) }}°C</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-auto flex flex-col gap-3">
                    <button onclick="openAiModal('{{ $t->id_tanaman }}', '{{ $t->nama_tanaman }}')" class="btn-shimmer flex justify-center items-center gap-2 px-6 py-3.5 bg-gradient-to-r from-emerald-500 to-teal-400 text-white font-bold rounded-xl shadow-md shadow-emerald-500/20 hover:shadow-lg hover:shadow-emerald-500/40 transition-all w-full group/btn">
                        <i data-lucide="sparkles" class="w-5 h-5 group-hover/btn:rotate-12 transition-transform"></i>
                        <span class="tracking-wide">Analisis Siklus AI</span>
                    </button>
                    
                    @if($t->video_id)
                        <button onclick="playVideo('{{ $t->video_id }}')" class="flex justify-center items-center gap-2 px-6 py-3.5 bg-white/60 hover:bg-white backdrop-blur-md border border-rose-100 hover:border-rose-300 text-rose-600 font-bold rounded-xl shadow-sm transition-all w-full group/play">
                            <div class="p-1 bg-rose-500 rounded-full text-white group-hover/play:scale-110 transition-transform">
                                <i data-lucide="play" class="w-3 h-3 ml-[2px] mt-[1px]"></i>
                            </div>
                            <span>Tonton Tutorial</span>
                        </button>
                    @else
                        <button class="flex justify-center items-center gap-2 px-6 py-3.5 bg-slate-50/50 border border-slate-200 text-slate-400 font-bold rounded-xl shadow-sm w-full opacity-60 cursor-not-allowed">
                            <div class="p-1 bg-slate-300 rounded-full text-white">
                                <i data-lucide="play" class="w-3 h-3 ml-[2px] mt-[1px]"></i>
                            </div>
                            <span>Video Belum Tersedia</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
            <div class="glass-card-premium rounded-3xl col-span-full text-center p-16">
                <i data-lucide="sprout" class="w-16 h-16 text-emerald-300 mx-auto mb-4"></i>
                <p class="text-slate-500 font-medium text-lg">Database botani belum terisi.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- YouTube Video Modal -->
<div id="youtubeModal" class="modal-overlay fixed inset-0 z-[100] bg-slate-900/80 backdrop-blur-md hidden items-center justify-center p-4">
    <div class="modal-card w-full max-w-5xl aspect-video bg-black rounded-3xl overflow-hidden border border-emerald-500/30 shadow-[0_0_50px_rgba(16,185,129,0.2)] relative flex flex-col">
        <button onclick="closeVideo()" class="absolute top-4 right-4 z-10 p-2 bg-black/50 hover:bg-red-500 rounded-full text-white backdrop-blur-sm transition-colors border border-white/20">
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>
        <iframe id="videoPlayer" src="" class="w-full h-full" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
    </div>
</div>

<!-- AI Lifecycle Modal -->
<div id="aiModal" class="modal-overlay fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="modal-card w-full max-w-xl bg-white/90 backdrop-blur-2xl rounded-[2rem] border border-white p-8 max-h-[90vh] overflow-y-auto shadow-2xl relative">
        <div class="flex justify-between items-center mb-6 border-b border-slate-200 pb-4">
            <h2 class="text-2xl font-extrabold text-slate-800 flex items-center gap-3">
                <div class="p-2 bg-emerald-100 text-emerald-600 rounded-xl">
                    <i data-lucide="brain-circuit" class="w-6 h-6"></i>
                </div>
                AI Lifecycle: <span id="aiPlantName" class="text-emerald-600 ml-1"></span>
            </h2>
            <button onclick="closeAiModal()" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-colors">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <div id="aiModalBody" class="min-h-[200px] flex flex-col items-center justify-center">
            <i data-lucide="loader-2" class="w-10 h-10 text-emerald-500 animate-spin mb-4"></i>
            <p class="text-slate-500 font-bold text-center">Groq AI sedang menyusun siklus realistis...</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const ytModal = document.getElementById('youtubeModal');
    const aiModal = document.getElementById('aiModal');
    const player = document.getElementById('videoPlayer');

    function playVideo(videoId) {
        document.body.appendChild(ytModal); // Fix fixed positioning context
        player.src = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
        ytModal.classList.remove('hidden');
        ytModal.classList.add('flex');
        setTimeout(() => ytModal.classList.add('active'), 10);
    }

    function closeVideo() {
        ytModal.classList.remove('active');
        setTimeout(() => {
            ytModal.classList.remove('flex');
            ytModal.classList.add('hidden');
            player.src = ""; 
        }, 300);
    }

    // AI Lifecycle Modal Logic
    function openAiModal(id, name) {
        document.body.appendChild(aiModal); // Fix fixed positioning context
        aiModal.classList.remove('hidden');
        aiModal.classList.add('flex');
        setTimeout(() => aiModal.classList.add('active'), 10);
        
        document.getElementById('aiPlantName').innerText = name;
        document.getElementById('aiModalBody').innerHTML = `
            <div class="h-40 flex flex-col items-center justify-center">
                <i data-lucide="loader-2" class="w-10 h-10 text-emerald-500 animate-spin mb-4"></i>
                <p class="text-slate-500 font-bold text-center">Groq AI sedang menyusun siklus agronomis...</p>
            </div>
        `;
        lucide.createIcons();

        fetch(`/katalog/${id}/ai-lifecycle`)
            .then(res => res.json())
            .then(data => {
                if(data.error) {
                    document.getElementById('aiModalBody').innerHTML = `<p class="text-red-500 text-center font-bold p-8"><i data-lucide="alert-circle" class="w-8 h-8 mx-auto mb-2"></i>${data.error}</p>`;
                    lucide.createIcons();
                    return;
                }
                
                let html = `
                    <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-4 mb-6 flex items-center justify-between">
                        <span class="font-bold text-slate-600">Total Estimasi Panen:</span>
                        <span class="text-xl font-black text-emerald-600">${data.total_days} Hari</span>
                    </div>
                    <div class="relative pl-6 md:pl-8 border-l-2 border-dashed border-emerald-200 flex flex-col gap-6">
                `;
                
                if (data.stages && data.stages.length > 0) {
                    data.stages.forEach(stage => {
                        let iconName = 'leaf'; // Default
                        let phaseLower = stage.phase.toLowerCase();
                        if(phaseLower.includes('vegetatif') || phaseLower.includes('daun')) iconName = 'leafy-green';
                        if(phaseLower.includes('bunga') || phaseLower.includes('buah') || phaseLower.includes('generatif')) iconName = 'flower';
                        if(phaseLower.includes('panen')) iconName = 'wheat';
                        
                        html += `
                        <div class="relative">
                            <div class="absolute -left-[35px] md:-left-[43px] top-1 w-6 h-6 rounded-full bg-emerald-100 border-4 border-white flex items-center justify-center shadow-sm">
                                <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                            </div>
                            <div class="bg-slate-50/80 border border-slate-200 rounded-xl p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-bold text-slate-800 flex items-center gap-2">
                                        <i data-lucide="${iconName}" class="w-4 h-4 text-emerald-600"></i>
                                        ${stage.phase}
                                    </h4>
                                    <span class="text-xs font-black bg-emerald-100 text-emerald-700 px-2 py-1 rounded-md">${stage.days} Hari</span>
                                </div>
                                <p class="text-sm text-slate-500 leading-relaxed">${stage.action}</p>
                            </div>
                        </div>`;
                    });
                } else {
                    html += `<p class="text-slate-400">Data fase belum tersedia.</p>`;
                }
                
                html += `</div>`;
                document.getElementById('aiModalBody').innerHTML = html;
                lucide.createIcons();
            })
            .catch(err => {
                document.getElementById('aiModalBody').innerHTML = `<p class="text-red-500 text-center font-bold p-8"><i data-lucide="wifi-off" class="w-8 h-8 mx-auto mb-2"></i>Gagal terhubung ke AI.</p>`;
                lucide.createIcons();
            });
    }

    function closeAiModal() {
        aiModal.classList.remove('active');
        setTimeout(() => {
            aiModal.classList.remove('flex');
            aiModal.classList.add('hidden');
        }, 300);
    }
</script>
@endsection