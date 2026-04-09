@extends('layouts.mobile')

@section('content')
<style>
    /* Hide bottom nav and header for a clean login experience */
    nav, header { display: none !important; }
    main { padding: 0 !important; }
    
    .login-container {
        min-height: 100vh;
        background: radial-gradient(circle at top right, rgba(37, 99, 235, 0.1), transparent),
                    radial-gradient(circle at bottom left, rgba(22, 163, 74, 0.1), transparent);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .glass {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .dark .glass {
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    @keyframes float {
        0% { transform: translateY(0px) rotate(12deg); }
        50% { transform: translateY(-10px) rotate(15deg); }
        100% { transform: translateY(0px) rotate(12deg); }
    }

    .logo-animation {
        animation: float 4s ease-in-out infinite;
    }
</style>

<div class="login-container px-6 py-12" x-data="{ email: '', password: '' }">
    <!-- Logo & Brand -->
    <div class="mb-10 text-center">
        <div class="w-20 h-20 bg-gradient-to-tr from-brand-600 to-brand-400 rounded-3xl flex items-center justify-center shadow-2xl shadow-brand-600/30 mx-auto mb-6 transform rotate-12 logo-animation">
            <span class="text-white font-black text-4xl uppercase italic">P</span>
        </div>
        <h1 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white mb-2">ProMatch</h1>
        <p class="text-slate-500 dark:text-slate-400 font-medium tracking-wide">Gestion de l'Administration</p>
    </div>

    <!-- Login Card -->
    <div class="w-full max-w-sm glass rounded-[2.5rem] p-8 shadow-2xl">
        
        <!-- Status Messages -->
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800/50 rounded-2xl flex items-center gap-3 animate-pulse">
                <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <p class="text-xs text-red-600 dark:text-red-400 font-bold">{{ session('error') }}</p>
            </div>
        @endif

        <div class="space-y-6">
            <!-- Email Input -->
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Email professionnel</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-500 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                    </div>
                    <input type="email" x-model="email" placeholder="admin@promatch.ma" required autofocus class="w-full pl-11 pr-4 py-4 bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-700/50 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all font-medium text-slate-900 dark:text-white">
                </div>
            </div>

            <!-- Password Input -->
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Mot de passe</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-500 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <input type="password" x-model="password" placeholder="••••••••" required class="w-full pl-11 pr-4 py-4 bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-700/50 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all font-medium text-slate-900 dark:text-white">
                </div>
            </div>

            <!-- Buttons Wrapper -->
            <div class="pt-4 space-y-4">
                <!-- Login Button (Bypass as requested: works even if fields are empty or invalid) -->
                <button @click="window.location.href = '{{ route('login.bypass') }}'" class="w-full py-4 bg-gradient-to-r from-brand-600 to-brand-500 text-white rounded-2xl text-sm font-bold shadow-xl shadow-brand-600/30 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-2 group">
                    <span>Se connecter</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>

                <!-- Separator -->
                <div class="relative flex items-center py-2">
                    <div class="flex-grow border-t border-slate-200 dark:border-slate-700"></div>
                    <span class="flex-shrink mx-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Accès Direct</span>
                    <div class="flex-grow border-t border-slate-200 dark:border-slate-700"></div>
                </div>

                <!-- Secondary Accès Rapide button -->
                <a href="{{ route('login.bypass') }}" class="w-full py-4 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 text-slate-700 dark:text-slate-200 rounded-2xl text-sm font-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition-all flex items-center justify-center gap-2 shadow-sm">
                    <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Accès Rapide Admin
                </a>
            </div>
        </div>
    </div>
    
    <!-- Footer info -->
    <p class="mt-12 text-center text-slate-400 text-xs font-medium">
        &copy; 2024 ProMatch. Plateforme de gestion sportive.
    </p>
</div>
@endsection
