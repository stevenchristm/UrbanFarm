@extends('layouts.app')

@section('title', 'Daftar Lahan Baru')

@section('content')
<div class="page-header animate-up">
    <h1>🌱 Daftarkan Lahan Strategis</h1>
    <p>Masukkan parameter lingkungan spesifik lahan Anda untuk kalibrasi algoritma AI.</p>
</div>

<div class="animate-up" style="max-width: 600px; margin: 0 auto; padding: 3rem; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 24px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08), 0 4px 10px rgba(0, 0, 0, 0.03);">
    <form action="{{ route('lahan.store') }}" method="POST">
        @csrf

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; font-size: 0.9rem; font-weight: 700; color: #334155; margin-bottom: 0.8rem;">Nama Lahan / Area</label>
            <input type="text" name="nama_lahan" placeholder="Contoh: Balkon Depan / Atap Rumah" required 
            style="width: 100%; padding: 14px; border-radius: 12px; border: 1px solid #cbd5e1; background: #ffffff; font-family: inherit; font-size: 0.95rem; outline: none; transition: all 0.3s; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);"
            onfocus="this.style.borderColor='#10b981'; this.style.boxShadow='0 0 0 4px rgba(16, 185, 129, 0.1)';" onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='inset 0 2px 4px rgba(0,0,0,0.02)';">
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; font-size: 0.9rem; font-weight: 700; color: #334155; margin-bottom: 0.8rem;">Lokasi Geografis (Kota)</label>
            <input type="text" name="lokasi_lahan" placeholder="Contoh: Malang / Jakarta Selatan" required 
            style="width: 100%; padding: 14px; border-radius: 12px; border: 1px solid #cbd5e1; background: #ffffff; font-family: inherit; font-size: 0.95rem; outline: none; transition: all 0.3s; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);"
            onfocus="this.style.borderColor='#10b981'; this.style.boxShadow='0 0 0 4px rgba(16, 185, 129, 0.1)';" onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='inset 0 2px 4px rgba(0,0,0,0.02)';">
            <p style="font-size: 0.75rem; color: #64748b; margin-top: 5px;"><i class="fas fa-info-circle"></i> Digunakan untuk sinkronisasi data cuaca real-time.</p>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; font-size: 0.9rem; font-weight: 700; color: #334155; margin-bottom: 0.8rem;">Luas Lahan (m²)</label>
            <input type="number" name="luas_lahan" placeholder="Misal: 5" required 
            style="width: 100%; padding: 14px; border-radius: 12px; border: 1px solid #cbd5e1; background: #ffffff; font-family: inherit; font-size: 0.95rem; outline: none; transition: all 0.3s; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);"
            onfocus="this.style.borderColor='#10b981'; this.style.boxShadow='0 0 0 4px rgba(16, 185, 129, 0.1)';" onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='inset 0 2px 4px rgba(0,0,0,0.02)';">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 2.5rem;">
            <div>
                <label style="display: block; font-size: 0.9rem; font-weight: 700; color: #334155; margin-bottom: 0.8rem;">Suhu Rata-rata (°C)</label>
                <input type="number" name="suhu_lahan" placeholder="Contoh: 28" required 
                style="width: 100%; padding: 14px; border-radius: 12px; border: 1px solid #cbd5e1; background: #ffffff; font-family: inherit; font-size: 0.95rem; outline: none; transition: all 0.3s; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);"
                onfocus="this.style.borderColor='#10b981'; this.style.boxShadow='0 0 0 4px rgba(16, 185, 129, 0.1)';" onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='inset 0 2px 4px rgba(0,0,0,0.02)';">
            </div>
            <div>
                <label style="display: block; font-size: 0.9rem; font-weight: 700; color: #334155; margin-bottom: 0.8rem;">Sinar Matahari (Jam)</label>
                <input type="number" name="cahaya_lahan" placeholder="Contoh: 6" required 
                style="width: 100%; padding: 14px; border-radius: 12px; border: 1px solid #cbd5e1; background: #ffffff; font-family: inherit; font-size: 0.95rem; outline: none; transition: all 0.3s; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);"
                onfocus="this.style.borderColor='#10b981'; this.style.boxShadow='0 0 0 4px rgba(16, 185, 129, 0.1)';" onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='inset 0 2px 4px rgba(0,0,0,0.02)';">
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-4 mt-8">
            <!-- Secondary Button: Batal -->
            <a href="{{ route('lahan.index') }}" class="w-full sm:w-auto flex justify-center items-center gap-2 px-6 py-3 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold rounded-xl shadow-sm transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
                <span>Batal dan Kembali</span>
            </a>
            
            <!-- Primary Button: Simpan -->
            <button type="submit" class="w-full sm:w-auto flex justify-center items-center gap-2 px-8 py-3 bg-emerald-500 hover:bg-emerald-400 text-white font-semibold rounded-xl shadow-[0_4px_15px_rgba(16,185,129,0.3)] hover:shadow-[0_4px_25px_rgba(16,185,129,0.5)] transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                <i data-lucide="save" class="w-5 h-5"></i>
                <span>Simpan & Eksekusi Analisis</span>
            </button>
        </div>
    </form>
</div>
@endsection