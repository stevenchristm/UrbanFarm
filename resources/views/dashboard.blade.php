@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<!-- Header & Station Capsule -->
<div class="flex flex-col lg:flex-row gap-6 mb-8">
    <!-- Welcome Text -->
    <div class="flex-1 flex flex-col justify-center">
        <h1 class="text-3xl font-display font-bold tracking-tight text-foreground mb-2">
            Halo, {{ Auth::user()->nama ?? Auth::user()->name }} <span class="wave">👋</span>
        </h1>
        <p class="text-muted-foreground">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }} · {{ \App\Models\Setting::where('key','dashboard_subtitle')->value('value') ?? 'Pusat kendali ekosistem urban Anda aktif.' }}</p>
    </div>

    <!-- Station Capsule -->
    <div class="glass-strong rounded-3xl p-6 flex items-center gap-8 shadow-elegant relative overflow-hidden">
        <!-- Decorator -->
        <div class="absolute -right-10 -top-10 w-32 h-32 bg-primary/20 rounded-full blur-3xl"></div>
        
        <!-- Main Weather -->
        <div class="flex items-center gap-4 relative z-10">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-sky-400 to-blue-500 flex items-center justify-center shadow-lg text-white">
                <i data-lucide="cloud-sun" class="w-8 h-8"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-2 h-2 rounded-full bg-primary animate-pulse-dot"></span>
                    <span class="text-xs font-semibold text-primary uppercase tracking-wider">Live</span>
                    <span class="text-xs text-muted-foreground ml-2"><i data-lucide="map-pin" class="w-3 h-3 inline"></i> Malang, ID</span>
                </div>
                <div class="text-4xl font-display font-bold tracking-tighter text-foreground">
                    {{ $weather['temp'] }}<span class="text-2xl text-muted-foreground font-medium">°C</span>
                </div>
                <div class="text-sm text-muted-foreground mt-1">{{ $weather['desc'] }}</div>
            </div>
        </div>

        <div class="h-16 w-px bg-border/50 hidden md:block"></div>

        <!-- Sub Stats -->
        <div class="hidden md:flex gap-6 relative z-10">
            <div>
                <div class="text-muted-foreground text-sm flex items-center gap-1.5 mb-1"><i data-lucide="droplets" class="w-4 h-4"></i> Humidity</div>
                <div class="font-bold text-lg">{{ $weather['humidity'] }}%</div>
            </div>
            <div>
                <div class="text-muted-foreground text-sm flex items-center gap-1.5 mb-1"><i data-lucide="wind" class="w-4 h-4"></i> Wind</div>
                <div class="font-bold text-lg">{{ $weather['wind'] }} km/h</div>
            </div>
        </div>
    </div>
</div>

<!-- KPI Strip -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="glass rounded-2xl p-5 hover-lift relative overflow-hidden group">
        <div class="flex justify-between items-start mb-4">
            <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                <i data-lucide="sprout" class="w-5 h-5"></i>
            </div>
        </div>
        <div class="text-2xl font-display font-bold">{{ $totalLahan }}</div>
        <div class="text-sm text-muted-foreground mt-1">Lahan Terkelola</div>
    </div>

    <div class="glass rounded-2xl p-5 hover-lift relative overflow-hidden group">
        <div class="flex justify-between items-start mb-4">
            <div class="w-10 h-10 rounded-xl bg-earth/10 flex items-center justify-center text-earth group-hover:scale-110 transition-transform">
                <i data-lucide="scissors" class="w-5 h-5"></i>
            </div>
        </div>
        <div class="text-2xl font-display font-bold">{{ $totalTanaman }}</div>
        <div class="text-sm text-muted-foreground mt-1">Vigor Tanaman</div>
    </div>

    <div class="glass rounded-2xl p-5 hover-lift relative overflow-hidden group">
        <div class="flex justify-between items-start mb-4">
            <div class="w-10 h-10 rounded-xl bg-sky/10 flex items-center justify-center text-sky group-hover:scale-110 transition-transform">
                <i data-lucide="map" class="w-5 h-5"></i>
            </div>
        </div>
        <div class="text-2xl font-display font-bold">{{ number_format($totalLuas) }} m²</div>
        <div class="text-sm text-muted-foreground mt-1">Luas Ekosistem</div>
    </div>

    <div class="glass rounded-2xl p-5 hover-lift relative overflow-hidden group">
        <div class="flex justify-between items-start mb-4">
            <div class="w-10 h-10 rounded-xl bg-accent/20 flex items-center justify-center text-accent-foreground group-hover:scale-110 transition-transform">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
        </div>
        <div class="text-2xl font-display font-bold">{{ $totalUser }}</div>
        <div class="text-sm text-muted-foreground mt-1">Petani Aktif</div>
    </div>
