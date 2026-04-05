<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ProMatch — Se connecter</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
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
                            900: '#1a4a2b',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                    },
                },
            },
        };
    </script>
</head>
    <body class="antialiased bg-slate-50 text-slate-900 font-sans min-h-screen flex items-center justify-center p-4 relative">
    
        <!-- Background Decoration -->
        <div class="absolute inset-0 z-0 overflow-hidden">
            <div class="absolute top-0 right-0 -mt-20 -mr-20 w-96 h-96 bg-brand-600/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-96 h-96 bg-emerald-600/10 rounded-full blur-3xl"></div>
        </div>
    
        <!-- Login Card -->
        <div
            class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 w-full max-w-md p-8 relative z-10 border border-slate-100">
    
            <!-- Header -->
            <div class="text-center mb-8">
                <a href="/" class="inline-block mb-6">
                    <img src="/images/logo.png" alt="ProMatch Logo" class="h-20 w-auto" 
                         onerror="this.src='https://ui-avatars.com/api/?name=Pro+Match&background=1a4a2b&color=fff&size=256'">
                </a>
                <h1 class="text-2xl font-bold text-slate-900">Bon retour !</h1>
                <p class="text-slate-500 mt-2">Connectez-vous pour gérer vos réservations</p>
            </div>
    
            <!-- Laravel Session Error -->
            @if(session('error'))
                <div class="mb-6 p-5 bg-[#FEF2F2] border-l-[4px] border-red-600 rounded-2xl shadow-sm animate-in fade-in slide-in-from-top-1 duration-300">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <p class="text-sm text-red-700 font-semibold">{{ session('error') }}</p>
                    </div>
                </div>
            @endif
    
            <!-- Validation Errors -->
            @if($errors->any())
                <div class="mb-6 p-4 bg-[#FEF2F2] border-l-[4px] border-[#FCA5A5] rounded-2xl shadow-sm animate-in fade-in slide-in-from-top-1 duration-300">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 mt-0.5">
                            <svg class="h-5 w-5 text-[#EF4444]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-[#991B1B] font-bold mb-1.5 leading-tight">Veuillez corriger les erreurs suivantes :</p>
                            <ul class="space-y-1">
                                @foreach($errors->all() as $error)
                                    <li class="flex items-center gap-2 text-sm text-[#B91C1C] font-medium">
                                        <span class="w-1 h-1 rounded-full bg-[#EF4444]"></span>
                                        {{ $error }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
    
            <!-- Form -->
            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-2 px-1">Email</label>
                    <div class="relative group">
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="w-full rounded-xl border {{ $errors->has('email') ? 'border-[#FCA5A5] ring-2 ring-[#FEF2F2] bg-[#FEF2F2]/10' : 'border-slate-300 group-hover:border-slate-400' }} pl-11 pr-4 py-3 text-sm focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 outline-none transition-all shadow-sm"
                            placeholder="exemple@email.com">
                        <div class="absolute left-4 top-3.5 {{ $errors->has('email') ? 'text-[#EF4444]' : 'text-slate-400 group-hover:text-brand-500' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        @error('email')
                            <p class="mt-2 text-xs font-semibold text-[#EF4444] flex items-center gap-1.5 px-0.5 animate-in fade-in slide-in-from-top-1 duration-200">
                                <span class="w-1 h-1 rounded-full bg-[#EF4444]"></span>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
    
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-2 px-1">Mot de passe</label>
                    <div class="relative group">
                        <input type="password" name="password" id="password" required
                            class="w-full rounded-xl border {{ $errors->has('password') ? 'border-[#FCA5A5] ring-2 ring-[#FEF2F2] bg-[#FEF2F2]/10' : 'border-slate-300 group-hover:border-slate-400' }} pl-11 pr-4 py-3 text-sm focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 outline-none transition-all shadow-sm"
                            placeholder="••••••••">
                        <div class="absolute left-4 top-3.5 {{ $errors->has('password') ? 'text-[#EF4444]' : 'text-slate-400 group-hover:text-brand-500' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        @error('password')
                            <p class="mt-2 text-xs font-semibold text-[#EF4444] flex items-center gap-1.5 px-0.5 animate-in fade-in slide-in-from-top-1 duration-200">
                                <span class="w-1 h-1 rounded-full bg-[#EF4444]"></span>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
    
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                        <span class="text-slate-600">Se souvenir de moi</span>
                    </label>
                    <a href="#" class="font-medium text-brand-600 hover:text-brand-700">Mot de passe oublié ?</a>
                </div>
    
                <button type="submit"
                    class="w-full rounded-xl bg-brand-500 px-6 py-3 text-sm font-bold text-white hover:bg-brand-600 transition-colors shadow-lg shadow-brand-500/20 flex items-center justify-center gap-2">
                    <span>Se connecter</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </form>
    
            <!-- Divider -->
            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-slate-500">Ou continuer avec</span>
                </div>
            </div>
    
            <!-- Social Login -->
            <div class="grid grid-cols-1 gap-3">
                <button
                    class="flex items-center justify-center gap-2 px-4 py-2.5 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path
                            d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                            fill="#4285F4" />
                        <path
                            d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                            fill="#34A853" />
                        <path
                            d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                            fill="#FBBC05" />
                        <path
                            d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                            fill="#EA4335" />
                    </svg>
                    <span class="text-sm font-medium text-slate-700">Google</span>
                </button>
            </div>
    
            <!-- Footer -->
            <p class="text-center text-sm text-slate-500 mt-8">
                Pas encore de compte ?
                <a href="/register" class="font-bold text-brand-600 hover:text-brand-700">Créer un compte</a>
            </p>
    </div>
    
    </body>
</html>
