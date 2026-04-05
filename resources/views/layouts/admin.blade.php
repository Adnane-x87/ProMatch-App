<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'ProMatch — Admin Dashboard')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'] 
                    },
                    colors: {
                        brand: {
                            50: '#f0f9f1',
                            100: '#dcf1df',
                            200: '#bbe2c3',
                            300: '#8dca9e',
                            400: '#5eac72',
                            500: '#4da565',
                            600: '#3d8a54',
                            700: '#327145',
                        }
                    }
                },
            },
        };
    </script>
    <style>
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>

<body class="bg-slate-50 text-slate-900 font-sans antialiased">

    <div class="min-h-screen flex">

        <!-- Sidebar -->
        <aside class="hidden lg:flex w-64 flex-col fixed inset-y-0 bg-white border-r border-slate-200 shadow-sm">
            
            <!-- Logo -->
            <div class="h-20 relative flex items-center justify-center border-b border-slate-100 bg-white z-10">
                <a href="{{ url('/admin/dashboard') }}" class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-brand-600 text-white font-bold flex items-center justify-center text-lg italic">P</span>
                    <span class="text-xl font-extrabold tracking-tight">Pro<span class="text-brand-600">Match</span></span>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Principale</p>
                
                <a href="{{ url('/admin/dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-semibold rounded-lg @if(request()->is('admin/dashboard*')) bg-brand-50 text-brand-700 @else text-slate-600 hover:bg-slate-50 hover:text-slate-900 @endif">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Tableau de bord
                </a>

                <a href="{{ url('/admin/fields') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-semibold rounded-lg @if(request()->is('admin/fields*')) bg-brand-50 text-brand-700 @else text-slate-600 hover:bg-slate-50 hover:text-slate-900 @endif">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Gérer les terrains
                </a>

                <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-8 mb-4">Opérations</p>

                <a href="{{ url('/admin/reservations') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg @if(request()->is('admin/reservations*')) bg-brand-50 text-brand-700 @else text-slate-600 hover:bg-slate-50 hover:text-slate-900 @endif">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Réservations
                </a>
                <a href="{{ url('/admin/validations') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg @if(request()->is('admin/validations*')) bg-brand-50 text-brand-700 @else text-slate-600 hover:bg-slate-50 hover:text-slate-900 @endif">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Validations CNI
                    <span class="ml-auto w-5 h-5 flex items-center justify-center rounded-full bg-rose-100 text-rose-600 text-xs font-bold">{{ $pendingValidationsCount ?? 0 }}</span>
                </a>
                <a href="{{ url('/admin/clients') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg @if(request()->is('admin/clients*')) bg-brand-50 text-brand-700 @else text-slate-600 hover:bg-slate-50 hover:text-slate-900 @endif">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Clients
                </a>
                
                <a href="{{ url('/') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:bg-slate-50 hover:text-slate-900 border-t mt-4 pt-4 border-slate-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Accueil Application
                </a>
            </nav>

            <!-- User -->
            <div class="p-4 border-t border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-brand-100 flex items-center justify-center text-brand-700 font-bold">
                        {{ strtoupper(substr(session('user')['name'] ?? 'AD', 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-900 truncate">{{ session('user')['name'] ?? 'Administrateur' }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ session('user')['email'] ?? 'admin@promatch.ma' }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-64">
            
            <!-- Header -->
            <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-slate-200">
                <div class="px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <button class="lg:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <div>
                            <h1 class="text-xl font-bold text-slate-900">@yield('page-title')</h1>
                            <p class="text-sm text-slate-500">@yield('page-subtitle', 'ProMatch GESTION')</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="hidden md:block text-xs font-medium text-slate-400">Dernière connexion: {{ now()->format('H:i') }}</span>
                        <a href="{{ url('/booking') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition-all active:scale-95 shadow-lg shadow-slate-900/10">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Nouvelle réservation
                        </a>
                    </div>
                </div>
            </header>

            <div class="p-6 max-w-7xl mx-auto space-y-6">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
