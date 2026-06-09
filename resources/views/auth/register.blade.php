@extends('layouts.mobile')

@section('content')
<style>
    /* Hide bottom nav and header for a clean registration experience */
    nav, header { display: none !important; }
    main { padding: 0 !important; }
    
    .register-container {
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

    /* Password strength bar animation */
    .strength-bar {
        transition: width 0.4s ease, background-color 0.4s ease;
    }
</style>

<div class="register-container px-6 py-12" x-data="registerApp()">
    <!-- Logo & Brand -->
    <div class="mb-6 text-center">
        <img src="/images/logo.png" alt="ProMatch Logo" class="h-16 w-auto mx-auto mb-4 logo-animation dark:brightness-150">
        <p class="text-slate-500 dark:text-slate-400 text-xs font-medium tracking-wide">Rejoignez la plateforme ProMatch</p>
    </div>

    <!-- Register Card -->
    <div class="w-full max-w-sm glass rounded-[2.5rem] p-8 shadow-2xl">
        
        <!-- Status Messages -->
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800/50 rounded-2xl flex items-center gap-3 animate-pulse">
                <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <p class="text-xs text-red-600 dark:text-red-400 font-bold">{{ session('error') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800/50 rounded-2xl">
                <ul class="list-disc list-inside text-xs text-red-600 dark:text-red-400 font-bold">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <template x-if="error">
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800/50 rounded-2xl">
                <p class="text-xs text-red-600 dark:text-red-400 font-bold" x-text="error"></p>
            </div>
        </template>

        <form method="POST" action="{{ route('register') }}" class="space-y-4" @submit.prevent="submit($event)">
            @csrf

            <!-- Names Grid -->
            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="text-[9px] font-black uppercase tracking-[0.15em] text-slate-400 ml-1">Prénom</label>
                    <input type="text" name="first_name" placeholder="Votre prénom" autocomplete="given-name" required value="{{ old('first_name') }}" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-700/50 rounded-2xl text-xs focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all font-medium text-slate-900 dark:text-white">
                </div>
                <div class="space-y-1">
                    <label class="text-[9px] font-black uppercase tracking-[0.15em] text-slate-400 ml-1">Nom</label>
                    <input type="text" name="last_name" placeholder="Votre nom" autocomplete="family-name" required value="{{ old('last_name') }}" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-700/50 rounded-2xl text-xs focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all font-medium text-slate-900 dark:text-white">
                </div>
            </div>

            <!-- Email Input -->
            <div class="space-y-1">
                <label class="text-[9px] font-black uppercase tracking-[0.15em] text-slate-400 ml-1">Adresse Email</label>
                <input type="email" name="email" placeholder="Votre email" autocomplete="email" required value="{{ old('email') }}" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-700/50 rounded-2xl text-xs focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all font-medium text-slate-900 dark:text-white">
            </div>

            <!-- Phone Input -->
            <div class="space-y-1">
                <label class="text-[9px] font-black uppercase tracking-[0.15em] text-slate-400 ml-1">Numéro de Téléphone</label>
                <input type="tel" name="phone" placeholder="Votre numéro" autocomplete="tel" required value="{{ old('phone') }}" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-700/50 rounded-2xl text-xs focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all font-medium text-slate-900 dark:text-white">
            </div>

            <!-- Role Selection (Owner / Tenant / Employee) -->
            <div class="space-y-1">
                <label class="text-[9px] font-black uppercase tracking-[0.15em] text-slate-400 ml-1 block mb-1">Je suis un...</label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="tenant" x-model="role" class="sr-only" @checked(old('type', 'tenant') === 'tenant')>
                        <div :class="role === 'tenant' ? 'border-brand-500 bg-brand-50 dark:bg-brand-950/20 text-brand-600' : 'border-slate-100 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-800/20 text-slate-500'" 
                             class="py-2.5 text-center border-2 rounded-xl text-xs font-bold transition-all hover:scale-[1.02]">
                            Joueur
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="owner" x-model="role" class="sr-only" @checked(old('type', 'tenant') === 'owner')>
                        <div :class="role === 'owner' ? 'border-brand-500 bg-brand-50 dark:bg-brand-950/20 text-brand-600' : 'border-slate-100 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-800/20 text-slate-500'" 
                             class="py-2.5 text-center border-2 rounded-xl text-xs font-bold transition-all hover:scale-[1.02]">
                            Gérant
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="employee" x-model="role" class="sr-only" @checked(old('type', 'tenant') === 'employee')>
                        <div :class="role === 'employee' ? 'border-brand-500 bg-brand-50 dark:bg-brand-950/20 text-brand-600' : 'border-slate-100 dark:border-slate-700/50 bg-slate-50/50 dark:bg-slate-800/20 text-slate-500'" 
                             class="py-2.5 text-center border-2 rounded-xl text-xs font-bold transition-all hover:scale-[1.02]">
                            Employé
                        </div>
                    </label>
                </div>
            </div>

            <!-- Password Input -->
            <div class="space-y-1">
                <label class="text-[9px] font-black uppercase tracking-[0.15em] text-slate-400 ml-1">Mot de passe</label>
                <input type="password" name="password" x-model="password" @input="checkStrength" placeholder="Créez un mot de passe" autocomplete="new-password" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-700/50 rounded-2xl text-xs focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all font-medium text-slate-900 dark:text-white">
                
                <!-- Password Strength indicators -->
                <div class="flex gap-1 mt-1.5" x-show="password.length > 0">
                    <template x-for="i in 4">
                        <div class="h-1 flex-1 rounded-full bg-slate-200 dark:bg-slate-800 overflow-hidden">
                            <div :class="i <= strengthScore ? strengthColor : 'bg-transparent'" class="h-full strength-bar rounded-full" style="width: 100%"></div>
                        </div>
                    </template>
                </div>
                <p class="text-[10px] font-bold mt-1" :class="strengthTextClass" x-show="password.length > 0" x-text="strengthText"></p>
            </div>

            <!-- Confirm Password Input -->
            <div class="space-y-1">
                <label class="text-[9px] font-black uppercase tracking-[0.15em] text-slate-400 ml-1">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" x-model="passwordConfirmation" placeholder="Confirmez le mot de passe" autocomplete="new-password" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-700/50 rounded-2xl text-xs focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all font-medium text-slate-900 dark:text-white">
                <p class="text-[10px] text-red-500 font-bold mt-1" x-show="password.length > 0 && passwordConfirmation.length > 0 && password !== passwordConfirmation">Les mots de passe ne correspondent pas.</p>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button type="submit" :disabled="submitting || (password.length > 0 && passwordConfirmation.length > 0 && password !== passwordConfirmation)" 
                        class="w-full py-4 bg-gradient-to-r from-brand-600 to-brand-500 text-white rounded-2xl text-sm font-bold shadow-xl shadow-brand-600/30 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-2 group cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                    <span>Créer le compte</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>
        </form>
        
        <!-- Login Link -->
        <p class="mt-6 text-center text-xs text-slate-500 dark:text-slate-400">
            Déjà un compte ? 
            <a href="{{ route('login') }}" class="font-bold text-brand-600 hover:text-brand-700 underline">Se connecter</a>
        </p>
    </div>
</div>

<script>
    function registerApp() {
        return {
            role: @js(old('type', 'tenant')),
            password: '',
            passwordConfirmation: '',
            error: '',
            submitting: false,
            strengthScore: 0,
            strengthColor: 'bg-red-400',
            strengthText: 'Très faible',
            strengthTextClass: 'text-red-500',
            
            checkStrength() {
                let score = 0;
                const val = this.password;
                if (val.length >= 6) score++;
                if (/[A-Z]/.test(val)) score++;
                if (/[0-9]/.test(val)) score++;
                if (/[^A-Za-z0-9]/.test(val)) score++;
                
                this.strengthScore = score;
                
                const colors = ['bg-red-400', 'bg-amber-400', 'bg-brand-400', 'bg-emerald-500'];
                const labels = ['Très faible', 'Faible', 'Moyen', 'Fort'];
                const textClasses = ['text-red-500', 'text-amber-500', 'text-brand-500', 'text-emerald-500'];
                
                this.strengthColor = colors[score - 1] || 'bg-red-400';
                this.strengthText = labels[score - 1] || 'Très faible';
                this.strengthTextClass = textClasses[score - 1] || 'text-red-500';
            },

            async submit(event) {
                this.error = '';

                if (this.password !== this.passwordConfirmation) {
                    this.error = 'Les mots de passe ne correspondent pas.';
                    return;
                }

                this.submitting = true;

                try {
                    const form = event.target;
                    const value = (name) => form.elements[name]?.value || '';

                    const response = await fetch(form.action, {
                        method: 'POST',
                        credentials: 'same-origin',
                        body: JSON.stringify({
                            _token: value('_token'),
                            first_name: value('first_name').trim(),
                            last_name: value('last_name').trim(),
                            email: value('email').trim(),
                            phone: value('phone').trim(),
                            type: value('type') || this.role,
                            role: value('type') || this.role,
                            password: value('password'),
                            password_confirmation: value('password_confirmation'),
                        }),
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    const data = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        const errors = data.errors ? Object.values(data.errors).flat() : [];
                        throw new Error(errors[0] || data.message || 'Création du compte impossible.');
                    }

                    window.location.href = data.redirect || '/';
                } catch (error) {
                    this.error = error.message;
                } finally {
                    this.submitting = false;
                }
            }
        };
    }
</script>
@endsection

