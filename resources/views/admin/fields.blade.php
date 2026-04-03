@extends('layouts.admin')

@section('title', 'ProMatch — Gérer les terrains')
@section('page-title', 'Gestion des terrains')
@section('page-subtitle', 'Ajoutez, modifiez ou supprimez vos installations sportives')

@section('content')
<div x-data="fieldManager()" x-init="fetchFields()" class="space-y-6">
    
    <!-- Top Actions -->
    <div class="flex justify-between items-center bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-brand-50 text-brand-600 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-900" x-text="fields.length + ' terrains enregistrés'"></p>
                <p class="text-xs text-slate-500">Mise à jour en temps réel</p>
            </div>
        </div>
        <button @click="openAddModal()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-600 text-white text-sm font-bold rounded-xl hover:bg-brand-700 transition-all shadow-lg shadow-brand-600/20 active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Ajouter un terrain
        </button>
    </div>

    <!-- Fields Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-bold text-slate-500 uppercase tracking-widest">
                    <tr>
                        <th class="px-6 py-4">Nom du terrain</th>
                        <th class="px-6 py-4">Localisation</th>
                        <th class="px-6 py-4 text-center">Prix / h</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-if="loading">
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-brand-600 mb-2"></div>
                                <p class="text-slate-400 font-medium">Chargement des terrains...</p>
                            </td>
                        </tr>
                    </template>

                    <template x-if="!loading && fields.length === 0">
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <p class="text-slate-400 italic">Aucun terrain trouvé. Commencez par en ajouter un !</p>
                            </td>
                        </tr>
                    </template>

                    <template x-for="field in fields" :key="field.id">
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-brand-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900" x-text="field.name"></p>
                                        <p class="text-xs text-slate-500 truncate max-w-[200px]" x-text="field.description"></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600 font-medium" x-text="field.address"></td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-bold" x-text="field.price_per_hour + ' DH'"></span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <button @click="openEditModal(field)" class="p-2 text-slate-400 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button @click="deleteField(field.id)" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- CRUD Modal -->
    <div x-show="showModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" x-cloak>
        
        <div @click.away="showModal = false" 
             class="bg-white rounded-3xl w-full max-w-lg shadow-2xl overflow-hidden border border-slate-200">
            
            <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div>
                    <h3 class="text-xl font-bold text-slate-900" x-text="editingField ? 'Modifier le terrain' : 'Nouveau terrain'"></h3>
                    <p class="text-xs text-slate-500 mt-1">Remplissez les informations ci-dessous</p>
                </div>
                <button @click="showModal = false" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-white transition-all shadow-sm border border-transparent hover:border-slate-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form @submit.prevent="saveField()" class="p-8 space-y-5">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nom du terrain</label>
                    <input type="text" x-model="formData.name" required placeholder="Ex: Terrain Central"
                           class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-brand-500 focus:ring-4 focus:ring-brand-100 outline-none transition-all placeholder:text-slate-300">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Adresse / Localisation</label>
                    <input type="text" x-model="formData.address" required placeholder="Ex: Casablanca, Maarif"
                           class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-brand-500 focus:ring-4 focus:ring-brand-100 outline-none transition-all placeholder:text-slate-300">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Prix par heure (DH)</label>
                    <input type="number" x-model="formData.price_per_hour" required placeholder="250"
                           class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-brand-500 focus:ring-4 focus:ring-brand-100 outline-none transition-all placeholder:text-slate-300">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Description</label>
                    <textarea x-model="formData.description" rows="3" placeholder="Description du terrain..."
                              class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-brand-500 focus:ring-4 focus:ring-brand-100 outline-none transition-all placeholder:text-slate-300 resize-none"></textarea>
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="button" @click="showModal = false" 
                            class="flex-1 py-3.5 px-4 rounded-xl border border-slate-200 text-sm font-bold text-slate-600 hover:bg-slate-50 transition-all active:scale-95">
                        Annuler
                    </button>
                    <button type="submit" 
                            :disabled="submitting"
                            class="flex-[2] py-3.5 px-4 rounded-xl bg-brand-600 text-white text-sm font-bold hover:bg-brand-700 transition-all shadow-lg shadow-brand-600/20 active:scale-95 disabled:opacity-50">
                        <span x-show="!submitting" x-text="editingField ? 'Mettre à jour' : 'Enregistrer le terrain'"></span>
                        <span x-show="submitting" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Traitement...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function fieldManager() {
        // Backend API is on host machine
        const API_URL = 'http://10.0.2.2:8000/api/fields';

        return {
            fields: [],
            loading: true,
            submitting: false,
            showModal: false,
            editingField: null,
            formData: {
                name: '',
                address: '',
                description: '',
                price_per_hour: ''
            },

            async fetchFields() {
                this.loading = true;
                try {
                    const response = await fetch(API_URL);
                    const result = await response.json();
                    this.fields = result.data || result;
                } catch (error) {
                    console.error('Erreur lors de la récupération:', error);
                } finally {
                    this.loading = false;
                }
            },

            openAddModal() {
                this.editingField = null;
                this.formData = { name: '', address: '', description: '', price_per_hour: '' };
                this.showModal = true;
            },

            openEditModal(field) {
                this.editingField = field;
                this.formData = { ...field };
                this.showModal = true;
            },

            async saveField() {
                this.submitting = true;
                const method = this.editingField ? 'PUT' : 'POST';
                const url = this.editingField ? `${API_URL}/${this.editingField.id}` : API_URL;

                try {
                    const response = await fetch(url, {
                        method: method,
                        headers: { 
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.formData)
                    });

                    if (response.ok) {
                        await this.fetchFields();
                        this.showModal = false;
                    } else {
                        const error = await response.json();
                        alert('Erreur: ' + (error.message || 'Impossible de sauvegarder'));
                    }
                } catch (error) {
                    console.error('Erreur lors de la sauvegarde:', error);
                } finally {
                    this.submitting = false;
                }
            },

            async deleteField(id) {
                if (!confirm('Êtes-vous sûr de vouloir supprimer ce terrain ?')) return;

                try {
                    const response = await fetch(`${API_URL}/${id}`, { method: 'DELETE' });
                    if (response.ok) {
                        this.fields = this.fields.filter(f => f.id !== id);
                    } else {
                        alert('Erreur lors de la suppression');
                    }
                } catch (error) {
                    console.error('Erreur lors de la suppression:', error);
                }
            }
        }
    }
</script>
@endpush
@endsection
