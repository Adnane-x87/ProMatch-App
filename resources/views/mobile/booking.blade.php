@extends('layouts.mobile')

@section('content')
<div x-data="bookingApp()" x-init="init()" class="pb-12">
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-slate-900 mb-2">Réserver votre terrain</h1>
        <p class="text-slate-500">Réservation par créneau d'1 heure</p>
    </div>

    <!-- Booking Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        
        <!-- Loading State -->
        <template x-if="loading">
            <div class="p-12 text-center">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-brand-600 mx-auto mb-4"></div>
                <p class="text-slate-500 font-medium">Chargement des données...</p>
            </div>
        </template>

        <div x-show="!loading" x-cloak>
            <!-- Terrain Selection -->
            <div class="p-6 border-b border-slate-100">
                <label class="block text-sm font-medium text-slate-700 mb-2">Terrain</label>
                <select x-model="formData.field_id" @change="fetchTimeSlots()"
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none transition-all">
                    <option value="">Sélectionnez un terrain</option>
                    <template x-for="field in fields" :key="field.id">
                        <option :value="field.id" x-text="`${field.name} (${field.price_per_hour} DH/h)`"></option>
                    </template>
                </select>
            </div>

            <!-- Date -->
            <div class="p-6 border-b border-slate-100">
                <label class="block text-sm font-medium text-slate-700 mb-2">Date</label>
                <input type="date" x-model="formData.date" @change="fetchTimeSlots()"
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none transition-all">
            </div>

            <!-- Time Slots -->
            <div class="p-6 border-b border-slate-100">
                <label class="block text-sm font-medium text-slate-700 mb-3">
                    Heure disponible <span class="text-xs font-normal text-slate-400">(créneau 1h)</span>
                </label>
                
                <template x-if="slotsLoading">
                    <div class="py-4 text-center">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-brand-600 mx-auto"></div>
                    </div>
                </template>

                <template x-if="!slotsLoading && timeSlots.length > 0">
                    <div class="grid grid-cols-4 gap-3">
                        <template x-for="slot in timeSlots" :key="slot.id">
                            <button type="button"
                                @click="formData.time_slot_id = slot.id; formData.selected_time = slot.start_time"
                                :class="formData.time_slot_id == slot.id ? 'bg-brand-600 text-white border-brand-600' : 'border-slate-200 text-slate-600 hover:border-brand-500 hover:text-brand-600'"
                                class="py-2.5 px-3 rounded-lg border text-sm font-medium transition-all text-center"
                                x-text="slot.start_time">
                            </button>
                        </template>
                    </div>
                </template>
                
                <template x-if="!slotsLoading && timeSlots.length === 0 && formData.date && formData.field_id">
                    <p class="text-sm text-slate-400 italic text-center py-4">Aucun créneau disponible pour cette date.</p>
                </template>

                <template x-if="!formData.date || !formData.field_id">
                    <p class="text-sm text-slate-400 italic text-center py-4">Sélectionnez un terrain et une date.</p>
                </template>
            </div>

            <!-- User Info -->
            <div class="p-6 border-b border-slate-100">
                <label class="block text-sm font-medium text-slate-700 mb-2">Vos informations</label>
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <input type="text" placeholder="Prénom" x-model="formData.first_name"
                        class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none transition-all">
                    <input type="text" placeholder="Nom" x-model="formData.last_name"
                        class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none transition-all">
                </div>
                <input type="tel" placeholder="Téléphone" x-model="formData.phone"
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none mb-3 transition-all">
                <input type="email" placeholder="Email" x-model="formData.email"
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none mb-3 transition-all">

                <!-- CNI Upload -->
                <div class="mt-4">
                    <div class="relative border-2 border-dashed border-slate-300 rounded-xl p-6 flex flex-col items-center justify-center cursor-pointer hover:border-brand-500 transition-all bg-white overflow-hidden"
                         :class="cniPreview ? 'border-brand-500 bg-brand-50' : ''"
                         @click="$refs.cniInput.click()">
                        
                        <input type="file" 
                               x-ref="cniInput"
                               accept="image/*" 
                               capture="environment"
                               class="hidden" 
                               @change="handleFileUpload">

                        <template x-if="!cniPreview">
                            <div class="text-center">
                                <svg class="w-8 h-8 text-slate-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-sm text-slate-500">Ajouter votre CNI ou <span class="text-brand-600 font-bold">cliquez ici</span></p>
                            </div>
                        </template>

                        <template x-if="cniPreview">
                            <div class="text-center relative">
                                <img :src="cniPreview" class="max-h-32 mx-auto rounded-lg shadow-sm border border-brand-200">
                                <p class="text-xs text-brand-600 mt-2 font-bold text-emerald-600">L'image a été sélectionnée</p>
                                <button type="button" @click.stop="cniPreview = null; formData.cni_image_base64 = ''" 
                                        class="absolute -top-2 -right-2 bg-white text-red-500 rounded-full p-1.5 shadow-md border border-slate-100 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
                <p class="text-xs text-slate-400 mt-2">La CNI est requise pour valider votre réservation</p>
            </div>

            <!-- Total & Submit -->
            <div class="p-6 bg-slate-50 rounded-b-xl">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm text-slate-600">Total <span class="text-xs text-slate-400">(1 heure)</span></span>
                    <span class="text-2xl font-bold text-slate-900" x-text="totalPrice">0 DH</span>
                </div>
                
                <button type="button" @click="submitBooking()"
                    :disabled="submitting || !isFormValid"
                    :class="(!isFormValid || submitting) ? 'opacity-50 cursor-not-allowed bg-slate-400' : 'bg-brand-600 hover:bg-brand-700 shadow-xl shadow-brand-200'"
                    class="w-full py-3 text-white font-semibold rounded-lg transition-all flex items-center justify-center gap-2">
                    <span x-show="submitting" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full font-bold uppercase tracking-widest text-[10px]"></span>
                    <span x-text="submitting ? 'Envoi...' : 'Envoyer la demande'"></span>
                </button>
                <p x-show="!isFormValid" class="text-[10px] text-red-500 text-center mt-2 font-medium">Veuillez remplir tous les champs et ajouter votre CNI.</p>
                <p class="text-xs text-slate-400 text-center mt-3">Paiement sur place • Confirmation sous 24h</p>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div x-show="showSuccess" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl p-8 max-w-sm w-full text-center shadow-2xl" @click.away="showSuccess = false">
            <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm shadow-emerald-200">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 mb-3 tracking-tight">Demande envoyée !</h2>
            <p class="text-slate-500 text-sm mb-8 leading-relaxed">
                Votre réservation est en attente de validation. Vous recevrez une confirmation par SMS sous 24h.
            </p>
            <div class="bg-slate-50 rounded-xl p-5 mb-8 text-left text-sm space-y-3 border border-slate-100">
                <div class="flex justify-between">
                    <span class="text-slate-500 font-medium">Terrain</span>
                    <span class="font-bold text-slate-900" x-text="getSelectedFieldName()">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500 font-medium">Date</span>
                    <span class="font-bold text-slate-900" x-text="formData.date">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500 font-medium">Heure</span>
                    <span class="font-bold text-slate-900" x-text="formData.selected_time">-</span>
                </div>
            </div>
            <button @click="window.location.href='/'" 
                class="w-full py-4 bg-slate-900 text-white font-bold rounded-xl hover:bg-slate-800 transition-all shadow-lg active:scale-[0.98]">
                Retour à l'accueil
            </button>
        </div>
    </div>