</div>

<!-- Active Cultivation Grid (Plant Cards) -->
<div class="mb-6 flex justify-between items-end">
    <div>
        <h2 class="text-xl font-display font-bold text-foreground">Active Cultivation</h2>
        <p class="text-sm text-muted-foreground">Penjadwalan & Lahan · {{ $jadwalUser->count() }} lahan aktif</p>
    </div>
    <button onclick="openModalTanam()" class="bg-primary text-primary-foreground hover:bg-primary-glow px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-glow flex items-center gap-2">
        <i data-lucide="plus" class="w-4 h-4"></i> Tanam Baru
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($jadwalUser as $j)
    @php
        $tugasAktif = collect($j->daftar_tugas_hari_ini)->first();
    @endphp
    <div class="glass rounded-3xl overflow-hidden hover-lift flex flex-col relative group">
        <!-- Image Header -->
        <div class="h-44 relative overflow-hidden bg-muted">
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent z-10"></div>
            @if(isset($katalogRaw->get($j->nama_tanaman)->foto_tanaman) && file_exists(public_path('assets/img/bibit/'.$katalogRaw->get($j->nama_tanaman)->foto_tanaman)))
                <img src="{{ asset('assets/img/bibit/'.$katalogRaw->get($j->nama_tanaman)->foto_tanaman) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            @else
                <div class="w-full h-full flex items-center justify-center text-5xl bg-gradient-to-br from-emerald-100 to-green-50">🌱</div>
            @endif
            
            <div class="absolute top-4 left-4 z-20">
                <span class="bg-white/90 backdrop-blur text-xs font-bold px-3 py-1.5 rounded-full text-primary shadow-sm ai-phase-text" data-id="{{ $katalogRaw->get($j->nama_tanaman)->id_tanaman ?? 0 }}" data-day="{{ $j->hariKe }}">
                    Tahap Pertumbuhan
                </span>
            </div>
            <div class="absolute top-4 right-4 z-20">
                <span class="bg-black/50 backdrop-blur border border-white/10 text-xs font-medium px-3 py-1.5 rounded-full text-white flex items-center gap-1.5">
                    <i data-lucide="map-pin" class="w-3 h-3"></i> {{ $j->nama_lahan }}
                </span>
            </div>
            
            <h3 class="absolute bottom-4 left-4 z-20 text-white font-display font-bold text-2xl">{{ $j->nama_tanaman }}</h3>
        </div>

        <!-- Card Body -->
        <div class="p-5 flex-1 flex flex-col">
            <!-- Progress -->
            <div class="mb-5 mt-1">
                <div class="flex justify-between text-xs font-bold mb-2">
                    <span class="text-primary">Hari {{ $j->hariKe ?? 1 }} dari {{ $j->totalHariPanen }}</span>
                    <span class="text-muted-foreground">Est. Panen: {{ \Carbon\Carbon::now()->addDays($j->totalHariPanen - ($j->hariKe ?? 1))->format('d M') }}</span>
                </div>
                <div class="h-2.5 bg-muted rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-primary rounded-full relative" style="width: {{ $j->progresPersen }}%">
                        <div class="absolute inset-0 bg-white/20 animate-shimmer"></div>
                    </div>
                </div>
            </div>

            <!-- Task -->
            <div class="bg-background/60 rounded-2xl p-3.5 flex items-center justify-between mt-auto border border-border/50">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground font-bold uppercase tracking-wider mb-0.5">Tugas Hari Ini</p>
                        <p class="text-sm font-bold text-foreground">{{ $tugasAktif['name'] ?? 'Monitor Rutin Ekosistem' }}</p>
                    </div>
                </div>
                <a href="{{ route('jadwal.index') }}" class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center hover:bg-primary-glow transition-colors shadow-sm">
                    <i data-lucide="chevron-right" class="w-5 h-5"></i>
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full py-16 text-center border-2 border-dashed border-border rounded-3xl bg-glass">
        <div class="w-20 h-20 rounded-full bg-muted flex items-center justify-center mx-auto mb-5">
            <i data-lucide="leaf" class="w-10 h-10 text-muted-foreground"></i>
        </div>
        <h3 class="text-xl font-display font-bold text-foreground mb-2">Belum Ada Tanaman Aktif</h3>
        <p class="text-muted-foreground mb-6 max-w-md mx-auto">Mulai blueprint tanam pertama Anda hari ini untuk memantau pertumbuhan menggunakan AI Agronomist.</p>
        <button onclick="openModalTanam()" class="bg-primary text-primary-foreground px-6 py-3 rounded-xl font-bold hover:shadow-glow transition-all">
            Inisialisasi Tanam Sekarang
        </button>
    </div>
    @endforelse
