@extends('layouts.app')

@section('title', 'Manajemen Lahan')

@section('styles')
<style>
    /* Mesh Gradient Background Elements */
    .mesh-bg {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: -1;
        overflow: hidden;
        background-color: #f8fafc; /* Fallback light background */
        pointer-events: none;
    }
    
    .blob-1 {
        position: absolute;
        top: -15%;
        left: -10%;
        width: 55vw;
        height: 55vw;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, rgba(16, 185, 129, 0) 70%);
        filter: blur(80px);
        animation: float 25s infinite alternate ease-in-out;
    }

    .blob-2 {
        position: absolute;
        bottom: -20%;
        right: -10%;
        width: 65vw;
        height: 65vw;
        background: radial-gradient(circle, rgba(6, 95, 70, 0.12) 0%, rgba(6, 95, 70, 0) 70%);
        filter: blur(100px);
        animation: float 30s infinite alternate-reverse ease-in-out;
    }

    .blob-3 {
        position: absolute;
        top: 30%;
        left: 45%;
        transform: translate(-50%, -50%);
        width: 50vw;
        height: 50vw;
        background: radial-gradient(circle, rgba(14, 165, 233, 0.12) 0%, rgba(167, 243, 208, 0.1) 40%, rgba(167, 243, 208, 0) 70%);
        filter: blur(90px);
        animation: float 22s infinite alternate ease-in-out;
    }

    @keyframes float {
        0% { transform: translate(0, 0) scale(1) rotate(0deg); }
        50% { transform: translate(3%, 5%) scale(1.05) rotate(2deg); }
        100% { transform: translate(-3%, -2%) scale(0.95) rotate(-2deg); }
    }

    /* Glassmorphism Card Core */
    .glass-card-premium {
        background: #ffffff;
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: inset 0 0 0 1px rgba(255,255,255,0.1), 0 8px 30px rgba(16, 185, 129, 0.05);
        transition: all 0.5s cubic-bezier(0.2, 0.8, 0.2, 1);
    }

    .glass-card-premium:hover {
        background: #ffffff;
        border-color: rgba(255, 255, 255, 0.4);
        box-shadow: inset 0 0 0 1px rgba(255,255,255,0.2), 0 15px 40px rgba(16, 185, 129, 0.15);
    }

    /* Inner Shadow Concave Effect */
    .concave-box {
        background: rgba(255, 255, 255, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.4);
        box-shadow: inset 0 4px 10px rgba(0, 0, 0, 0.03), inset 0 -2px 5px rgba(255, 255, 255, 0.8);
        transition: all 0.3s ease;
    }
    
    .glass-card-premium:hover .concave-box {
        background: rgba(255, 255, 255, 0.35);
    }

    /* Shimmer Animation for Button */
    @keyframes shimmer {
        0% { transform: translateX(-150%) skewX(-20deg); }
        100% { transform: translateX(250%) skewX(-20deg); }
    }
    
    .btn-shimmer {
        position: relative;
        overflow: hidden;
    }
    
    .btn-shimmer::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 30%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.6), transparent);
        transform: translateX(-150%) skewX(-20deg);
        transition: none;
    }
    
    .glass-card-premium:hover .btn-shimmer::after {
        animation: shimmer 1.5s ease-in-out infinite;
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

<!-- Floating Action Button (FAB) -->
<a href="{{ route('lahan.create') }}" class="fixed bottom-8 right-8 z-50 flex items-center justify-center gap-3 px-5 md:px-6 py-4 bg-gradient-to-r from-emerald-500 to-emerald-400 text-white font-extrabold rounded-full shadow-[0_0_30px_rgba(16,185,129,0.4)] hover:shadow-[0_0_40px_rgba(16,185,129,0.6)] transition-all duration-300 transform hover:scale-110 hover:-translate-y-1 group border border-emerald-300/50">
    <i data-lucide="plus" class="w-6 h-6 transition-transform duration-500 group-hover:rotate-90"></i>
    <span class="hidden md:block tracking-wide">Tambah Lahan</span>
</a>

<div class="relative z-10 flex flex-col gap-8 pb-16">
    <!-- Header -->
    <div class="flex flex-col justify-center items-start animate-slide-up">
        <h1 class="text-4xl md:text-5xl font-extrabold text-slate-800 tracking-tight mb-3 flex items-center gap-4">
            <div class="p-3 bg-white/40 backdrop-blur-md rounded-2xl shadow-sm border border-white/50">
                <i data-lucide="map" class="w-8 h-8 md:w-10 md:h-10 text-emerald-600"></i>
            </div>
            {{ \App\Models\Setting::where('key','lahan_title')->value('value') ?? 'Area Lahan Strategis' }}
        </h1>
        <p class="text-slate-500 font-medium text-lg max-w-2xl">
            {{ \App\Models\Setting::where('key','lahan_subtitle')->value('value') ?? 'Pusat kendali pintar. Kelola dan pantau seluruh parameter lingkungan agrikultur Anda secara presisi tinggi.' }}
        </p>
    </div>

    <!-- Feedback Messages -->
    @if(session('success'))
        <div class="glass-card-premium rounded-2xl p-4 border-l-4 border-l-emerald-500 animate-slide-up">
            <div class="flex items-center gap-3 text-emerald-700 font-semibold">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="glass-card-premium rounded-2xl p-4 border-l-4 border-l-red-500 animate-slide-up">
            <div class="flex items-center gap-3 text-red-700 font-semibold">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Cards Grid -->
    @if($spaces->isEmpty())
        <div class="glass-card-premium rounded-3xl p-16 text-center animate-slide-up" style="animation-delay: 0.1s;">
            <div class="w-24 h-24 mx-auto mb-6 bg-emerald-50/50 backdrop-blur-sm rounded-full flex items-center justify-center text-emerald-300 border border-emerald-100">
                <i data-lucide="sprout" class="w-12 h-12"></i>
            </div>
            <h3 class="text-2xl font-bold text-slate-700 mb-2">Ekosistem Lahan Kosong</h3>
            <p class="text-slate-500 mb-6 max-w-md mx-auto">Inisialisasi lahan pertama Anda untuk mulai memantau dan mengoptimalkan lingkungan secara cerdas dengan AI.</p>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8 animate-slide-up" style="animation-delay: 0.1s;">
            @foreach($spaces as $l)
                <div class="glass-card-premium rounded-3xl p-6 relative group overflow-hidden flex flex-col hover:scale-[1.02] transform-gpu">
                    
                    <!-- Botanical Watermark / Background Element -->
                    <i data-lucide="trees" class="absolute -bottom-8 -right-8 w-56 h-56 text-emerald-800/5 rotate-[-15deg] transition-transform duration-700 group-hover:rotate-[-5deg] group-hover:scale-110 pointer-events-none z-0"></i>
                    
                    <!-- Subtle Glow behind card content -->
                    <div class="absolute -top-12 -right-12 w-40 h-40 bg-emerald-400/10 rounded-full blur-3xl transition-all duration-500 group-hover:bg-emerald-400/20 group-hover:scale-150 pointer-events-none z-0"></div>
                    
                    <!-- Card Header: Image/Icon & Titles -->
                    <div class="flex items-start gap-5 mb-6 relative z-10">
                        <!-- Left Decorative Element -->
                        <div class="w-16 h-16 shrink-0 rounded-2xl bg-gradient-to-br from-emerald-100/90 to-teal-50/90 flex items-center justify-center shadow-inner border border-white/60 relative overflow-hidden">
                            <div class="absolute inset-0 bg-emerald-500/5 backdrop-blur-sm"></div>
                            <i data-lucide="leaf" class="w-8 h-8 text-emerald-600 drop-shadow-sm relative z-10"></i>
                        </div>
                        
                        <!-- Identity -->
                        <div class="flex-1 min-w-0 pt-1">
                            <h3 class="text-2xl font-extrabold text-slate-800 truncate mb-1 tracking-tight">{{ $l->nama_lahan }}</h3>
                            <div class="flex items-center gap-1.5 text-sm text-emerald-600 font-bold">
                                <i data-lucide="map-pin" class="w-4 h-4"></i>
                                <span class="truncate">{{ $l->lokasi_lahan ?: 'Malang' }}</span>
                            </div>
                            <div class="text-xs text-slate-400 mt-1.5 font-bold tracking-widest uppercase">ID: LHN-{{ $l->id_lahan }}</div>
                        </div>
                    </div>

                    <!-- Environmental Parameters -->
                    <div class="grid grid-cols-2 gap-4 mb-6 relative z-10">
                        <div class="concave-box rounded-2xl p-4 flex flex-col items-center justify-center text-center">
                            <div class="p-2 bg-amber-50 rounded-full mb-2 shadow-sm border border-amber-100">
                                <i data-lucide="thermometer" class="w-6 h-6 text-orange-500"></i>
                            </div>
                            <span class="text-2xl font-extrabold text-slate-700 tracking-tight">{{ $l->suhu_lahan }}°C</span>
                            <span class="text-[11px] text-slate-500 font-bold uppercase tracking-wider mt-1">Suhu Area</span>
                        </div>
                        <div class="concave-box rounded-2xl p-4 flex flex-col items-center justify-center text-center">
                            <div class="p-2 bg-yellow-50 rounded-full mb-2 shadow-sm border border-yellow-100">
                                <i data-lucide="sun" class="w-6 h-6 text-amber-400"></i>
                            </div>
                            <span class="text-2xl font-extrabold text-slate-700 tracking-tight">{{ $l->cahaya_lahan }}h</span>
                            <span class="text-[11px] text-slate-500 font-bold uppercase tracking-wider mt-1">Pencahayaan</span>
                        </div>
                    </div>

                    <!-- Size & Owner -->
                    <div class="flex justify-between items-center px-1 mb-6 text-sm relative z-10">
                        <div class="flex items-center gap-2 bg-emerald-50/70 backdrop-blur-sm px-3 py-1.5 rounded-lg border border-emerald-200/50 shadow-sm">
                            <i data-lucide="scaling" class="w-4 h-4 text-emerald-600"></i>
                            <span class="text-slate-600 font-medium"><strong class="text-slate-800 text-base">{{ $l->luas_lahan }}</strong> m²</span>
                        </div>
                        <div class="flex items-center gap-2 text-slate-500 font-medium bg-white/40 backdrop-blur-sm px-3 py-1.5 rounded-lg border border-white/60 shadow-sm">
                            <i data-lucide="user" class="w-4 h-4 text-slate-400"></i>
                            <span class="truncate max-w-[90px]">{{ Auth::user()->nama ?? Auth::user()->name }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-auto pt-5 border-t border-slate-200/40 flex items-center justify-between gap-3 relative z-10">
                        <a href="{{ route('lahan.rekomendasi', $l->id_lahan) }}" class="btn-shimmer flex-1 flex items-center justify-center gap-2 py-3 px-4 bg-gradient-to-r from-emerald-500 to-teal-400 text-white font-bold rounded-xl shadow-md shadow-emerald-500/20">
                            <i data-lucide="bot" class="w-5 h-5"></i>
                            <span class="tracking-wide">AI Analitik</span>
                        </a>
                        
                        <div class="flex gap-2 shrink-0">
                            <a href="{{ route('lahan.edit', $l->id_lahan) }}" class="p-3 bg-white/60 hover:bg-white border border-slate-200 hover:border-emerald-300 text-slate-500 hover:text-emerald-600 rounded-xl transition-all duration-300 shadow-sm" title="Edit Lahan">
                                <i data-lucide="edit-3" class="w-5 h-5"></i>
                            </a>
                            <form action="{{ route('lahan.destroy', $l->id_lahan) }}" method="POST" id="delete-form-{{ $l->id_lahan }}" class="m-0">
                                @csrf @method('DELETE')
                                <input type="hidden" name="password_konfirmasi" id="pass-field-{{ $l->id_lahan }}">
                                <button type="button" class="p-3 bg-white/60 hover:bg-red-500 border border-slate-200 hover:border-red-500 text-red-500 hover:text-white rounded-xl transition-all duration-300 shadow-sm group/btn" title="Hapus Lahan" onclick="confirmDeletePassword('{{ $l->id_lahan }}')">
                                    <i data-lucide="trash-2" class="w-5 h-5 transition-transform group-hover/btn:scale-110"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    function confirmDeletePassword(id) {
        let userPassword = prompt("PROSEDUR KEAMANAN: Masukkan password Anda untuk konfirmasi penghapusan:");
        if (userPassword) {
            const inputField = document.getElementById('pass-field-' + id);
            const form = document.getElementById('delete-form-' + id);
            if (inputField && form) {
                inputField.value = userPassword;
                form.submit();
            }
        }
    }
</script>
@endsection