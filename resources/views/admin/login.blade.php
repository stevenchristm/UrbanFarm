<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — UrbanFarm</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    @vite(['resources/css/app.css'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f2112 0%, #0d3320 40%, #0a1f14 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .bg-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.3;
            animation: drift 12s ease-in-out infinite alternate;
        }
        .orb-1 { width: 500px; height: 500px; background: radial-gradient(circle, #22c55e, transparent); top: -150px; left: -150px; animation-delay: 0s; }
        .orb-2 { width: 400px; height: 400px; background: radial-gradient(circle, #16a34a, transparent); bottom: -100px; right: -100px; animation-delay: -4s; }
        .orb-3 { width: 300px; height: 300px; background: radial-gradient(circle, #4ade80, transparent); top: 50%; left: 50%; transform: translate(-50%, -50%); animation-delay: -8s; }
        @keyframes drift {
            0%   { transform: translate(0, 0) scale(1); }
            100% { transform: translate(30px, 20px) scale(1.05); }
        }
        .glass-card {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 25px 60px rgba(0,0,0,0.4), inset 0 1px 0 rgba(255,255,255,0.1);
        }
        .input-field {
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            color: #fff;
            transition: all 0.3s ease;
        }
        .input-field:focus {
            outline: none;
            border-color: #22c55e;
            background: rgba(34,197,94,0.1);
            box-shadow: 0 0 0 3px rgba(34,197,94,0.15);
        }
        .input-field::placeholder { color: rgba(255,255,255,0.35); }
        .btn-login {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: #fff;
            border: none;
            transition: all 0.3s cubic-bezier(0.2,0.8,0.2,1);
            position: relative;
            overflow: hidden;
        }
        .btn-login::after {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 60%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 12px 30px rgba(34,197,94,0.4); }
        .btn-login:hover::after { left: 150%; }
        .logo-ring {
            background: linear-gradient(135deg, #22c55e, #4ade80);
            box-shadow: 0 0 0 8px rgba(34,197,94,0.15), 0 0 40px rgba(34,197,94,0.3);
        }
        .badge-admin {
            background: rgba(34,197,94,0.15);
            border: 1px solid rgba(34,197,94,0.3);
            color: #4ade80;
        }
    </style>
</head>
<body>
    <!-- Orbs -->
    <div class="bg-orb orb-1"></div>
    <div class="bg-orb orb-2"></div>
    <div class="bg-orb orb-3"></div>

    <div class="relative z-10 w-full max-w-md px-4">
        <div class="glass-card rounded-3xl p-10">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="w-20 h-20 logo-ring rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="shield-check" class="w-10 h-10 text-white"></i>
                </div>
                <div class="inline-flex items-center gap-2 badge-admin text-xs font-bold px-3 py-1.5 rounded-full mb-3">
                    <i data-lucide="lock" class="w-3 h-3"></i>
                    AKSES ADMIN
                </div>
                <h1 class="text-2xl font-display font-bold text-white tracking-tight" style="font-family: 'Outfit', sans-serif;">
                    Panel Administrasi
                </h1>
                <p class="text-sm mt-1.5" style="color: rgba(255,255,255,0.45);">
                    UrbanFarm · Sistem Manajemen Konten
                </p>
            </div>

            <!-- Error / Success Alerts -->
            @if($errors->any())
            <div class="mb-6 flex items-start gap-3 p-4 rounded-xl" style="background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.3);">
                <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5" style="color:#f87171;"></i>
                <p class="text-sm" style="color:#fca5a5;">{{ $errors->first() }}</p>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 flex items-start gap-3 p-4 rounded-xl" style="background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.3);">
                <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5" style="color:#f87171;"></i>
                <p class="text-sm" style="color:#fca5a5;">{{ session('error') }}</p>
            </div>
            @endif

            <!-- Form -->
            <form action="{{ route('admin.authenticate') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold mb-2" style="color:rgba(255,255,255,0.6); letter-spacing:0.05em; text-transform:uppercase;">
                        Email Admin
                    </label>
                    <div class="relative">
                        <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4" style="color:rgba(255,255,255,0.35);"></i>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="admin@urban.com"
                            required
                            class="input-field w-full rounded-xl pl-11 pr-4 py-3.5 text-sm font-medium"
                        >
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-2" style="color:rgba(255,255,255,0.6); letter-spacing:0.05em; text-transform:uppercase;">
                        Password
                    </label>
                    <div class="relative">
                        <i data-lucide="key-round" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4" style="color:rgba(255,255,255,0.35);"></i>
                        <input
                            type="password"
                            name="password"
                            placeholder="••••••••"
                            required
                            class="input-field w-full rounded-xl pl-11 pr-4 py-3.5 text-sm font-medium"
                        >
                    </div>
                </div>
                <button type="submit" class="btn-login w-full py-3.5 rounded-xl text-sm font-bold tracking-wide mt-2">
                    Masuk ke Panel Admin
                </button>
            </form>

            <!-- Back link -->
            <div class="text-center mt-6">
                <a href="{{ route('dashboard') }}" class="text-xs font-medium hover:opacity-80 transition-opacity inline-flex items-center gap-1.5" style="color:rgba(255,255,255,0.4);">
                    <i data-lucide="arrow-left" class="w-3 h-3"></i>
                    Kembali ke Aplikasi
                </a>
            </div>
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
