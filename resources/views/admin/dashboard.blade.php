<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel — UrbanFarm</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    @vite(['resources/css/app.css'])
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f0;
            min-height: 100vh;
            color: #1e2d1f;
        }
        .sidebar {
            width: 260px;
            background: linear-gradient(160deg, #0d2e16 0%, #0f3d1e 100%);
            min-height: 100vh;
            position: fixed;
            left: 0; top: 0;
            box-shadow: 4px 0 24px rgba(0,0,0,0.15);
        }
        .main-content {
            margin-left: 260px;
            padding: 32px;
            min-height: 100vh;
        }
        .page-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid #e4ede4;
            box-shadow: 0 2px 12px rgba(16,80,32,0.06);
            transition: box-shadow 0.25s;
        }
        .page-card:hover { box-shadow: 0 4px 24px rgba(16,80,32,0.12); }
        .page-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 24px;
            border-bottom: 1px solid #eef3ee;
            cursor: pointer;
            user-select: none;
        }
        .page-icon {
            width: 40px; height: 40px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            color: #16a34a;
            flex-shrink: 0;
        }
        .page-body { padding: 24px; display: none; }
        .page-body.open { display: block; }
        .field-group { margin-bottom: 20px; }
        .field-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: #6b7280;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .field-input, .field-textarea {
            width: 100%;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 14px;
            color: #1f2937;
            background: #f9fafb;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
            resize: none;
        }
        .field-input:focus, .field-textarea:focus {
            outline: none;
            border-color: #22c55e;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(34,197,94,0.12);
        }
        .btn-save {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: #fff;
            border: none;
            padding: 10px 24px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex; align-items: center; gap: 8px;
            transition: all 0.2s;
        }
        .btn-save:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(34,197,94,0.35); }
        .sidebar-nav-link {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 16px;
            border-radius: 10px;
            color: rgba(255,255,255,0.6);
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s;
            text-decoration: none;
        }
        .sidebar-nav-link:hover, .sidebar-nav-link.active {
            background: rgba(255,255,255,0.1);
            color: #fff;
        }
        .alert-success {
            background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d;
            padding: 14px 18px; border-radius: 12px;
            display: flex; align-items: center; gap: 10px;
            font-size: 14px; font-weight: 500;
            margin-bottom: 24px;
            animation: slideDown 0.3s ease;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .chevron-icon { transition: transform 0.2s; margin-left: auto; color: #9ca3af; }
        .page-header.open .chevron-icon { transform: rotate(180deg); }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar flex flex-col">
        <!-- Logo -->
        <div style="padding: 28px 24px 20px; border-bottom: 1px solid rgba(255,255,255,0.08);">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#22c55e,#16a34a);display:flex;align-items:center;justify-content:center;">
                    <i data-lucide="shield-check" style="width:22px;height:22px;color:#fff;"></i>
                </div>
                <div>
                    <div style="font-family:'Outfit',sans-serif;font-weight:700;color:#fff;font-size:16px;line-height:1.2;">Admin Panel</div>
                    <div style="font-size:11px;color:rgba(255,255,255,0.4);">UrbanFarm CMS</div>
                </div>
            </div>
        </div>

        <!-- Admin Info -->
        <div style="padding: 16px 24px; border-bottom: 1px solid rgba(255,255,255,0.06);">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:34px;height:34px;border-radius:50%;background:rgba(34,197,94,0.2);display:flex;align-items:center;justify-content:center;">
                    <i data-lucide="user" style="width:16px;height:16px;color:#4ade80;"></i>
                </div>
                <div>
                    <div style="font-size:13px;font-weight:600;color:#fff;">{{ session('admin_name') }}</div>
                    <div style="font-size:11px;color:rgba(255,255,255,0.4);">Administrator</div>
                </div>
            </div>
        </div>

        <!-- Nav -->
        <nav style="flex:1;padding:16px 12px;display:flex;flex-direction:column;gap:4px;">
            <p style="font-size:10px;font-weight:700;color:rgba(255,255,255,0.3);letter-spacing:0.08em;text-transform:uppercase;padding:0 8px;margin-bottom:6px;">Menu</p>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-nav-link active">
                <i data-lucide="layout-dashboard" style="width:16px;height:16px;"></i>
                Manajemen Teks
            </a>
            <a href="{{ route('dashboard') }}" target="_blank" class="sidebar-nav-link">
                <i data-lucide="external-link" style="width:16px;height:16px;"></i>
                Lihat Aplikasi
            </a>
        </nav>

        <!-- Logout -->
        <div style="padding:16px 12px;border-top:1px solid rgba(255,255,255,0.08);">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="sidebar-nav-link w-full" style="background:rgba(239,68,68,0.1);color:#f87171;border:none;cursor:pointer;">
                    <i data-lucide="log-out" style="width:16px;height:16px;"></i>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div style="margin-bottom:28px;">
            <h1 style="font-family:'Outfit',sans-serif;font-size:26px;font-weight:800;color:#0d2e16;margin:0 0 4px;">
                Manajemen Teks Halaman
            </h1>
            <p style="font-size:14px;color:#6b7280;margin:0;">
                Edit judul dan deskripsi di setiap halaman UrbanFarm. Perubahan langsung berlaku tanpa perlu deploy ulang.
            </p>
        </div>

        @if(session('success'))
        <div class="alert-success">
            <i data-lucide="check-circle" style="width:18px;height:18px;flex-shrink:0;"></i>
            {{ session('success') }}
        </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST" id="settingsForm">
            @csrf
            <div style="display:grid;gap:16px;">

                {{-- DASHBOARD --}}
                <div class="page-card">
                    <div class="page-header" onclick="toggleSection(this)">
                        <div class="page-icon"><i data-lucide="layout-dashboard" style="width:18px;height:18px;"></i></div>
                        <div>
                            <div style="font-weight:700;font-size:15px;color:#0d2e16;">Dashboard</div>
                            <div style="font-size:12px;color:#9ca3af;margin-top:1px;">Halaman beranda utama</div>
                        </div>
                        <i data-lucide="chevron-down" class="chevron-icon" style="width:18px;height:18px;"></i>
                    </div>
                    <div class="page-body open">
                        <div class="field-group">
                            <label class="field-label">Subjudul / Kalimat Sambutan</label>
                            <input type="text" name="dashboard_subtitle" class="field-input"
                                value="{{ $settings['dashboard_subtitle']->value ?? 'Pusat kendali ekosistem urban Anda aktif.' }}">
                        </div>
                        <button type="submit" class="btn-save"><i data-lucide="save" style="width:14px;height:14px;"></i> Simpan</button>
                    </div>
                </div>

                {{-- AREA LAHAN --}}
                <div class="page-card">
                    <div class="page-header" onclick="toggleSection(this)">
                        <div class="page-icon"><i data-lucide="map" style="width:18px;height:18px;"></i></div>
                        <div>
                            <div style="font-weight:700;font-size:15px;color:#0d2e16;">Area Lahan</div>
                            <div style="font-size:12px;color:#9ca3af;margin-top:1px;">Halaman manajemen lahan</div>
                        </div>
                        <i data-lucide="chevron-down" class="chevron-icon" style="width:18px;height:18px;"></i>
                    </div>
                    <div class="page-body">
                        <div class="field-group">
                            <label class="field-label">Judul Halaman</label>
                            <input type="text" name="lahan_title" class="field-input"
                                value="{{ $settings['lahan_title']->value ?? 'Area Lahan Strategis' }}">
                        </div>
                        <div class="field-group">
                            <label class="field-label">Deskripsi / Subjudul</label>
                            <textarea name="lahan_subtitle" class="field-textarea" rows="2">{{ $settings['lahan_subtitle']->value ?? '' }}</textarea>
                        </div>
                        <button type="submit" class="btn-save"><i data-lucide="save" style="width:14px;height:14px;"></i> Simpan</button>
                    </div>
                </div>

                {{-- ALUR KERJA --}}
                <div class="page-card">
                    <div class="page-header" onclick="toggleSection(this)">
                        <div class="page-icon"><i data-lucide="calendar-days" style="width:18px;height:18px;"></i></div>
                        <div>
                            <div style="font-weight:700;font-size:15px;color:#0d2e16;">Alur Kerja</div>
                            <div style="font-size:12px;color:#9ca3af;margin-top:1px;">Halaman jadwal pekerjaan</div>
                        </div>
                        <i data-lucide="chevron-down" class="chevron-icon" style="width:18px;height:18px;"></i>
                    </div>
                    <div class="page-body">
                        <div class="field-group">
                            <label class="field-label">Judul Halaman</label>
                            <input type="text" name="jadwal_title" class="field-input"
                                value="{{ $settings['jadwal_title']->value ?? 'Pusat Kendali Agronomi' }}">
                        </div>
                        <div class="field-group">
                            <label class="field-label">Deskripsi / Subjudul</label>
                            <textarea name="jadwal_subtitle" class="field-textarea" rows="2">{{ $settings['jadwal_subtitle']->value ?? '' }}</textarea>
                        </div>
                        <button type="submit" class="btn-save"><i data-lucide="save" style="width:14px;height:14px;"></i> Simpan</button>
                    </div>
                </div>

                {{-- EDUKASI BIBIT --}}
                <div class="page-card">
                    <div class="page-header" onclick="toggleSection(this)">
                        <div class="page-icon"><i data-lucide="book-open" style="width:18px;height:18px;"></i></div>
                        <div>
                            <div style="font-weight:700;font-size:15px;color:#0d2e16;">Edukasi Bibit</div>
                            <div style="font-size:12px;color:#9ca3af;margin-top:1px;">Halaman katalog tanaman</div>
                        </div>
                        <i data-lucide="chevron-down" class="chevron-icon" style="width:18px;height:18px;"></i>
                    </div>
                    <div class="page-body">
                        <div class="field-group">
                            <label class="field-label">Judul Halaman</label>
                            <input type="text" name="katalog_title" class="field-input"
                                value="{{ $settings['katalog_title']->value ?? 'Eksplorasi Biodiversitas' }}">
                        </div>
                        <div class="field-group">
                            <label class="field-label">Deskripsi / Subjudul</label>
                            <textarea name="katalog_subtitle" class="field-textarea" rows="2">{{ $settings['katalog_subtitle']->value ?? '' }}</textarea>
                        </div>
                        <button type="submit" class="btn-save"><i data-lucide="save" style="width:14px;height:14px;"></i> Simpan</button>
                    </div>
                </div>

                {{-- KOMUNITAS --}}
                <div class="page-card">
                    <div class="page-header" onclick="toggleSection(this)">
                        <div class="page-icon"><i data-lucide="users" style="width:18px;height:18px;"></i></div>
                        <div>
                            <div style="font-weight:700;font-size:15px;color:#0d2e16;">Komunitas</div>
                            <div style="font-size:12px;color:#9ca3af;margin-top:1px;">Halaman petani digital</div>
                        </div>
                        <i data-lucide="chevron-down" class="chevron-icon" style="width:18px;height:18px;"></i>
                    </div>
                    <div class="page-body">
                        <div class="field-group">
                            <label class="field-label">Judul Halaman</label>
                            <input type="text" name="komunitas_title" class="field-input"
                                value="{{ $settings['komunitas_title']->value ?? 'Komunitas Petani Digital' }}">
                        </div>
                        <div class="field-group">
                            <label class="field-label">Deskripsi / Subjudul</label>
                            <textarea name="komunitas_subtitle" class="field-textarea" rows="2">{{ $settings['komunitas_subtitle']->value ?? '' }}</textarea>
                        </div>
                        <button type="submit" class="btn-save"><i data-lucide="save" style="width:14px;height:14px;"></i> Simpan</button>
                    </div>
                </div>

                {{-- ASISTEN AI --}}
                <div class="page-card">
                    <div class="page-header" onclick="toggleSection(this)">
                        <div class="page-icon"><i data-lucide="sparkles" style="width:18px;height:18px;"></i></div>
                        <div>
                            <div style="font-weight:700;font-size:15px;color:#0d2e16;">Asisten AI</div>
                            <div style="font-size:12px;color:#9ca3af;margin-top:1px;">Halaman Pakar Botani AI</div>
                        </div>
                        <i data-lucide="chevron-down" class="chevron-icon" style="width:18px;height:18px;"></i>
                    </div>
                    <div class="page-body">
                        <div class="field-group">
                            <label class="field-label">Judul Chat AI</label>
                            <input type="text" name="ai_title" class="field-input"
                                value="{{ $settings['ai_title']->value ?? 'Pakar Botani AI' }}">
                        </div>
                        <div class="field-group">
                            <label class="field-label">Deskripsi / Subjudul</label>
                            <textarea name="ai_subtitle" class="field-textarea" rows="2">{{ $settings['ai_subtitle']->value ?? '' }}</textarea>
                        </div>
                        <button type="submit" class="btn-save"><i data-lucide="save" style="width:14px;height:14px;"></i> Simpan</button>
                    </div>
                </div>

                {{-- SIDEBAR --}}
                <div class="page-card">
                    <div class="page-header" onclick="toggleSection(this)">
                        <div class="page-icon"><i data-lucide="sidebar" style="width:18px;height:18px;"></i></div>
                        <div>
                            <div style="font-weight:700;font-size:15px;color:#0d2e16;">Sidebar Global</div>
                            <div style="font-size:12px;color:#9ca3af;margin-top:1px;">Teks pada panel kiri aplikasi</div>
                        </div>
                        <i data-lucide="chevron-down" class="chevron-icon" style="width:18px;height:18px;"></i>
                    </div>
                    <div class="page-body">
                        <div class="field-group">
                            <label class="field-label">Teks Pro Tips</label>
                            <textarea name="sidebar_protips" class="field-textarea" rows="2">{{ $settings['sidebar_protips']->value ?? '' }}</textarea>
                        </div>
                        <button type="submit" class="btn-save"><i data-lucide="save" style="width:14px;height:14px;"></i> Simpan</button>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <script>
        lucide.createIcons();

        function toggleSection(header) {
            const body = header.nextElementSibling;
            const isOpen = body.classList.contains('open');
            body.classList.toggle('open', !isOpen);
            header.classList.toggle('open', !isOpen);
        }
    </script>
</body>
</html>
