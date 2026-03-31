<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="theme-color" content="#2563eb">
    <title>{{ config('app.name', 'ProMatch') }}</title>

    <!-- Fonts: Outfit for a premium, modern feel -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS 4 & Alpine.js -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Outfit', sans-serif; }
        [x-cloak] { display: none !important; }
        .glass { @apply backdrop-blur-md bg-white/10 dark:bg-black/20 border border-white/20 dark:border-white/10; }
        .safe-pb { padding-bottom: env(safe-area-inset-bottom, 1.5rem); }
        .gradient-bg {
            background: radial-gradient(circle at top right, rgba(37, 99, 235, 0.15), transparent),
                        radial-gradient(circle at bottom left, rgba(124, 58, 237, 0.1), transparent);
        }
    </style>
</head>
<body class="bg-[#F8FAFC] dark:bg-[#020617] text-[#1e293b] dark:text-[#f1f5f9] h-full gradient-bg overflow-x-hidden antialiased">
    <!-- Top Header -->
    <header class="fixed top-0 left-0 right-0 z-50 glass shadow-sm px-5 py-4 flex items-center justify-between backdrop-blur-xl">
        <div class="flex items-center gap-2">
            <div class="w-10 h-10 bg-gradient-to-tr from-blue-600 to-violet-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                <span class="text-white font-bold text-xl uppercase italic">P</span>
            </div>
            <h1 class="text-xl font-bold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-violet-600">ProMatch</h1>
        </div>
        <button class="relative p-2 rounded-full hover:bg-black/5 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-red-500 rounded-full border-2 border-white dark:border-slate-900"></span>
        </button>
    </header>

    <!-- Main Content Area -->
    <main class="pt-24 pb-32 px-5 min-h-full">
        @yield('content')
    </main>

    <!-- Bottom Navigation Bar -->
    <nav class="fixed bottom-0 left-0 right-0 z-50 glass border-t border-white/20 dark:border-white/10 px-6 py-4 safe-pb backdrop-blur-2xl">
        <div class="flex items-center justify-between max-w-lg mx-auto">
            <a href="#" class="flex flex-col items-center gap-1 text-blue-600 transition-transform active:scale-90">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                </svg>
                <span class="text-xs font-semibold">Home</span>
            </a>
            <a href="#" class="flex flex-col items-center gap-1 text-slate-400 hover:text-blue-500 transition-transform active:scale-90">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <span class="text-xs font-semibold">Explore</span>
            </a>
            <a href="#" class="flex flex-col items-center gap-1 text-slate-400 hover:text-blue-500 transition-transform active:scale-90">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-xs font-semibold">Bookings</span>
            </a>
            <a href="#" class="flex flex-col items-center gap-1 text-slate-400 hover:text-blue-500 transition-transform active:scale-90">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="text-xs font-semibold">Profile</span>
            </a>
        </div>
    </nav>
</body>
</html>
