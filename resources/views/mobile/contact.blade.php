@extends('layouts.mobile')

@section('content')
<div class="pb-20">
    <!-- Header -->
    <div class="text-center max-w-3xl mx-auto mb-12">
        <span class="inline-block py-1 px-3 rounded-full bg-brand-50 text-brand-600 text-xs font-bold uppercase tracking-wide mb-3">
            Contact
        </span>
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-4">
            Parlons de votre prochain match
        </h1>
        <p class="text-base text-slate-500 leading-relaxed">
            Une question sur nos terrains ? Besoin d'organiser un tournoi ? Notre équipe est là pour vous répondre.
        </p>
    </div>

    <div class="grid lg:grid-cols-2 gap-8 items-start">
        
        <!-- Contact Info -->
        <div class="bg-slate-900 rounded-3xl p-8 lg:p-12 text-white relative overflow-hidden shadow-2xl shadow-slate-900/40">
            <!-- Background Pattern -->
            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-brand-600/20 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-64 h-64 bg-emerald-600/20 rounded-full blur-3xl"></div>

            <div class="relative z-10 space-y-8">
                <div>
                    <h3 class="text-xl font-bold mb-6">Nos Coordonnées</h3>
                    <ul class="space-y-6">
                        <li class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-white uppercase text-xs tracking-wider mb-0.5">Adresse</p>
                                <p class="text-slate-400">123 Boulevard de la Corniche<br>Casablanca, Maroc</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-white uppercase text-xs tracking-wider mb-0.5">Téléphone</p>
                                <p class="text-slate-400">+212 6 12 34 56 78</p>
                                <p class="text-[10px] text-slate-500 mt-1 uppercase font-bold tracking-widest">Lun - Dim: 9h - 23h</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-white uppercase text-xs tracking-wider mb-0.5">Email</p>
                                <p class="text-slate-400">contact@promatch.ma</p>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="pt-8 border-t border-white/10">
                    <h4 class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500 mb-6">Suivez-nous</h4>
                    <div class="flex gap-4">
                        <a href="#" class="w-11 h-11 rounded-xl bg-white/10 flex items-center justify-center hover:bg-brand-600 transition-all hover:scale-110 active:scale-95">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="w-11 h-11 rounded-xl bg-white/10 flex items-center justify-center hover:bg-brand-600 transition-all hover:scale-110 active:scale-95">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </a>
                        <a href="#" class="w-11 h-11 rounded-xl bg-white/10 flex items-center justify-center hover:bg-brand-600 transition-all hover:scale-110 active:scale-95">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c0 .795-.646 1.44-1.441 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-xl shadow-slate-200/50">
            <h3 class="text-xl font-bold text-slate-900 mb-6">Envoyez-nous un message</h3>
            <form class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5 font-bold tracking-tight">Prénom</label>
                        <input type="text" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-brand-500 focus:ring-4 focus:ring-brand-100 outline-none transition-all bg-slate-50/50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5 font-bold tracking-tight">Nom</label>
                        <input type="text" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-brand-500 focus:ring-4 focus:ring-brand-100 outline-none transition-all bg-slate-50/50">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5 font-bold tracking-tight">Email</label>
                    <input type="email" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-brand-500 focus:ring-4 focus:ring-brand-100 outline-none transition-all bg-slate-50/50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5 font-bold tracking-tight">Sujet</label>
                    <select class="w-full appearance-none rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-brand-500 focus:ring-4 focus:ring-brand-100 outline-none transition-all bg-slate-50/50">
                        <option>Réservation de terrain</option>
                        <option>Organisation d'événement</option>
                        <option>Partenariat</option>
                        <option>Autre demande</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5 font-bold tracking-tight">Message</label>
                    <textarea rows="4" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-brand-500 focus:ring-4 focus:ring-brand-100 outline-none transition-all bg-slate-50/50 resize-none"></textarea>
                </div>
                <button type="button" class="w-full rounded-xl bg-brand-600 px-6 py-4 text-sm font-bold text-white hover:bg-brand-500 transition-all shadow-lg shadow-brand-600/20 active:scale-95">
                    Envoyer le message
                </button>
            </form>
        </div>

    </div>
</div>
@endsection

