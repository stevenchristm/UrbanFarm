<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'UrbanFarm') - Smart Cultivation</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Tailwind / Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @yield('styles')
</head>
<body class="bg-background text-foreground font-sans antialiased min-h-screen">
    @yield('background')
    <div class="flex min-h-screen w-full">
        <!-- Sidebar Desktop -->
        <aside class="w-[260px] hidden md:flex flex-col border-r border-sidebar-border/60 bg-sidebar/50 backdrop-blur-xl flex-shrink-0 sticky top-0 h-screen">
            <!-- Sidebar Header -->
            @php
                $appLogo = \App\Models\Setting::where('key', 'app_logo')->first();
            @endphp
            <div class="px-5 py-6 flex items-center gap-3">
                @if($appLogo && $appLogo->value)
                    <img src="{{ asset('storage/' . $appLogo->value) }}" class="h-10 w-10 shrink-0 rounded-xl object-cover shadow-glow border border-primary/20">
                @else
                    <div class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-primary shadow-glow">
                        <i data-lucide="sprout" class="h-6 w-6 text-primary-foreground"></i>
                    </div>
                @endif
                <div class="flex flex-col leading-tight">
                    <span class="font-display text-lg font-bold tracking-tight text-foreground">UrbanFarm</span>
                    <span class="text-xs text-muted-foreground">Smart Cultivation</span>
                </div>
            </div>

            <div class="px-5 pb-4 mb-2 border-b border-sidebar-border/60 flex items-center gap-3">
                @if(Auth::user()->logo_path)
                    <img src="{{ asset('storage/' . Auth::user()->logo_path) }}" class="h-10 w-10 shrink-0 rounded-full object-cover border-2 border-primary/20">
                @else
                    <div class="h-10 w-10 shrink-0 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-lg border-2 border-primary/20">
                        {{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}
                    </div>
                @endif
                <div class="flex flex-col leading-tight">
                    <span class="font-bold text-sm text-foreground uppercase tracking-wider">{{ Auth::user()->nama }}</span>
                </div>
            </div>

            <!-- Sidebar Content -->
            <div class="flex-1 px-3 py-2 space-y-1">
                <p class="px-4 text-xs font-semibold text-muted-foreground mb-2 uppercase tracking-wider mt-4">Main Menu</p>
                
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium transition-all {{ request()->is('dashboard') ? 'bg-primary/10 text-primary' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">
                    <i data-lucide="layout-dashboard" class="h-4 w-4"></i>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('lahan.index') }}" class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium transition-all {{ request()->is('lahan*') ? 'bg-primary/10 text-primary' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">
                    <i data-lucide="map" class="h-4 w-4"></i>
                    <span>Area Lahan</span>
                </a>

                <a href="{{ route('jadwal.index') }}" class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium transition-all {{ request()->is('jadwal*') || request()->is('semua-jadwal') ? 'bg-primary/10 text-primary' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">
                    <i data-lucide="calendar-days" class="h-4 w-4"></i>
                    <span>Alur Kerja</span>
                </a>

                <a href="{{ route('katalog.index') }}" class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium transition-all {{ request()->is('katalog*') ? 'bg-primary/10 text-primary' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">
                    <i data-lucide="book-open" class="h-4 w-4"></i>
                    <span>Edukasi Bibit</span>
                </a>

                <a href="{{ route('user.index') }}" class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium transition-all {{ request()->is('user*') ? 'bg-primary/10 text-primary' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">
                    <i data-lucide="users" class="h-4 w-4"></i>
                    <span>Komunitas</span>
                </a>

                <a href="{{ route('ai.index') }}" class="flex items-center gap-3 rounded-lg px-4 py-2.5 text-sm font-medium transition-all {{ request()->is('ai-assistant*') || request()->is('ai*') ? 'bg-primary/10 text-primary' : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground' }}">
                    <i data-lucide="sparkles" class="h-4 w-4"></i>
                    <span>Asisten AI</span>
                </a>
            </div>

            <!-- Sidebar Footer -->
            <div class="p-4 mt-auto">
                <div class="rounded-2xl border border-primary/15 bg-gradient-to-br from-primary/10 via-primary-glow/10 to-transparent p-4 mb-4 shadow-sm">
                    <div class="flex items-center gap-2 text-xs font-semibold text-primary">
                        <i data-lucide="sparkles" class="h-4 w-4"></i>
                        Pro Tips
                    </div>
                    <p class="mt-1.5 text-xs leading-relaxed text-muted-foreground">
                        {{ \App\Models\Setting::where('key','sidebar_protips')->value('value') ?? 'Tanyakan AI Agronomist untuk jadwal pemupukan yang paling optimal.' }}
                    </p>
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-4 py-2 text-sm font-medium text-destructive hover:bg-destructive/10 transition-all">
                        <i data-lucide="log-out" class="h-4 w-4"></i>
                        <span>Keluar Sesi</span>
                    </button>
                </form>
                <a href="{{ route('admin.login') }}" class="flex w-full items-center gap-3 rounded-lg px-4 py-2 text-sm font-medium text-muted-foreground/50 hover:text-muted-foreground hover:bg-sidebar-accent/50 transition-all mt-1" style="opacity:0.5;font-weight:400;">
                    <i data-lucide="shield" class="h-4 w-4"></i>
                    <span>Admin</span>
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex flex-col flex-1 bg-transparent min-w-0">
            <!-- Topbar (Mobile Header) -->
            <header class="flex md:hidden h-16 items-center border-b border-sidebar-border/60 bg-glass/80 backdrop-blur-xl px-4 sticky top-0 z-10">
                <div class="flex items-center gap-2 font-display font-semibold text-primary">
                    @if($appLogo && $appLogo->value)
                        <img src="{{ asset('storage/' . $appLogo->value) }}" class="h-6 w-6 rounded-md object-cover">
                    @else
                        <i data-lucide="sprout" class="h-5 w-5"></i> 
                    @endif
                    UrbanFarm
                </div>
            </header>

            <!-- Main Scrollable Area -->
            <div class="flex-1 w-full p-6 md:p-10 lg:p-14 overflow-y-auto overflow-x-hidden">
                <main class="w-full max-w-7xl mx-auto space-y-8 animate-slide-up">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    <!-- Initialize Lucide Icons -->
    <script>
      lucide.createIcons();
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Global Notifications Script -->
    @auth
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function initGlobalEcho() {
                if (window.Echo) {
                    const userId = {{ Auth::id() }};
                    window.Echo.private('App.Models.User.' + userId)
                        .listen('TaskNotification', (e) => {
                            showGlobalToast(e.title, e.message, e.actionUrl);
                        });
                } else {
                    setTimeout(initGlobalEcho, 100);
                }
            }
            initGlobalEcho();

            function showGlobalToast(title, message, url) {
                const toastId = 'toast-' + Math.random().toString(36).substr(2, 9);
                const actionHtml = url ? `<a href="${url}" class="mt-2 inline-block text-xs font-bold text-emerald-600 hover:text-emerald-800">Lihat Detail &rarr;</a>` : '';
                
                const toastHtml = `
                    <div id="${toastId}" class="fixed top-4 right-4 z-[100] max-w-sm w-full bg-white/95 backdrop-blur-md shadow-2xl border-2 border-emerald-500/20 rounded-2xl p-4 flex gap-4 transform transition-all duration-500 translate-x-full opacity-0">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-100 to-emerald-200 flex items-center justify-center text-emerald-700 shadow-inner">
                                <i data-lucide="bell-ring" class="w-5 h-5 animate-pulse"></i>
                            </div>
                        </div>
                        <div class="flex-1 pt-0.5">
                            <p class="text-sm font-bold text-slate-800">${title}</p>
                            <p class="text-sm text-slate-600 mt-1 leading-relaxed">${message}</p>
                            ${actionHtml}
                        </div>
                        <button onclick="document.getElementById('${toastId}').remove()" class="flex-shrink-0 text-slate-400 hover:text-rose-500 transition-colors">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>
                `;
                
                $('body').append(toastHtml);
                lucide.createIcons(); // re-init icons for the new toast
                
                // Animate in
                setTimeout(() => {
                    $(`#${toastId}`).removeClass('translate-x-full opacity-0');
                }, 100);

                // Auto remove after 7 seconds
                setTimeout(() => {
                    const toast = $(`#${toastId}`);
                    if (toast.length) {
                        toast.addClass('translate-x-full opacity-0');
                        setTimeout(() => toast.remove(), 500);
                    }
                }, 7000);
            }
        });
    </script>
    @endauth

    @yield('scripts')
</body>
</html>