</div>

<script>
    function bookingApp() {
        // --- Configuration ---
        // NativePHP serves via an internal localhost WebView, but on Android
        // the actual host machine (where Laravel runs) is always 10.0.2.2
        const API_CONFIG = {
            baseUrl: 'http://10.0.2.2:8000',
            token: localStorage.getItem('api_token') || '',
        };

        return {
            fields: [],
            timeSlots: [],
            loading: true,
            slotsLoading: false,
            submitting: false,
            showSuccess: false,
            error: null, // Error state for UI feedback
            cniPreview: null,
            cniFile: null, // 👈 Added to store the actual file object
            formData: {
                field_id: '',
                date: '',
                time_slot_id: '',
                selected_time: '',
                first_name: '',
                last_name: '',
                phone: '',
                email: '',
                cni_image_base64: ''
            },
            
            async init() {
                const urlParams = new URLSearchParams(window.location.search);
                this.formData.field_id = urlParams.get('id') || '';
                
                const today = new Date().toISOString().split('T')[0];
                this.formData.date = today;
                
                await this.fetchFields();
                if (this.formData.field_id) {
                    await this.fetchTimeSlots();
                }
            },

            get headers() {
                const h = { 'Accept': 'application/json' };
                if (API_CONFIG.token) {
                    h['Authorization'] = `Bearer ${API_CONFIG.token}`;
                }
                return h;
            },

            async fetchFields() {
                this.loading = true;
                this.error = null;
                const timestamp = new Date().getTime();
                try {
                    console.log(`Fetching fields from: ${API_CONFIG.baseUrl}/api/public-fields?t=${timestamp}`);
                    const res = await fetch(`${API_CONFIG.baseUrl}/api/public-fields?t=${timestamp}`, { 
                        cache: 'no-store'
                    });
                    
                    if (!res.ok) throw new Error(`Erreur ${res.status}: ${res.statusText}`);
                    const data = await res.json();
                    
                    this.fields = data.data || data || [];
                    console.log('Fields loaded:', this.fields);
                    
                    if (this.fields.length === 0) {
                        this.error = "Aucun terrain trouvé sur le serveur.";
                    }
                } catch (e) {
                    console.error('Error fetching fields:', e);
                    this.error = "Erreur de connexion : " + e.message;
                } finally {
                    this.loading = false;
                }
            },

            async fetchTimeSlots() {
                if (!this.formData.field_id || !this.formData.date) return;
                
                this.slotsLoading = true;
                this.formData.time_slot_id = '';
                this.formData.selected_time = '';
                this.error = null;
                
                const timestamp = new Date().getTime();
                try {
                    const res = await fetch(`${API_CONFIG.baseUrl}/api/available-slots?field_id=${this.formData.field_id}&date=${this.formData.date}&t=${timestamp}`, { 
                        cache: 'no-store'
                    });

                    if (res.ok) {
                        const data = await res.json();
                        this.timeSlots = data.data || data || [];
                    } else if (res.status === 404) {
                        console.warn("L'endpoint /api/available-slots n'existe pas encore. Utilisation des créneaux par défaut.");
                        this.useDummySlots();
                    } else {
                        throw new Error(`Erreur ${res.status}`);
                    }
                } catch (e) {
                    console.error('Error fetching slots:', e);
                    this.useDummySlots();
                } finally {
                    this.slotsLoading = false;
                }
            },

            useDummySlots() {
                this.timeSlots = [
                    { id: 1, start_time: '18:00' },
                    { id: 2, start_time: '19:00' },
                    { id: 3, start_time: '20:00' },
                    { id: 4, start_time: '21:00' },
                ];
            },

            handleFileUpload(e) {
                const file = e.target.files[0];
                if (!file) return;

                // Validate file size (e.g., max 5MB)
                const maxSize = 5 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert("L'image est trop volumineuse (maximum 5 Mo). Veuillez choisir une photo plus petite.");
                    e.target.value = ''; // Reset input
                    return;
                }
                
                this.cniFile = file; // 👈 Store the actual file object

                const reader = new FileReader();
                reader.onload = (f) => {
                    this.cniPreview = f.target.result;
                    // We no longer necessarily need the base64 for submission, 
                    // but we keep the preview for the UI.
                };
                reader.readAsDataURL(file);
            },

            get isFormValid() {
                return this.formData.field_id && 
                       this.formData.date && 
                       this.formData.time_slot_id && 
                       this.formData.first_name && 
                       this.formData.last_name && 
                       this.formData.phone && 
                       this.formData.email && 
                       this.cniFile; // 👈 Check for the actual file
            },

            get totalPrice() {
                const field = this.fields.find(f => f.id == this.formData.field_id);
                return field ? `${field.price_per_hour} DH` : '0 DH';
            },

            getSelectedFieldName() {
                const field = this.fields.find(f => f.id == this.formData.field_id);
                return field ? field.name : '-';
            },

            async submitBooking() {
                if (!this.isFormValid) return;
                
                this.submitting = true;
                this.error = null;
                
                try {
                    // 🚀 NEW: Using FormData for native file upload handling
                    const body = new FormData();
                    
                    // Append all text fields
                    Object.keys(this.formData).forEach(key => {
                        if (key !== 'cni_image_base64') {
                            body.append(key, this.formData[key]);
                        }
                    });

                    // Append the actual file
                    if (this.cniFile) {
                        body.append('cni_image', this.cniFile); // The backend expects 'cni_image'
                    }

                    const res = await fetch(`${API_CONFIG.baseUrl}/api/reservations`, {
                        method: 'POST',
                        headers: { 
                            'Accept': 'application/json',
                            // ⚠️ DO NOT set Content-Type header when using FormData, 
                            // the browser will set it automatically with the correct boundary
                            ...(API_CONFIG.token ? { 'Authorization': `Bearer ${API_CONFIG.token}` } : {})
                        },
                        body: body
                    });
                    
                    if (res.ok) {
                        this.showSuccess = true;
                    } else {
                        const errorData = await res.json().catch(() => ({}));
                        throw new Error(errorData.message || `Erreur ${res.status}: Échec de l'envoi de la demande.`);
                    }
                } catch (e) {
                    console.error('Submission error:', e);
                    this.error = "Erreur lors de l'envoi : " + e.message;
                    alert("سبب الرفض من السيرفر: \n" + e.message);
                } finally {
                    this.submitting = false;
                }
            }
        };
    }
</script>
@endsection
