@extends('layouts.mobile')

@section('content')
<div x-data="dashboardApp()" x-init="init()" class="pb-12" x-cloak>
    
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Tableau de bord</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Aperçu de vos terrains aujourd'hui</p>
    </div>

    <!-- Loading State -->
    <template x-if="loading">
        <div class="flex flex-col items-center justify-center py-12">
            <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-brand-600 mb-4"></div>
            <p class="text-slate-500 font-medium">Chargement des données API...</p>
        </div>
    </template>

    <!-- Error State -->
    <template x-if="error">
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
            <p class="text-red-700 font-semibold text-sm" x-text="error"></p>
            <button @click="fetchData()" class="mt-2 text-xs bg-red-100 px-3 py-1.5 rounded-lg text-red-700 font-bold hover:bg-red-200">Réessayer</button>
        </div>
    </template>

    <div x-show="!loading && !error" class="space-y-6">
        <!-- Revenue Card (Highlight) -->
        <div class="bg-gradient-to-br from-brand-600 to-brand-800 rounded-2xl p-6 text-white shadow-lg shadow-brand-900/20 relative overflow-hidden">
            <!-- Decorative circle -->
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            
            <p class="text-brand-100 text-sm font-medium mb-1 drop-shadow-sm">Recettes aujourd'hui</p>
            <div class="flex items-end gap-2">
                <h2 class="text-4xl font-extrabold tracking-tight drop-shadow-md" x-text="revenue"></h2>
                <span class="text-lg font-bold text-brand-200 mb-1">MAD</span>
            </div>
            <div class="mt-4 inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-white/20 backdrop-blur-md text-xs font-bold text-white border border-white/20">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                Détails via API
            </div>
        </div>

        <!-- Mini Stats Grid -->
        <div class="grid grid-cols-3 gap-3">
            <div class="glass bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-sm border border-slate-100 dark:border-slate-700 text-center">
                <p class="text-[10px] uppercase tracking-wider font-extrabold text-slate-400 mb-1">Réservs.</p>
                <p class="text-xl font-black text-slate-900 dark:text-white" x-text="reservationsCount"></p>
            </div>
            <div class="glass bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-sm border border-slate-100 dark:border-slate-700 text-center">
                <p class="text-[10px] uppercase tracking-wider font-extrabold text-slate-400 mb-1">Joueurs</p>
                <p class="text-xl font-black text-slate-900 dark:text-white" x-text="activePlayersCount"></p>
            </div>
            <div class="glass bg-slate-900 dark:bg-slate-100 rounded-2xl p-4 shadow-sm text-center relative overflow-hidden">
                <div class="absolute top-2 right-2 w-2 h-2 rounded-full bg-rose-500 animate-pulse"></div>
                <p class="text-[10px] uppercase tracking-wider font-extrabold text-slate-400 dark:text-slate-500 mb-1">CNI Valid.</p>
                <p class="text-xl font-black text-white dark:text-slate-900" x-text="pendingValidationsCount"></p>
            </div>
        </div>

        <!-- Validations CNI Section -->
        <template x-if="pendingReservations.length > 0">
            <div>
                <div class="flex items-center justify-between mb-3 px-1">
                    <h2 class="text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider">Validations CNI <span class="bg-rose-100 text-rose-600 px-2 py-0.5 rounded-full ml-1 text-[10px]" x-text="pendingValidationsCount"></span></h2>
                </div>
                <!-- Horizontal scroll for CNI tasks mapped into mobile cards -->
                <div class="flex overflow-x-auto pb-4 -mx-5 px-5 gap-4 snap-x">
                    <template x-for="task in pendingReservations" :key="task.id">
                        <div class="snap-center shrink-0 w-[260px] bg-white dark:bg-slate-800 rounded-2xl p-4 border border-rose-100 shadow-sm relative">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-xs font-bold text-slate-600 dark:text-slate-300" x-text="initials(task.first_name, task.last_name)"></div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900 dark:text-white truncate" x-text="`${task.first_name} ${task.last_name || ''}`"></p>
                                    <p class="text-[10px] text-slate-400 font-medium" x-text="formatTime(task.start_time) + ' • ' + (task.field ? task.field.name : '')"></p>
                                </div>
                            </div>
                            
                            <template x-if="task.cni_image">
                                <a :href="getImageUrl(task.cni_image)" target="_blank" class="flex items-center justify-center gap-2 mb-4 py-2 bg-slate-50 dark:bg-slate-700/50 rounded-lg text-xs font-semibold text-brand-600 dark:text-brand-400 hover:bg-brand-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Consulter le CNI
                                </a>
                            </template>
                            <template x-if="!task.cni_image">
                                <div class="mb-4 py-2 text-center text-xs font-medium text-slate-400 italic">Aucune image fournie</div>
                            </template>
                            
                            <div class="flex gap-2">
                                <button @click="validateReservation(task.id)" class="flex-1 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl text-xs font-bold shadow-md active:scale-95 transition-all">Valider</button>
                                <button class="px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold active:scale-95 transition-all">Refuser</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        <!-- Planning du Jour (Mobile Timeline) -->
        <div>
            <div class="flex items-center justify-between mb-4 px-1">
                <h2 class="text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider">Planning du jour</h2>
            </div>
            
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm p-5">
                <template x-if="planning.length === 0">
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-slate-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <p class="text-sm text-slate-400 font-medium">Aucune réservation trouvée via l'API.</p>
                    </div>
                </template>

                <div class="space-y-4 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-200 before:to-transparent">
                    <template x-for="slot in planning" :key="slot.id">
                        <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                            <!-- Timeline Indicator -->
                            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-white border-4 border-slate-50 dark:border-slate-900 shadow shrink-0 absolute left-0 md:left-1/2 md:-translate-x-1/2" :class="getTimelineColorClass(slot.status)">
                                <svg class="w-3 h-3 text-white" :class="slot.status === 'PENDING' ? 'text-amber-500' : 'text-emerald-500'" fill="currentColor" viewBox="0 0 20 20">
                                    <circle cx="10" cy="10" r="5" />
                                </svg>
                            </div>
                            
                            <!-- Card content -->
                            <div class="w-[calc(100%-3rem)] md:w-[calc(50%-2.5rem)] ml-14 md:ml-0 p-4 bg-slate-50 dark:bg-slate-700/30 rounded-2xl border border-slate-100 dark:border-slate-700 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start mb-1">
                                    <span class="text-xs font-black text-slate-900 dark:text-white" x-text="formatTime(slot.start_time)"></span>
                                    <span :class="statusColor(slot.status)" class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-widest" x-text="statusText(slot.status)"></span>
                                </div>
                                <p class="text-sm font-semibold text-slate-700 dark:text-slate-200" x-text="`${slot.first_name} ${slot.last_name || ''}`"></p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1" x-text="slot.field ? slot.field.name : 'Terrain'"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function dashboardApp() {
        // Bridge the PHP session token to localStorage for the API calls
        const sessionToken = "{{ session('api_token') }}";
        if (sessionToken) {
            localStorage.setItem('api_token', sessionToken);
        }

        // Use the API_URL from .env as the base for all calls
        // This ensures the emulator correctly hits 10.0.2.2 if configured
        const apiBaseUrl = "{{ str_replace('/api', '', env('API_URL', 'http://localhost:8000')) }}";
        
        const API_CONFIG = {
            baseUrl: apiBaseUrl,
            token: localStorage.getItem('api_token') || '',
        };

        return {
            planning: [],
            revenue: 0,
            reservationsCount: 0,
            activePlayersCount: 0,
            pendingValidationsCount: 0,
            loading: true,
            error: null,

            async init() {
                await this.fetchData();
            },

            async fetchData() {
                this.loading = true;
                this.error = null;
                try {
                    const today = new Date().toISOString().split('T')[0];
                    const timestamp = new Date().getTime();
                    const headers = { 'Accept': 'application/json' };
                    if (API_CONFIG.token) {
                        headers['Authorization'] = `Bearer ${API_CONFIG.token}`;
                    }

                    // STRICT API FETCH: No dummy data fallback
                    const res = await fetch(`${API_CONFIG.baseUrl}/api/planning?date=${today}&t=${timestamp}`, {
                        headers: headers,
                        cache: 'no-store'
                    });

                    if (res.ok) {
                        const data = await res.json();
                        this.planning = data.data || [];
                        this.calculateStats();
                    } else if (res.status === 401) {
                        this.error = "Erreur 401 : Vous devez vous authentifier auprès de l'API pour voir le tableau de bord (Sanctum).";
                    } else {
                        this.error = `Erreur API: ${res.status} ${res.statusText}`;
                    }
                } catch (e) {
                    console.error('Fetch error:', e);
                    this.error = "Erreur de connexion à l'API. Assurez-vous que l'API est lancée.";
                } finally {
                    this.loading = false;
                }
            },

            calculateStats() {
                this.reservationsCount = this.planning.length;
                this.revenue = this.planning.reduce((sum, res) => {
                    if (res.status === 'APPROVED' || res.status === 'CONFIRMED' || res.status === 'PENDING') {
                        const price = res.field ? parseFloat(res.field.price_per_hour) : 0;
                        return sum + (isNaN(price) ? 0 : price);
                    }
                    return sum;
                }, 0);
                
                this.pendingValidationsCount = this.planning.filter(r => r.status === 'PENDING').length;
                this.activePlayersCount = this.planning.length * 4;
            },

            get pendingReservations() {
                return this.planning.filter(r => r.status === 'PENDING');
            },

            initials(firstName, lastName) {
                const f = firstName ? firstName.charAt(0) : '';
                const l = lastName ? lastName.charAt(0) : '';
                return (f + l).toUpperCase() || '??';
            },

            formatTime(timeString) {
                if (!timeString) return '';
                const parts = timeString.split(' ');
                const timePart = parts.length > 1 ? parts[1] : parts[0];
                return timePart.substring(0, 5);
            },

            statusColor(status) {
                if (status === 'PENDING') return 'bg-amber-100 text-amber-700';
                if (status === 'APPROVED' || status === 'CONFIRMED') return 'bg-emerald-100 text-emerald-700';
                if (status === 'REJECTED' || status === 'CANCELED') return 'bg-rose-100 text-rose-700';
                return 'bg-slate-100 text-slate-700';
            },

            statusText(status) {
                if (status === 'PENDING') return 'Attente';
                if (status === 'APPROVED' || status === 'CONFIRMED') return 'Confirmé';
                if (status === 'REJECTED') return 'Refusé';
                if (status === 'CANCELED') return 'Annulé';
                return status;
            },

            getTimelineColorClass(status) {
                if (status === 'PENDING') return 'border-amber-200 bg-amber-50';
                if (status === 'APPROVED' || status === 'CONFIRMED') return 'border-emerald-200 bg-emerald-50';
                return 'border-brand-200 bg-brand-50';
            },

            getImageUrl(path) {
                if (!path) return '#';
                return `${API_CONFIG.baseUrl}/storage/${path}`;
            },

            async validateReservation(id) {
                try {
                    const headers = { 
                        'Content-Type': 'application/json',
                        'Accept': 'application/json' 
                    };
                    if (API_CONFIG.token) {
                        headers['Authorization'] = `Bearer ${API_CONFIG.token}`;
                    }

                    const res = await fetch(`${API_CONFIG.baseUrl}/api/reservations/${id}/validate`, {
                        method: 'PUT',
                        headers: headers,
                        body: JSON.stringify({ status: 'APPROVED' })
                    });

                    if (res.ok) {
                        const index = this.planning.findIndex(r => r.id === id);
                        if (index !== -1) {
                            this.planning[index].status = 'APPROVED';
                            this.calculateStats();
                        }
                    } else {
                        alert("Erreur " + res.status + " : L'action a échoué.");
                    }
                } catch (e) {
                    console.error("Validation error:", e);
                    alert("Erreur de connexion.");
                }
            }
        }
    }
</script>
@endsection

