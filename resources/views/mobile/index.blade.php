@extends('layouts.mobile')

@section('content')
<div x-data="fieldCatalogue()" x-init="fetchFields()" x-cloak class="space-y-8">
    <!-- Search and Filters Section -->
    <div class="space-y-4">
        <h2 class="text-3xl font-extrabold tracking-tight">Find Your <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-violet-600">Perfect Match</span></h2>
        
        <!-- Premium Search Bar -->
        <div class="relative group">
            <input type="text" x-model="searchQuery" placeholder="Search for fields, location..." 
                   class="w-full h-14 pl-12 pr-6 rounded-2xl glass bg-white/40 dark:bg-slate-900/40 focus:ring-2 focus:ring-blue-500/50 outline-none transition-all shadow-sm group-hover:shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 absolute left-4 top-4 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>

        <!-- Filter Chips -->
        <div class="flex items-center gap-3 overflow-x-auto pb-2 scrollbar-hide no-scrollbar">
            <template x-for="category in categories" :key="category">
                <button @click="selectedCategory = category" 
                        :class="selectedCategory === category ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/30' : 'glass text-slate-500'"
                        class="px-5 py-2.5 rounded-full text-sm font-bold whitespace-nowrap transition-all active:scale-95"
                        x-text="category">
                </button>
            </template>
        </div>
    </div>

    <!-- Fields Listing -->
    <div class="grid grid-cols-1 gap-6">
        <!-- Loading State: Premium Skeleton Pulsing -->
        <template x-if="loading">
            <div class="space-y-6">
                <template x-for="i in 3">
                    <div class="glass rounded-3xl overflow-hidden animate-pulse">
                        <div class="h-48 bg-slate-200 dark:bg-slate-800"></div>
                        <div class="p-5 space-y-3">
                            <div class="h-6 bg-slate-200 dark:bg-slate-800 rounded-lg w-2/3"></div>
                            <div class="h-4 bg-slate-200 dark:bg-slate-800 rounded-lg w-1/2"></div>
                            <div class="flex justify-between items-center pt-2">
                                <div class="h-6 bg-slate-200 dark:bg-slate-800 rounded-lg w-20"></div>
                                <div class="h-10 bg-slate-200 dark:bg-slate-800 rounded-xl w-32"></div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        <!-- Data State: Dynamic Grid -->
        <template x-if="!loading && filteredFields().length > 0">
            <div class="space-y-6">
                <template x-for="field in filteredFields()" :key="field.id">
                    <div class="glass rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition-all group active:scale-[0.98]">
                        <!-- Image Container -->
                        <div class="relative h-52 overflow-hidden">
                            <img :src="field.image_url || 'https://images.unsplash.com/photo-1544919982-b61976f0ba43?q=80&w=800&auto=format&fit=crop'" 
                                 :alt="field.name" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute top-4 right-4 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md px-3 py-1.5 rounded-xl text-sm font-bold shadow-soft">
                                <span x-text="'$' + field.price"></span><span class="text-xs text-slate-500 font-medium lowercase">/hr</span>
                            </div>
                            <div class="absolute bottom-4 left-4 flex gap-2">
                                <span class="bg-blue-600/90 backdrop-blur-md text-white text-[10px] uppercase tracking-widest font-black px-2 py-1 rounded-md">Featured</span>
                            </div>
                        </div>
                        
                        <!-- Content -->
                        <div class="p-5">
                            <div class="flex justify-between items-start mb-1">
                                <h3 class="text-xl font-bold tracking-tight" x-text="field.name"></h3>
                                <div class="flex items-center gap-1 text-amber-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-current" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                    <span class="text-xs font-bold transform -translate-y-px">4.8</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 text-slate-400 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="text-xs font-medium" x-text="field.address"></span>
                            </div>
                            
                            <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800">
                                <div class="flex -space-x-2">
                                    <template x-for="i in 3">
                                        <div class="w-8 h-8 rounded-full border-2 border-white dark:border-slate-800 bg-slate-200 overflow-hidden">
                                            <img :src="'https://i.pravatar.cc/100?u=' + Math.random()" alt="avatar">
                                        </div>
                                    </template>
                                    <div class="w-8 h-8 rounded-full border-2 border-white dark:border-slate-800 bg-blue-50 flex items-center justify-center">
                                        <span class="text-[10px] font-bold text-blue-600">+12</span>
                                    </div>
                                </div>
                                <button class="bg-gradient-to-tr from-blue-600 to-violet-600 text-white px-6 py-3 rounded-2xl text-sm font-bold shadow-lg shadow-blue-500/20 active:scale-95 transition-all">
                                    Book Now
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </template>

        <!-- Empty State -->
        <template x-if="!loading && filteredFields().length === 0">
            <div class="flex flex-col items-center justify-center py-20 text-center space-y-4">
                <div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-500">No fields found</h3>
                <p class="text-slate-400 text-sm">Try adjusting your filters or search query.</p>
            </div>
        </template>
    </div>
</div>

<script>
    function fieldCatalogue() {
        return {
            fields: [],
            loading: true,
            searchQuery: '',
            selectedCategory: 'All Fields',
            categories: ['All Fields', 'Football', 'Padel', 'Tennis', 'Basketball'],
            
            async fetchFields() {
                this.loading = true;
                try {
                    const response = await fetch('/api/public-fields');
                    if (!response.ok) throw new Error('API fetch failed');
                    this.fields = await response.json();
                } catch (error) {
                    console.error('Error fetching fields:', error);
                    // Mock data if API is actually missing, for demo purposes in Sprint 1
                    if (!this.fields.length) {
                        this.fields = [
                            { id: 1, name: 'Marrakech Pro Padel', address: 'Gueliz, Marrakech', price: 25, category: 'Padel' },
                            { id: 2, name: 'Stadium Arena', address: 'Palais des Congrès', price: 40, category: 'Football' },
                            { id: 3, name: 'Club de Tennis', address: 'Hivernage', price: 15, category: 'Tennis' },
                        ];
                    }
                } finally {
                    setTimeout(() => { this.loading = false; }, 800); // Small delay for smooth skeleton transition
                }
            },

            filteredFields() {
                return this.fields.filter(field => {
                    const matchesSearch = field.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                          field.address.toLowerCase().includes(this.searchQuery.toLowerCase());
                    const matchesCategory = this.selectedCategory === 'All Fields' || field.category === this.selectedCategory;
                    return matchesSearch && matchesCategory;
                });
            }
        }
    }
</script>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .shadow-soft { box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05); }
</style>
@endsection
