@extends('layouts.mobile')

@section('content')
<style>
    .hero-bg {
        background-image: linear-gradient(rgba(15, 23, 42, 0.6), rgba(15, 23, 42, 0.6)), url('/images/hero-bg.png');
        background-size: cover;
        background-position: center;
    }
</style>

<div x-data="fieldApp()" x-init="fetchFields()">
    <!-- HERO SECTION -->
    <section class="relative -mx-5 -mt-24 min-h-[400px] hero-bg flex items-center mb-12">
        <div class="relative z-10 px-6 w-full pt-12">
            <div class="max-w-2xl py-12">
                <h1 class="text-4xl font-extrabold text-white tracking-tight leading-[1.1] mb-3">
                    Votre terrain<br>
                    <span class="text-brand-400">vous attend.</span>
                </h1>
                <p class="text-sm text-slate-200 leading-relaxed max-w-sm mb-6">
                    4 terrains de football professionnels avec gazon synthétique et vestiaires premium. Réservation simplifiée.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="/booking" class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-brand-500 transition-colors shadow-lg shadow-brand-600/25">
                        Réserver
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Terrains Section -->
    <section id="terrains" class="mb-20">
        <div class="text-center mb-10">
            <span class="inline-block py-1 px-3 rounded-full bg-brand-50 text-brand-600 text-[10px] font-bold uppercase tracking-wide mb-2">
                Nos Installations
            </span>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                Terrains disponibles
            </h2>
        </div>

        <!-- Search Bar -->
        <div class="mb-10 px-1">
            <div class="relative group">
                <input type="text" 
                       x-model="q" 
                       @input.debounce.500ms="fetchFields()"
                       placeholder="Rechercher un terrain (ex: Casablanca)..." 
                       class="w-full bg-white/80 backdrop-blur-md border border-slate-200 rounded-2xl py-4 pl-12 pr-4 text-sm focus:ring-4 focus:ring-brand-100 focus:border-brand-500 outline-none transition-all shadow-sm group-hover:shadow-md">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" :class="loading ? 'animate-pulse text-brand-500' : ''">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <template x-if="loading">
            <div class="flex justify-center items-center h-40">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-brand-600"></div>
                <p class="ml-3 text-brand-600 font-bold">Chargement...</p>
            </div>
        </template>

        <!-- Error State -->
        <template x-if="error">
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md">
                <p class="text-sm text-red-700 font-bold" x-text="errorTitle"></p>
                <p class="text-xs text-red-600 mt-1" x-text="error"></p>
                <button @click="fetchFields()" class="mt-3 text-xs bg-red-100 text-red-700 font-bold py-1.5 px-4 rounded w-full">
                    Réessayer
                </button>
            </div>
        </template>

        <!-- Dynamic Grid -->
        <div class="grid gap-6 grid-cols-1" x-show="!loading && !error">
            <template x-for="field in fields" :key="field.id">
                <article class="group bg-white rounded-2xl border border-slate-200 overflow-hidden hover-lift shadow-sm">
                    <div class="h-36 overflow-hidden relative">
                        <span class="absolute top-3 right-3 px-2.5 py-1 rounded-full bg-emerald-50/90 backdrop-blur-sm text-emerald-600 text-[10px] font-bold border border-emerald-100 z-10">
                            Disponible
                        </span>
                        <img :src="'/images/field_' + field.id + '.jpg'" 
                             :alt="field.name" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                             x-on:error="$event.target.src = field.image_url || '/images/hero-bg.png'">
                    </div>
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-bold text-slate-900" x-text="field.name"></h3>
                            
                        </div>
                        <p class="text-sm text-slate-500 mb-4 line-clamp-2" x-text="field.description"></p>
                        <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">À partir de</span>
                                <span class="text-xl font-bold text-slate-900"><span x-text="field.price_per_hour"></span> Dh<span class="text-xs text-slate-400">/h</span></span>
                            </div>
                            <a :href="'/booking?id=' + field.id" class="px-5 py-2 bg-slate-900 text-white text-sm font-bold rounded-xl hover:bg-brand-600 transition-colors shadow-md decoration-0">
                                Réserver
                            </a>
                        </div>
                    </div>
                </article>
            </template>
        </div>
    </section>

    <!-- How it Works -->
    <section id="how" class="py-12 bg-slate-50 -mx-5 px-5 mb-20 rounded-3xl">
        <div class="text-center mb-10">
            <span class="inline-block py-1 px-3 rounded-full bg-brand-50 text-brand-600 text-[10px] font-bold uppercase tracking-wide mb-2">
                Simple & Rapide
            </span>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Comment ça marche ?</h2>
        </div>

        <div class="space-y-8">
            <div class="flex gap-4">
                <div class="w-10 h-10 rounded-lg bg-brand-100 text-brand-600 flex-shrink-0 flex items-center justify-center font-bold">1</div>
                <div>
                    <h3 class="font-bold text-slate-900 mb-1">Choisissez</h3>
                    <p class="text-xs text-slate-600">Sélectionnez le terrain et le créneau idéal.</p>
                </div>
            </div>
            <div class="flex gap-4">
                <div class="w-10 h-10 rounded-lg bg-brand-600 text-white flex-shrink-0 shadow-lg shadow-brand-200 flex items-center justify-center font-bold">2</div>
                <div>
                    <h3 class="font-bold text-slate-900 mb-1">Validez</h3>
                    <p class="text-xs text-slate-600">Processus sécurisé et vérification rapide.</p>
                </div>
            </div>
            <div class="flex gap-4">
                <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-600 flex-shrink-0 flex items-center justify-center font-bold">3</div>
                <div>
                    <h3 class="font-bold text-slate-900 mb-1">Jouez</h3>
                    <p class="text-xs text-slate-600">Recevez la confirmation et profitez du match.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-12 bg-slate-900 -mx-5 px-8 text-center rounded-3xl mb-20">
        <h2 class="text-2xl font-extrabold text-white mb-3">Prêt à jouer ?</h2>
        <p class="text-xs text-slate-400 mb-6">Rejoignez plus de 2,500 joueurs actifs sur ProMatch.</p>
        <a href="/booking" class="w-full inline-block text-center rounded-xl bg-brand-600 py-4 text-sm font-bold text-white shadow-xl shadow-brand-600/20 active:scale-95 transition-transform decoration-0">
            Réserver maintenant
        </a>
    </section>

    <!-- Footer -->
    <footer class="py-10 text-center border-t border-slate-200">
        <p class="text-[10px] uppercase font-bold tracking-widest text-slate-400 mb-2">ProMatch Maroc</p>
        <p class="text-xs text-slate-300">© 2026 Tous droits réservés.</p>
    </footer>
