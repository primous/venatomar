<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? __('portal.secure_portal') }} · Venatomar Banking</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .header-paris {
            background-color: #0B1F3A;
            background-image: linear-gradient(to bottom, rgba(11, 31, 58, 0.85), rgba(11, 31, 58, 0.95)), url("{{ asset('images/paris-header.svg') }}");
            background-size: cover;
            background-position: center top;
        }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="h-full bg-[#F5F7FA] font-sans antialiased text-[#24324A] flex flex-col">

    <!-- Top Desktop & Tablet Navigation Bar (Visible md: and up) -->
    <header class="hidden md:block bg-[#0B1F3A] border-b border-white/10 text-white sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Brand Logo -->
            <div class="flex items-center gap-8">
                <a href="{{ route('portal.dashboard', ['locale' => app()->getLocale()]) }}" class="flex items-center gap-2.5">
                    <img src="{{ asset('images/logo.svg') }}" alt="Venatomar" class="size-8">
                    <span class="font-display font-extrabold text-lg text-white tracking-tight">VENATOMAR</span>
                    <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded bg-[#0FA57E] text-white">{{ __('portal.online_banking') }}</span>
                </a>

                <!-- Nav links with dynamic translations -->
                <nav class="flex items-center gap-1 text-sm font-semibold">
                    <a href="{{ route('portal.dashboard', ['locale' => app()->getLocale()]) }}"
                        class="px-3 py-2 rounded-lg transition {{ request()->routeIs('portal.dashboard') ? 'bg-white/15 text-white' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                        {{ __('portal.nav_accounts') }}
                    </a>
                    <a href="{{ route('portal.transfers', ['locale' => app()->getLocale()]) }}"
                        class="px-3 py-2 rounded-lg transition {{ request()->routeIs('portal.transfers*') ? 'bg-white/15 text-white' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                        {{ __('portal.nav_transfers') }}
                    </a>
                    <a href="{{ route('portal.documents', ['locale' => app()->getLocale()]) }}"
                        class="px-3 py-2 rounded-lg transition {{ request()->routeIs('portal.documents*') ? 'bg-white/15 text-white' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                        {{ __('portal.nav_cards') }}
                    </a>
                    <a href="{{ route('portal.loans', ['locale' => app()->getLocale()]) }}"
                        class="px-3 py-2 rounded-lg transition {{ request()->routeIs('portal.loans*') ? 'bg-white/15 text-white' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                        {{ __('portal.nav_loans') }}
                    </a>
                    <a href="{{ route('portal.support', ['locale' => app()->getLocale()]) }}"
                        class="px-3 py-2 rounded-lg transition {{ request()->routeIs('portal.support*') ? 'bg-white/15 text-white' : 'text-slate-300 hover:text-white hover:bg-white/10' }}">
                        {{ __('portal.nav_help') }}
                    </a>
                </nav>
            </div>

            <!-- Right user info & logout -->
            <div class="flex items-center gap-4">
                <a href="{{ route('portal.support', ['locale' => app()->getLocale()]) }}" class="size-9 rounded-full bg-white/10 flex items-center justify-center text-slate-200 hover:text-white hover:bg-white/20 transition relative" title="Messages">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="absolute top-1 right-1 size-2 bg-[#F26B1D] rounded-full"></span>
                </a>

                <div class="flex items-center gap-3 pl-3 border-l border-white/15">
                    <a href="{{ route('portal.profile', ['locale' => app()->getLocale()]) }}" class="flex items-center gap-2.5 text-left group">
                        <div class="size-8 rounded-full bg-[#0FA57E] text-white font-bold text-xs flex items-center justify-center">
                            {{ strtoupper(substr(auth('client')->user()?->first_name ?? 'C', 0, 1) . substr(auth('client')->user()?->last_name ?? 'L', 0, 1)) }}
                        </div>
                        <div class="text-xs">
                            <div class="font-bold text-white group-hover:text-[#0FA57E] transition">{{ auth('client')->user()?->fullName() }}</div>
                            <div class="text-slate-400 font-mono text-[11px]">{{ auth('client')->user()?->country }} ({{ strtoupper(app()->getLocale()) }})</div>
                        </div>
                    </a>

                    <form method="POST" action="{{ route('portal.logout') }}">
                        @csrf
                        <button type="submit" class="p-2 text-slate-300 hover:text-rose-400 transition" title="{{ __('portal.logout') }}">
                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Header Banner Inspired by screenshot (Atmospheric Paris Dusk + "Tous les comptes" in client language) -->
    <section class="header-paris text-white pt-6 pb-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            
            <!-- Mobile Top Bar (< md) -->
            <div class="flex md:hidden items-center justify-between mb-4">
                <a href="{{ route('portal.dashboard', ['locale' => app()->getLocale()]) }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/logo.svg') }}" alt="Venatomar" class="size-7">
                    <span class="font-display font-extrabold text-base text-white">VENATOMAR</span>
                </a>

                <div class="flex items-center gap-2">
                    <a href="{{ route('portal.support', ['locale' => app()->getLocale()]) }}" class="size-8 rounded-full bg-white/15 flex items-center justify-center text-white relative">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="absolute top-1 right-1 size-2 bg-[#F26B1D] rounded-full"></span>
                    </a>
                    <a href="{{ route('portal.profile', ['locale' => app()->getLocale()]) }}" class="size-8 rounded-full bg-[#0FA57E] text-white font-bold text-xs flex items-center justify-center">
                        {{ strtoupper(substr(auth('client')->user()?->first_name ?? 'C', 0, 1)) }}
                    </a>
                </div>
            </div>

            <!-- Header Titles & Tabs -->
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div>
                    <div class="text-xs uppercase font-bold tracking-wider text-[#0FA57E] mb-1">
                        {{ __('portal.secure_portal') }} · {{ auth('client')->user()?->country }} ({{ strtoupper(app()->getLocale()) }})
                    </div>
                    <h1 class="font-display text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white tracking-tight">
                        {{ $pageHeading ?? __('portal.all_accounts') }}
                    </h1>
                </div>

                <!-- Subtabs "Tous" & "Personnaliser" -->
                <div class="flex items-center gap-6 text-sm font-semibold border-b border-white/15 sm:border-b-0 pb-1 sm:pb-0">
                    <a href="{{ route('portal.dashboard', ['locale' => app()->getLocale()]) }}" class="relative pb-2 text-white font-bold transition">
                        {{ __('portal.all') }}
                        @if(request()->routeIs('portal.dashboard'))
                            <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#0FA57E] rounded-full"></span>
                        @endif
                    </a>
                    <a href="{{ route('portal.profile', ['locale' => app()->getLocale()]) }}" class="relative pb-2 text-slate-300 hover:text-white transition">
                        {{ __('portal.customize') }}
                        @if(request()->routeIs('portal.profile*'))
                            <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-[#0FA57E] rounded-full"></span>
                        @endif
                    </a>
                </div>
            </div>

        </div>
    </section>

    <!-- Main Content Container -->
    <main class="-mt-6 rounded-t-[28px] bg-[#F5F7FA] px-4 sm:px-6 lg:px-8 pt-6 pb-24 md:pb-12 flex-1">
        <div class="max-w-7xl mx-auto">
            
            @if(session('status'))
                <div class="mb-5 p-4 bg-[#E6F5F1] border border-[#0FA57E]/30 rounded-xl text-[#0A5E4E] text-sm font-medium flex items-center gap-3 shadow-sm">
                    <svg class="size-5 text-[#0FA57E] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <div>{{ session('status') }}</div>
                </div>
            @endif

            @if(session('warning'))
                <div class="mb-5 p-4 bg-[#FFF1E8] border border-[#F26B1D]/30 rounded-xl text-[#B23F08] text-sm font-medium flex items-center gap-3 shadow-sm">
                    <svg class="size-5 text-[#F26B1D] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>{{ session('warning') }}</div>
                </div>
            @endif

            @if(session('error') || $errors->any())
                <div class="mb-5 p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-sm font-medium flex items-center gap-3 shadow-sm">
                    <svg class="size-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>{{ session('error') ?? $errors->first() }}</div>
                </div>
            @endif

            {{ $slot }}

        </div>
    </main>

    <!-- Mobile Bottom Floating Navigation Dock (Fixed at bottom only on mobile screens < md) -->
    <nav class="md:hidden fixed bottom-3 inset-x-3 z-40">
        <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-[0_8px_30px_rgba(11,31,58,0.18)] border border-slate-200 px-3 py-2 flex items-center justify-between">
            
            <!-- 1. Comptes -->
            <a href="{{ route('portal.dashboard', ['locale' => app()->getLocale()]) }}" class="flex flex-col items-center justify-center flex-1 py-1 group transition">
                <div class="size-8 rounded-full flex items-center justify-center text-sm font-bold {{ request()->routeIs('portal.dashboard') ? 'bg-[#E6F5F1] text-[#0A5E4E]' : 'text-slate-500 group-hover:text-slate-900' }}">
                    €
                </div>
                <span class="text-[10px] font-semibold mt-0.5 {{ request()->routeIs('portal.dashboard') ? 'text-[#0A5E4E] font-bold' : 'text-slate-500' }}">{{ __('portal.nav_accounts') }}</span>
            </a>

            <!-- 2. Virements -->
            <a href="{{ route('portal.transfers', ['locale' => app()->getLocale()]) }}" class="flex flex-col items-center justify-center flex-1 py-1 group transition">
                <div class="size-8 rounded-full flex items-center justify-center text-sm font-bold {{ request()->routeIs('portal.transfers*') ? 'bg-[#E6F5F1] text-[#0A5E4E]' : 'text-slate-500 group-hover:text-slate-900' }}">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
                <span class="text-[10px] font-semibold mt-0.5 {{ request()->routeIs('portal.transfers*') ? 'text-[#0A5E4E] font-bold' : 'text-slate-500' }}">{{ __('portal.nav_transfers') }}</span>
            </a>

            <!-- 3. Cartes / RIB -->
            <a href="{{ route('portal.documents', ['locale' => app()->getLocale()]) }}" class="flex flex-col items-center justify-center flex-1 py-1 group transition">
                <div class="size-8 rounded-full flex items-center justify-center text-sm font-bold {{ request()->routeIs('portal.documents*') ? 'bg-[#E6F5F1] text-[#0A5E4E]' : 'text-slate-500 group-hover:text-slate-900' }}">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="2" y="5" width="20" height="14" rx="2"/>
                        <line x1="2" y1="10" x2="22" y2="10"/>
                    </svg>
                </div>
                <span class="text-[10px] font-semibold mt-0.5 {{ request()->routeIs('portal.documents*') ? 'text-[#0A5E4E] font-bold' : 'text-slate-500' }}">{{ __('portal.nav_cards') }}</span>
            </a>

            <!-- 4. Souscrire -->
            <a href="{{ route('portal.loans', ['locale' => app()->getLocale()]) }}" class="flex flex-col items-center justify-center flex-1 py-1 group transition">
                <div class="size-8 rounded-full flex items-center justify-center text-sm font-bold {{ request()->routeIs('portal.loans*') ? 'bg-[#E6F5F1] text-[#0A5E4E]' : 'text-slate-500 group-hover:text-slate-900' }}">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <span class="text-[10px] font-semibold mt-0.5 {{ request()->routeIs('portal.loans*') ? 'text-[#0A5E4E] font-bold' : 'text-slate-500' }}">{{ __('portal.nav_loans') }}</span>
            </a>

            <!-- 5. Plus / Profil -->
            <a href="{{ route('portal.profile', ['locale' => app()->getLocale()]) }}" class="flex flex-col items-center justify-center flex-1 py-1 group transition">
                <div class="size-8 rounded-full flex items-center justify-center text-sm font-bold {{ request()->routeIs('portal.profile*') ? 'bg-[#E6F5F1] text-[#0A5E4E]' : 'text-slate-500 group-hover:text-slate-900' }}">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <line x1="4" y1="6" x2="20" y2="6"/>
                        <line x1="4" y1="12" x2="20" y2="12"/>
                        <line x1="4" y1="18" x2="20" y2="18"/>
                    </svg>
                </div>
                <span class="text-[10px] font-semibold mt-0.5 {{ request()->routeIs('portal.profile*') ? 'text-[#0A5E4E] font-bold' : 'text-slate-500' }}">{{ __('portal.nav_more') }}</span>
            </a>

            <!-- Floating Assistance button -->
            <a href="{{ route('portal.support', ['locale' => app()->getLocale()]) }}" class="ml-1 shrink-0 p-2 rounded-xl bg-gradient-to-r from-[#0FA57E] to-[#0A5E4E] text-white shadow-sm flex flex-col items-center justify-center size-9" title="{{ __('portal.nav_help') }}">
                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
            </a>

        </div>
    </nav>

</body>
</html>
