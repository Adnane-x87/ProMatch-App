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

<div class="login-container px-6 py-12" x-data="loginForm(@js(old('email', '')))">
    <!-- Logo & Brand -->
    <div class="mb-10 text-center">
        <img src="/images/logo.png" alt="ProMatch Logo" class="h-16 w-auto mx-auto mb-4 logo-animation dark:brightness-150">
    </div>

    <!-- Login Card -->
    <div class="w-full max-w-sm glass rounded-[2.5rem] p-8 shadow-2xl">
        
        <!-- Status Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/50 rounded-2xl flex items-center gap-3">
                <svg class="h-5 w-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <p class="text-xs text-emerald-600 dark:text-emerald-400 font-bold">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800/50 rounded-2xl flex items-center gap-3 animate-pulse">
                <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <p class="text-xs text-red-600 dark:text-red-400 font-bold">{{ session('error') }}</p>
            </div>
        @endif

        @if($errors->any() && !($errors->has('email') || $errors->has('password')))
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800/50 rounded-2xl">
                <ul class="space-y-1 text-xs text-red-600 dark:text-red-400 font-bold">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <template x-if="submitError">
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800/50 rounded-2xl">
                <p class="text-xs text-red-600 dark:text-red-400 font-bold" x-text="submitError"></p>
            </div>
        </template>

        <form method="POST" action="{{ route('login') }}" class="space-y-6" novalidate @submit.prevent="submit($event)">
            @csrf

            <!-- Email Input -->
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Email professionnel</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-500 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                    </div>
                    <input type="email" name="email" x-model="email" placeholder="Entrez votre email" autocomplete="email" autofocus aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}" class="w-full pl-11 pr-4 py-4 bg-slate-50 dark:bg-slate-800/40 border {{ $errors->has('email') ? 'border-red-300 focus:border-red-500 focus:ring-red-500/20' : 'border-slate-100 dark:border-slate-700/50 focus:border-brand-500 focus:ring-brand-500/20' }} rounded-2xl text-sm focus:outline-none focus:ring-2 transition-all font-medium text-slate-900 dark:text-white">
                </div>
                <template x-if="submitted && !email.trim()">
                    <p class="ml-1 text-xs font-bold text-red-600 dark:text-red-400">Entrez votre email.</p>
                </template>
                @error('email')
                    <p class="ml-1 text-xs font-bold text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Input -->
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Mot de passe</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-brand-500 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <input type="password" name="password" x-model="password" placeholder="Entrez votre mot de passe" autocomplete="current-password" aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}" class="w-full pl-11 pr-4 py-4 bg-slate-50 dark:bg-slate-800/40 border {{ $errors->has('password') ? 'border-red-300 focus:border-red-500 focus:ring-red-500/20' : 'border-slate-100 dark:border-slate-700/50 focus:border-brand-500 focus:ring-brand-500/20' }} rounded-2xl text-sm focus:outline-none focus:ring-2 transition-all font-medium text-slate-900 dark:text-white">
                </div>
                <template x-if="submitted && !password">
                    <p class="ml-1 text-xs font-bold text-red-600 dark:text-red-400">Entrez votre mot de passe.</p>
                </template>
                @error('password')
                    <p class="ml-1 text-xs font-bold text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons Wrapper -->
            <div class="pt-4 space-y-4">
                <!-- Login Button -->
                <button type="submit" :disabled="submitting" class="w-full py-4 bg-gradient-to-r from-brand-600 to-brand-500 text-white rounded-2xl text-sm font-bold shadow-xl shadow-brand-600/30 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-2 group cursor-pointer disabled:opacity-55 disabled:shadow-none disabled:hover:scale-100 disabled:cursor-not-allowed">
                    <span x-text="submitting ? 'Connexion...' : 'Se connecter'">Se connecter</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>
        </form>

        <!-- Register Link -->
        <p class="mt-6 text-center text-xs text-slate-500 dark:text-slate-400">
            Pas encore de compte ? 
            <a href="{{ route('register') }}" class="font-bold text-brand-600 hover:text-brand-700 underline">Créer un compte</a>
        </p>
    </div>
    
    <!-- Footer info -->
    <p class="mt-12 text-center text-slate-400 text-xs font-medium">
        &copy; 2024 ProMatch. Plateforme de gestion sportive.
    </p>
</div>

<script>
    function loginForm(initialEmail) {
        return {
            email: initialEmail || '',
            password: '',
            submitted: false,
            submitting: false,
            submitError: '',

            async submit(event) {
                this.submitted = true;
                this.submitError = '';

                const email = this.email.trim();
                const password = this.password;

                console.log('LOGIN DATA BEFORE SEND', { email, password });

                if (!email || !password) {
                    return;
                }

                this.submitting = true;

                try {
                    const response = await fetch(event.target.action, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': event.target.elements._token.value,
                        },
                        body: JSON.stringify({
                            email: email,
                            password: password,
                        }),
                    });

                    const data = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        const errors = data.errors ? Object.values(data.errors).flat() : [];
                        this.submitError = errors[0] || data.message || 'Connexion impossible.';
                        return;
                    }

                    window.location.href = data.redirect || '/';
                } catch (error) {
                    this.submitError = 'API indisponible. Vérifiez que le serveur ProMatch est lancé.';
                } finally {
                    this.submitting = false;
                }
            },
        };
    }
</script>

@endsection

