<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="theme-color" content="#2563eb">
    <title>{{ config('app.name', 'ProMatch') }}</title>

    <!-- Fonts: Plus Jakarta Sans for the new professional design -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS 4 & Alpine.js -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', 'Outfit', sans-serif; }
        [x-cloak] { display: none !important; }
        
        .glass { @apply backdrop-blur-md bg-white/10 dark:bg-black/20 border border-white/20 dark:border-white/10; }
        .glass-nav {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
        }
        
        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.15);
        }

        .safe-pb { padding-bottom: env(safe-area-inset-bottom, 1.5rem); }
        .gradient-bg {
            background: radial-gradient(circle at top right, rgba(37, 99, 235, 0.15), transparent),
            background: radial-gradient(circle at top right, rgba(22, 163, 74, 0.15), transparent),
                        radial-gradient(circle at bottom left, rgba(21, 128, 61, 0.1), transparent);
        }
    </style>
</head>
<body class="bg-[#F8FAFC] dark:bg-[#020617] text-[#1e293b] dark:text-[#f1f5f9] h-full gradient-bg overflow-x-hidden antialiased">
    <!-- Top Header -->
    <header class="fixed top-0 left-0 right-0 z-50 glass shadow-sm px-6 py-4 flex items-center justify-between backdrop-blur-xl border-b border-brand-100/20">
        <div class="flex items-center gap-2">
            <div class="w-10 h-10 bg-gradient-to-tr from-brand-600 to-brand-400 rounded-xl flex items-center justify-center shadow-lg shadow-brand-600/20">
                <span class="text-white font-bold text-xl uppercase italic">P</span>
            </div>
            <h1 class="text-xl font-extrabold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-brand-700 to-brand-500">ProMatch</h1>
        </div>
        @if(session()->has('user'))
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-[10px] font-bold text-slate-400 uppercase leading-none">Connecté en tant que</p>
                    <p class="text-xs font-bold text-brand-600">{{ session('user')['first_name'] ?? 'Utilisateur' }}</p>
                </div>
                <a href="{{ route('logout') }}" class="p-2 rounded-full bg-red-50 text-red-500 hover:bg-red-100 transition-colors" title="Déconnexion">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-6 0v-1m6-10V5a3 3 0 01-6 0v1m6 0H9" />
                    </svg>
                </a>
            </div>
        @else
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-brand-50 text-brand-600 text-xs font-bold rounded-full hover:bg-brand-100 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-6 0v-1m6-10V5a3 3 0 01-6 0v1m6 0H9" />
                </svg>
                Se connecter
            </a>
        @endif
    </header>

    <!-- Main Content Area -->
    <main class="pt-24 pb-36 px-5 min-h-full">
        @yield('content')
    </main>

    <!-- Bottom Floating Navigation -->
    <nav x-data="{ currentPath: window.location.pathname }" 
         class="fixed bottom-6 left-5 right-5 z-50 bg-slate-900/95 backdrop-blur-2xl rounded-2xl border border-white/10 px-4 py-3 shadow-2xl safe-pb shadow-slate-900/40 translate-z-0">
        <div class="flex items-center justify-between max-w-lg mx-auto relative h-12">
            
            <!-- Accueil -->
            <a href="/" 
               :class="currentPath === '/' ? 'text-white' : 'text-slate-400 hover:text-slate-200'"
               class="relative z-10 flex flex-col items-center justify-center flex-1 h-full transition-all duration-300">
                <div x-show="currentPath === '/'" x-transition class="absolute inset-0 bg-brand-600 rounded-xl -z-10 shadow-lg shadow-brand-600/30"></div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mb-1" viewBox="0 0 24 24" :fill="currentPath === '/' ? 'currentColor' : 'none'" :stroke="currentPath === '/' ? 'none' : 'currentColor'" stroke-width="2">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                </svg>
                <span class="text-[10px] font-bold uppercase tracking-wider">Accueil</span>
            </a>

            <!-- Découvrir -->
            <a href="/#terrains" 
               :class="currentPath.includes('explore') ? 'text-white' : 'text-slate-400 hover:text-slate-200'"
               class="relative z-10 flex flex-col items-center justify-center flex-1 h-full transition-all duration-300">
                <div x-show="currentPath.includes('explore')" x-transition class="absolute inset-0 bg-brand-600 rounded-xl -z-10"></div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <span class="text-[10px] font-bold uppercase tracking-wider">Découvrir</span>
            </a>

            <!-- Réservations -->
            <a href="/booking" 
               :class="currentPath.includes('booking') ? 'text-white' : 'text-slate-400 hover:text-slate-200'"
               class="relative z-10 flex flex-col items-center justify-center flex-1 h-full transition-all duration-300">
                <div x-show="currentPath.includes('booking')" x-transition class="absolute inset-0 bg-brand-600 rounded-xl -z-10 shadow-lg shadow-brand-600/30"></div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-[10px] font-bold uppercase tracking-wider">Réserver</span>
            </a>

            <!-- Contact -->
            <a href="/contact" 
               :class="currentPath.includes('contact') ? 'text-white' : 'text-slate-400 hover:text-slate-200'"
               class="relative z-10 flex flex-col items-center justify-center flex-1 h-full transition-all duration-300">
                <div x-show="currentPath.includes('contact')" x-transition class="absolute inset-0 bg-brand-600 rounded-xl -z-10 shadow-lg shadow-brand-600/30"></div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span class="text-[10px] font-bold uppercase tracking-wider">Contact</span>
            </a>
        </div>
    </nav>
</body>
</html>
