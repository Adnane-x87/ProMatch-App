@extends('layouts.admin')

@section('title', 'ProMatch — Dashboard Admin')
@section('page-title', 'Tableau de bord')
@section('page-subtitle', 'Aperçu de vos terrains aujourd\'hui')

@section('content')
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <div class="bg-white p-5 rounded-xl border border-slate-200">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-slate-500">Recettes aujourd'hui</p>
                {{-- TODO: wire up $revenueGrowth --}}
                <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 text-xs font-semibold">{{ $revenueGrowth ?? '+12%' }}</span>
            </div>
            {{-- TODO: wire up $todayRevenue --}}
            <p class="text-2xl font-bold text-slate-900">{{ number_format($todayRevenue ?? 1240, 0, ',', ' ') }} <span class="text-sm font-medium text-slate-400">MAD</span></p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200">
            <p class="text-sm font-medium text-slate-500 mb-3">Réservations</p>
            {{-- TODO: wire up $todayReservationsCount and $totalCapacity --}}
            <p class="text-2xl font-bold text-slate-900">{{ $todayReservationsCount ?? 8 }} <span class="text-sm font-medium text-slate-400">/ {{ $totalCapacity ?? 12 }}</span></p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200">
            <p class="text-sm font-medium text-slate-500 mb-3">Joueurs actifs</p>
            {{-- TODO: wire up $activePlayersCount --}}
            <p class="text-2xl font-bold text-slate-900">{{ $activePlayersCount ?? 42 }}</p>
        </div>

        <div class="bg-rose-50 p-5 rounded-xl border border-rose-100 cursor-pointer hover:bg-rose-100 transition-colors">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-medium text-rose-600">Validations CNI</p>
                <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
            </div>
            {{-- TODO: wire up $pendingValidationsCount --}}
            <p class="text-2xl font-bold text-rose-700">{{ $pendingValidationsCount ?? 2 }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6 mt-6">
        
        <!-- Main Column -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Today's Schedule -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="font-semibold text-slate-900 uppercase text-xs tracking-widest">Planning du jour</h2>
                    <span class="text-xs font-medium text-slate-400">16h - 22h</span>
                </div>
                <div class="p-8">
                    <div class="relative">
                        <div class="absolute top-1/2 left-0 right-0 h-0.5 bg-slate-100 -translate-y-1/2 rounded-full opacity-60"></div>
                        <div class="grid grid-cols-4 gap-4 relative">
                            {{-- TODO: loop through $scheduleSlots --}}
                            
                            <div class="bg-brand-50 border border-brand-100 p-4 rounded-xl text-center shadow-sm relative group hover:bg-brand-100 transition-all">
                                <div class="w-2 h-2 rounded-full bg-brand-500 mx-auto mb-2 shadow-sm shadow-brand-200"></div>
                                <p class="text-[10px] font-bold text-brand-700 uppercase tracking-tight">16:00</p>
                                <p class="text-xs text-slate-600 font-semibold mt-1 truncate">Yassine M.</p>
                                <div class="absolute -top-2 left-1/2 -translate-x-1/2 px-2 py-0.5 bg-brand-600 text-white rounded-full text-[8px] font-extrabold shadow-sm scale-0 group-hover:scale-100 transition-transform">T1</div>
                            </div>

                            <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl text-center shadow-sm relative group hover:bg-emerald-100 transition-all">
                                <div class="w-2 h-2 rounded-full bg-emerald-500 mx-auto mb-2 shadow-sm shadow-emerald-200"></div>
                                <p class="text-[10px] font-bold text-emerald-700 uppercase tracking-tight">17:00</p>
                                <p class="text-xs text-slate-600 font-semibold mt-1 truncate">Club Junior</p>
                                <div class="absolute -top-2 left-1/2 -translate-x-1/2 px-2 py-0.5 bg-emerald-600 text-white rounded-full text-[8px] font-extrabold shadow-sm scale-0 group-hover:scale-100 transition-transform">T2</div>
                            </div>

                            <div class="bg-white border border-dashed border-slate-200 p-4 rounded-xl text-center opacity-60 hover:opacity-100 transition-opacity">
                                <div class="w-2 h-2 rounded-full bg-slate-200 mx-auto mb-2"></div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tight">18:00</p>
                                <p class="text-[9px] text-slate-300 font-extrabold mt-2 uppercase">L I B R E</p>
                            </div>

                            <div class="bg-amber-50 border border-amber-100 p-4 rounded-xl text-center shadow-sm relative group hover:bg-amber-100 transition-all">
                                <div class="w-2 h-2 rounded-full bg-amber-500 mx-auto mb-2 shadow-sm shadow-amber-200"></div>
                                <p class="text-[10px] font-bold text-amber-700 uppercase tracking-tight">19:00</p>
                                <p class="text-xs text-slate-600 font-semibold mt-1 truncate">Tournoi</p>
                                <div class="absolute -top-2 left-1/2 -translate-x-1/2 px-2 py-0.5 bg-amber-600 text-white rounded-full text-[8px] font-extrabold shadow-sm scale-0 group-hover:scale-100 transition-transform">T1</div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Reservations Table -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h2 class="font-semibold text-slate-900 uppercase text-xs tracking-widest">Dernières réservations</h2>
                    <a href="{{ url('admin/reservations') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700 uppercase tracking-wider">Voir tout</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                            <tr>
                                <th class="px-6 py-4">Client</th>
                                <th class="px-6 py-4">Terrain</th>
                                <th class="px-6 py-4">Heure</th>
                                <th class="px-6 py-4">Statut</th>
                                <th class="px-6 py-4 text-right"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            {{-- TODO: loop through $recentReservations --}}
                            
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-600 shadow-sm border border-slate-200">YM</div>
                                        <span class="font-semibold text-slate-900">Yassine M.</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded bg-slate-100 text-slate-500 font-bold text-[10px]">T1</span>
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-900">18:00</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-600 text-xs font-bold border border-amber-100 italic">En attente</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button class="p-2 text-slate-300 hover:text-slate-600 rounded-lg hover:bg-slate-50 transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                                    </button>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center text-xs font-bold text-brand-600 shadow-sm border border-brand-200">OB</div>
                                        <span class="font-semibold text-slate-900">Omar B.</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded bg-slate-100 text-slate-500 font-bold text-[10px]">T2</span>
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-900">20:00</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600 text-xs font-bold border border-emerald-100 italic">Confirmé</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button class="p-2 text-slate-300 hover:text-slate-600 rounded-lg hover:bg-slate-50 transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            
            <!-- CNI Tasks -->
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
                <h2 class="font-bold text-slate-900 text-xs uppercase tracking-widest mb-6">Validations CNI</h2>
                
                <div class="space-y-4">
                    {{-- TODO: loop through $pendingValidations --}}
                    
                    <div class="p-5 border border-slate-100 rounded-2xl bg-slate-50/30 hover:bg-slate-50 transition-colors">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-11 h-11 rounded-full bg-slate-100 flex items-center justify-center text-sm font-bold text-slate-600 shadow-sm border border-slate-200">YM</div>
                            <div>
                                <p class="text-sm font-bold text-slate-900">Yassine Moukrim</p>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wide">Attente de validation</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button class="flex-1 py-2.5 text-xs font-bold text-white bg-slate-900 rounded-xl hover:bg-slate-800 transition-all active:scale-95 shadow-lg shadow-slate-900/10">Vérifier</button>
                            <button class="px-3 py-2.5 text-xs font-bold text-slate-500 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-all active:scale-95">Ignorer</button>
                        </div>
                    </div>

                    <div class="p-5 border border-slate-100 rounded-2xl bg-slate-50/30">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-11 h-11 rounded-full bg-slate-100 flex items-center justify-center text-sm font-bold text-slate-600 shadow-sm border border-slate-200">AH</div>
                            <div>
                                <p class="text-sm font-bold text-slate-900">Amine Hassani</p>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wide">Attente de validation</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button class="flex-1 py-2.5 text-xs font-bold text-white bg-slate-900 rounded-xl hover:bg-slate-800 shadow-lg shadow-slate-900/10">Vérifier</button>
                            <button class="px-3 py-2.5 text-xs font-bold text-slate-500 bg-white border border-slate-200 rounded-xl">Ignorer</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
