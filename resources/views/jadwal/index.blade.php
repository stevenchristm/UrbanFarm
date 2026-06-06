@extends('layouts.app')

@section('title', 'Alur Kerja Produksi')

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

    /* Glassmorphism Classes */
    .glass-card-premium {
        background: #ffffff;
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: 0 8px 30px rgba(16, 185, 129, 0.05);
        transition: all 0.5s cubic-bezier(0.2, 0.8, 0.2, 1);
    }
    .concave-box {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05), 0 2px 4px rgba(0, 0, 0, 0.02);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .concave-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08), 0 4px 8px rgba(0, 0, 0, 0.04);
    }
    .btn-shimmer {
        position: relative; overflow: hidden;
    }
    @keyframes shimmer {
        0% { transform: translateX(-150%) skewX(-20deg); }
        100% { transform: translateX(250%) skewX(-20deg); }
    }
    .btn-shimmer::after {
        content: ''; position: absolute; top: 0; left: 0; width: 30%; height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.6), transparent);
        transform: translateX(-150%) skewX(-20deg);
        animation: shimmer 2.5s ease-in-out infinite;
    }
    
    /* Progress Bar */
    .progress-bar-glass {
        height: 12px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50px;
        overflow: hidden;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
    }
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #10b981, #34d399);
        border-radius: 50px;
        transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 0 15px rgba(16, 185, 129, 0.6);
    }
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
                <i data-lucide="dna" class="w-8 h-8 md:w-10 md:h-10 text-emerald-600"></i>
            </div>
            {{ \App\Models\Setting::where('key','jadwal_title')->value('value') ?? 'Pusat Kendali Agronomi' }}
        </h1>
        <p class="text-slate-500 font-medium text-lg max-w-2xl">{{ \App\Models\Setting::where('key','jadwal_subtitle')->value('value') ?? 'Algoritma UrbanFarm Master Agronomist telah mensinkronisasi profil pertumbuhan Anda dengan cuaca mikro saat ini.' }}</p>
    </div>

    @if($semuaJadwal->isEmpty())
        <div class="glass-card-premium rounded-3xl p-16 text-center animate-slide-up">
            <div class="w-24 h-24 mx-auto mb-6 bg-emerald-50/50 backdrop-blur-sm rounded-full flex items-center justify-center text-emerald-300 border border-emerald-100">
                <i data-lucide="cpu" class="w-12 h-12"></i>
            </div>
            <h3 class="text-2xl font-bold text-slate-700 mb-2">Belum Ada Ekosistem Aktif</h3>
            <p class="text-slate-500 mb-6 max-w-md mx-auto">Inisialisasi siklus tanam Anda di Area Lahan untuk memulai pemantauan AI presisi.</p>
            <a href="{{ route('lahan.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-400 text-white font-bold rounded-xl shadow-md hover:shadow-lg shadow-emerald-500/30 transition-all hover:-translate-y-1">
                <i data-lucide="rocket" class="w-5 h-5"></i> Inisialisasi Sekarang
            </a>
        </div>
    @else
        <div class="flex flex-col gap-10">
            @foreach($semuaJadwal as $j)
            <section class="glass-card-premium rounded-[2rem] p-8 md:p-10 relative overflow-hidden animate-slide-up group">
                <!-- Botanical Background Pattern -->
                <i data-lucide="sprout" class="absolute -right-10 -bottom-10 w-64 h-64 text-emerald-800/5 rotate-[-20deg] pointer-events-none group-hover:scale-110 transition-transform duration-1000 z-0"></i>

                <div class="relative z-10">
                    <!-- Header Section -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
                        <div>
                            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-800 tracking-tight mb-2">{{ $j->nama_tanaman }}</h2>
                            <div class="flex items-center gap-2 text-emerald-600 font-bold bg-emerald-50/50 px-3 py-1.5 rounded-lg border border-emerald-100/50 inline-flex">
                                <i data-lucide="map-pin" class="w-4 h-4"></i>
                                <span>{{ $j->nama_lahan }}</span>
                            </div>
                        </div>
                        <div class="text-right concave-box px-6 py-4 rounded-2xl">
                            <div class="text-[11px] text-slate-500 font-bold uppercase tracking-widest mb-1">Estimasi Panen</div>
                            <div class="text-4xl font-black text-slate-700 tracking-tighter">{{ max(0, $j->totalHariPanen - $j->hariKe) }} <span class="text-lg text-slate-400 font-bold">HARI LAGI</span></div>
                        </div>
                    </div>

                    <!-- Progress Bar Section -->
                    <div class="mb-12">
                        <div class="flex justify-between items-end mb-3">
                            <div class="flex items-center gap-2 text-emerald-600">
                                <i data-lucide="calendar-days" class="w-5 h-5"></i>
                                <span class="font-extrabold tracking-wide">HARI KE {{ $j->hariKe }}</span>
                            </div>
                            <span class="font-black text-slate-500 text-lg">{{ $j->progresPersen }}% <span class="text-xs tracking-widest">COMPLETE</span></span>
                        </div>
                        <div class="progress-bar-glass mb-4">
                            <div class="progress-fill" style="width: {{ $j->progresPersen }}%"></div>
                        </div>
                        <div class="grid grid-cols-4 gap-2 text-center text-[10px] md:text-xs font-bold uppercase tracking-wider">
                            <div class="{{ $j->progresPersen >= 5 ? 'text-emerald-500' : 'text-slate-400' }} transition-colors duration-500">Penyemaian</div>
                            <div class="{{ $j->progresPersen >= 25 ? 'text-emerald-500' : 'text-slate-400' }} transition-colors duration-500">Vegetatif</div>
                            <div class="{{ $j->progresPersen >= 60 ? 'text-emerald-500' : 'text-slate-400' }} transition-colors duration-500">Generatif</div>
                            <div class="{{ $j->progresPersen >= 95 ? 'text-emerald-500' : 'text-slate-400' }} transition-colors duration-500">Panen</div>
                        </div>
                    </div>

                    <!-- Timeline Tasks -->
                    <div class="pl-4 md:pl-8 border-l-2 border-dashed border-emerald-200/50 flex flex-col gap-8 relative">
                        @foreach($j->daftar_tugas_hari_ini as $t)
                        @php
                            $icon = 'leaf';
                            $colorClass = 'text-emerald-500';
                            $bgClass = 'bg-emerald-50 border-emerald-100';
                            
                            $cat = $t['category'] ?? '';
                            if(strpos($cat, 'Penyiraman') !== false) { 
                                $icon = 'droplet'; 
                                $colorClass = 'text-blue-500'; 
                                $bgClass = 'bg-blue-50 border-blue-100';
                            } elseif(strpos($cat, 'Pemupukan') !== false) { 
                                $icon = 'flask-conical'; 
                                $colorClass = 'text-purple-500'; 
                                $bgClass = 'bg-purple-50 border-purple-100';
                            } elseif(strpos($cat, 'Hama') !== false || strpos($cat, 'Protection') !== false) { 
                                $icon = 'shield-alert'; 
                                $colorClass = 'text-red-500'; 
                                $bgClass = 'bg-red-50 border-red-100';
                            }
                        @endphp
                        
                        <div class="relative {{ $t['is_done'] ? 'opacity-70 grayscale-[0.3]' : 'hover:-translate-y-1' }} transition-all duration-300">
                            <!-- Timeline Dot -->
                            <div class="absolute -left-[23px] md:-left-[39px] top-8 w-4 h-4 rounded-full {{ $t['is_done'] ? 'bg-slate-300' : 'bg-emerald-400 shadow-[0_0_10px_rgba(52,211,153,0.8)]' }} border-4 border-[#f8fafc]"></div>
                            
                            <!-- Task Card -->
                            <div class="concave-box rounded-[1.5rem] p-6 flex flex-col lg:flex-row gap-6 items-start lg:items-center {{ $t['is_overdue'] && !$t['is_done'] ? 'border-red-200 bg-red-50/10' : 'hover:bg-white/40' }} transition-colors">
                                
                                <!-- Icon Box -->
                                <div class="w-16 h-16 shrink-0 rounded-2xl flex items-center justify-center border shadow-sm {{ $bgClass }}">
                                    <i data-lucide="{{ $icon }}" class="w-8 h-8 {{ $colorClass }}"></i>
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center flex-wrap gap-3 mb-2">
                                        <div class="flex items-center gap-1.5 text-sm font-bold text-slate-500 bg-white/50 px-2.5 py-1 rounded-md border border-slate-200/60">
                                            <i data-lucide="clock" class="w-4 h-4"></i>
                                            <span>{{ $t['time'] }} WIB</span>
                                        </div>
                                        
                                        @if(isset($t['fase']))
                                            <span class="text-[10px] font-black uppercase tracking-widest bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-md">{{ $t['fase'] }}</span>
                                        @endif

                                        @if($t['is_overdue'] && !$t['is_done'])
                                            <span class="text-[10px] font-black uppercase tracking-widest bg-red-100 text-red-600 px-2.5 py-1 rounded-md flex items-center gap-1">
                                                <i data-lucide="alert-triangle" class="w-3 h-3"></i> Expired
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <h3 class="text-xl font-bold text-slate-800 mb-2">{{ $t['name'] }}</h3>
                                    <p class="text-slate-500 text-sm leading-relaxed mb-4 max-w-3xl">
                                        {{ $t['desc'] ?? ($t['description'] ?? 'Parameter sistem sedang mengkalibrasi instruksi perawatan harian.') }}
                                    </p>

                                    @if(!empty($t['alat_bahan']) && is_array($t['alat_bahan']))
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($t['alat_bahan'] as $tools)
                                            <span class="text-[11px] font-bold text-slate-600 bg-slate-100/80 border border-slate-200 px-3 py-1 rounded-full flex items-center gap-1">
                                                <i data-lucide="wrench" class="w-3 h-3"></i> {{ $tools }}
                                            </span>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>

                                <!-- Action Button -->
                                <div class="shrink-0 w-full lg:w-auto mt-4 lg:mt-0 flex justify-end">
                                    @if($t['is_done'])
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center border border-emerald-200 mb-1">
                                                <i data-lucide="check" class="w-6 h-6"></i>
                                            </div>
                                            <span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Selesai</span>
                                        </div>
                                    @elseif($t['is_future'])
                                        <div class="px-5 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-400 font-bold text-sm flex items-center gap-2">
                                            <i data-lucide="lock" class="w-4 h-4"></i> Belum Waktunya
                                        </div>
                                    @else
                                        <button onclick="finishTask({{ $j->id }}, {{ $t['step'] }}, this)" class="btn-shimmer flex items-center gap-2 px-6 py-3.5 bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 text-white font-bold rounded-xl transition-all duration-300 shadow-md hover:shadow-lg shadow-emerald-500/20 w-full lg:w-auto justify-center">
                                            <i data-lucide="check-square" class="w-5 h-5"></i>
                                            <span>Konfirmasi</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Footer Action -->
                    <div class="mt-10 pt-6 border-t border-slate-200/50 flex flex-col md:flex-row justify-between items-center gap-4">
                        <div>
                            @if($j->missedTasksCount > 0)
                            <button onclick="showAttention({{ $j->id }}, {{ $j->hariKe }}, {{ $j->missedTasksCount }}, this)" class="flex items-center gap-2 px-5 py-3 bg-red-500 hover:bg-red-600 border border-red-600 text-white rounded-xl transition-all duration-300 font-bold shadow-sm shadow-red-500/30 group animate-pulse hover:animate-none w-full md:w-auto justify-center">
                                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                                <span>Perhatian</span>
                            </button>
                            @endif
                        </div>
                        <form action="{{ route('jadwal.destroy', $j->id) }}" method="POST" onsubmit="return confirm('Hentikan siklus agronomis ini? Seluruh data riwayat akan dihapus dan tidak dapat dikembalikan.')" class="w-full md:w-auto">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-full md:w-auto flex justify-center items-center gap-2 px-5 py-3 bg-white/40 hover:bg-red-50 border border-red-200 text-red-500 hover:text-red-600 rounded-xl transition-all duration-300 font-bold shadow-sm group">
                                <i data-lucide="power-off" class="w-4 h-4 transition-transform group-hover:scale-110"></i>
                                <span>Hentikan Produksi</span>
                            </button>
                        </form>
                    </div>
                </div>
            </section>
            @endforeach
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
function finishTask(id, step, btn) {
    if(!confirm("Anda yakin tugas ini telah selesai?")) return;

    btn.disabled = true;
    const originalContent = btn.innerHTML;
    btn.innerHTML = '<i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i><span>Memproses...</span>';
    lucide.createIcons();

    fetch(`/complete-task/${id}`, {
        method: 'POST',
        headers: { 
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ step: step })
    })
    .then(response => {
        if (!response.ok) throw new Error('Query error');
        return response.json();
    })
    .then(data => {
        setTimeout(() => { location.reload(); }, 300);
    })
    .catch(error => {
        alert("Gagal sinkronisasi dengan server.");
        btn.disabled = false;
        btn.innerHTML = originalContent;
    });
}