</div>

<script>
    function fieldApp() {
        return {
            fields: [],
            q: '',
            loading: true,
            error: null,
            errorTitle: "Erreur de connexion",
            fetchFields() {
                this.loading = true;
                this.error = null;
                
                const timestamp = new Date().getTime();
                const url = `/api/public-fields?query=${encodeURIComponent(this.q)}&t=${timestamp}`;
                
                fetch(url, { cache: 'no-store' })
                    .then(async response => {
                        const data = await response.json().catch(() => null);

                        if (!response.ok) {
                            throw new Error((data && (data.detail || data.message)) || ('Erreur HTTP ' + response.status));
                        }

                        return data;
                    })
                    .then(data => {
                        if (data && data.success === true && data.data) {
                            this.fields = data.data; 
                        } else {
                            this.fields = data.data || data || [];
                        }

                        this.loading = false;
                        
                        if (!this.fields || this.fields.length === 0) {
                            this.errorTitle = "Aucun terrain trouve";
                            this.error = this.q
                                ? "Aucun terrain ne correspond a votre recherche."
                                : "Aucun terrain n'est encore disponible.";
                        }
                    })
                    .catch(error => {
                        this.loading = false;
                        this.errorTitle = "Erreur de connexion";
                        this.error = error.message;
                    });
            }
        }
    }
</script>
@endsection