</div>

<!-- Modal -->
<div id="modalTanam" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-md">
    <div class="glass-strong rounded-3xl p-8 max-w-md w-full mx-4 shadow-elegant border border-white/20 relative animate-scale-in">
        <button onclick="closeModalTanam()" class="absolute top-6 right-6 text-muted-foreground hover:text-foreground transition-colors">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
        
        <h2 class="text-2xl font-display font-bold text-foreground mb-6">🌿 Inisialisasi Tanam</h2>
        
        <form action="{{ route('simpan.tanam') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-xs font-bold text-muted-foreground uppercase tracking-wider mb-2">Lokasi Lahan</label>
                <select name="lahan" required class="w-full bg-background border border-border rounded-xl px-4 py-3 text-sm font-medium outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all">
                    @foreach($labels as $l) <option value="{{ $l }}">{{ $l }}</option> @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-muted-foreground uppercase tracking-wider mb-2">Varietas Tanaman</label>
                <select name="tanaman" required class="w-full bg-background border border-border rounded-xl px-4 py-3 text-sm font-medium outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all">
                    @foreach($semuaKatalog as $k) <option value="{{ $k->nama_tanaman }}">{{ $k->nama_tanaman }}</option> @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-muted-foreground uppercase tracking-wider mb-2">Waktu Operasional</label>
                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full bg-background border border-border rounded-xl px-4 py-3 text-sm font-medium outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all">
            </div>
            
            <button type="submit" class="w-full bg-gradient-primary text-white font-bold rounded-xl py-3.5 hover:shadow-glow transition-all mt-4 text-base">
                Mulai Blueprint Tanam
            </button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openModalTanam() { 
        document.getElementById('modalTanam').classList.remove('hidden'); 
        document.getElementById('modalTanam').classList.add('flex'); 
    }
    function closeModalTanam() { 
        document.getElementById('modalTanam').classList.add('hidden'); 
        document.getElementById('modalTanam').classList.remove('flex'); 
    }
    window.onclick = function(event) { if (event.target.id === 'modalTanam') closeModalTanam(); }

    // AI Phase Fetcher
    document.addEventListener('DOMContentLoaded', function() {
        const phaseElements = document.querySelectorAll('.ai-phase-text');
        const cache = {}; 

        phaseElements.forEach(el => {
            const id = el.getAttribute('data-id');
            const day = parseInt(el.getAttribute('data-day'));
            
            if (!id || id == 0) {
                el.innerHTML = "Fase Aktif";
                return;
            }

            if (cache[id]) {
                processPhase(cache[id], el, day);
            } else {
                fetch(`/katalog/${id}/ai-lifecycle`)
                    .then(res => res.json())
                    .then(data => {
                        if (!data.error) {
                            cache[id] = data;
                            processPhase(data, el, day);
                        } else {
                            el.innerHTML = "Gagal memuat fase";
                        }
                    })
                    .catch(() => {
                        el.innerHTML = "Fase Aktif";
                    });
            }
        });

        function processPhase(data, el, currentDay) {
            let currentPhase = "Fase Aktif";
            let daysAccumulated = 0;
            
            if (data.stages && data.stages.length > 0) {
                for (let stage of data.stages) {
                    daysAccumulated += stage.days;
                    if (currentDay <= daysAccumulated) {
                        currentPhase = stage.phase;
                        break;
                    }
                }
                if (currentDay > daysAccumulated) {
                    currentPhase = data.stages[data.stages.length - 1].phase;
                }
            }
            el.innerHTML = currentPhase;
        }
        
        if(typeof lucide !== 'undefined') lucide.createIcons();
    });
</script>
@endsection