function showAttention(id, hariKe, missedCount, btn) {
    const originalContent = btn.innerHTML;
    btn.innerHTML = '<i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i><span>Menganalisis...</span>';
    btn.disabled = true;
    lucide.createIcons();

    fetch(`/semua-jadwal/${id}/attention?hari_ke=${hariKe}&missed_count=${missedCount}`)
    .then(response => response.json())
    .then(data => {
        btn.innerHTML = originalContent;
        btn.disabled = false;
        lucide.createIcons();
        
        if(data.analysis) {
            showAiModal(data.analysis);
        } else {
            alert("Gagal memuat analisis AI.");
        }
    })
    .catch(error => {
        btn.innerHTML = originalContent;
        btn.disabled = false;
        lucide.createIcons();
        alert("Terjadi kesalahan jaringan.");
    });
}

function showAiModal(message) {
    const modalId = 'ai-modal-' + Math.random().toString(36).substr(2, 9);

    // Build modal structure without putting 'message' inside a template literal
    const overlay = document.createElement('div');
    overlay.id = modalId;
    overlay.className = 'fixed inset-0 z-[200] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm opacity-0 transition-opacity duration-300';

    const card = document.createElement('div');
    card.className = 'bg-white rounded-3xl p-8 max-w-lg w-full shadow-2xl transform scale-95 transition-all duration-300 relative border border-rose-100';

    const botBadge = document.createElement('div');
    botBadge.className = 'absolute -top-6 -right-6 w-16 h-16 bg-rose-100 rounded-full flex items-center justify-center border-4 border-white shadow-sm';
    botBadge.innerHTML = '<i data-lucide="bot" class="w-8 h-8 text-rose-500"></i>';

    const title = document.createElement('h3');
    title.className = 'text-2xl font-extrabold text-slate-800 mb-2 flex items-center gap-2';
    title.innerHTML = '<i data-lucide="triangle-alert" class="w-6 h-6 text-rose-500"></i> Peringatan Kelalaian';

    const subtitle = document.createElement('p');
    subtitle.className = 'text-slate-500 text-sm font-medium mb-6';
    subtitle.textContent = 'Analisis dari UrbanFarm AI Master Agronomist';

    const textBox = document.createElement('div');
    textBox.className = 'bg-rose-50/50 border border-rose-100 p-5 rounded-2xl mb-6';

    const textContent = document.createElement('p');
    textContent.className = 'text-slate-700 leading-relaxed text-[15px] font-medium';
    textContent.textContent = message; // Safely set text - no template literal issues
    textBox.appendChild(textContent);

    const footer = document.createElement('div');
    footer.className = 'flex justify-end';

    const closeBtn = document.createElement('button');
    closeBtn.className = 'px-6 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl transition-all shadow-md';
    closeBtn.textContent = 'Mengerti';
    closeBtn.onclick = () => closeAiModal(modalId);
    footer.appendChild(closeBtn);

    card.appendChild(botBadge);
    card.appendChild(title);
    card.appendChild(subtitle);
    card.appendChild(textBox);
    card.appendChild(footer);
    overlay.appendChild(card);
    document.body.appendChild(overlay);

    lucide.createIcons();

    // Animate in
    setTimeout(() => {
        overlay.classList.remove('opacity-0');
        card.classList.remove('scale-95');
        card.classList.add('scale-100');
    }, 50);
}

window.closeAiModal = function(modalId) {
    const overlay = document.getElementById(modalId);
    if (!overlay) return;
    const card = overlay.querySelector('div');
    overlay.classList.add('opacity-0');
    if (card) { card.classList.remove('scale-100'); card.classList.add('scale-95'); }
    setTimeout(() => { if (overlay) overlay.remove(); }, 300);
}
</script>
@endsection