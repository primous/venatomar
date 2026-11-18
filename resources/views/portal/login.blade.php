<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Connexion Espace Client · Venatomar Banking</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .login-bg {
            background-color: #0b0c10;
            background-image: url("{{ asset('images/paris-header.svg') }}");
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="h-full font-sans antialiased bg-slate-950 text-slate-800">

<div class="min-h-screen flex items-center justify-center p-4 login-bg">
    <div class="w-full max-w-md bg-white/95 backdrop-blur-xl rounded-3xl shadow-[0_20px_60px_rgba(0,0,0,0.6)] border border-white/20 p-6 sm:p-8 relative overflow-hidden">
        
        <!-- Header Branding -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center size-14 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 mb-3 shadow-inner">
                <svg class="size-8" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2L2 7l10 5 10-5-10-5zm0 8.5L4 11v6l8 4 8-4v-6l-8 4.5z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Espace Client Sécurisé</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Accédez à vos comptes, virements et prêts en toute sérénité</p>
        </div>

        @if(session('status'))
            <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-xs font-medium">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-xs font-medium">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('portal.login.submit') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Identifiant / Adresse Email
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/>
                        </svg>
                    </div>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                        placeholder="client@domaine.com">
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Mot de passe secret
                    </label>
                    <span class="text-[11px] text-slate-400 font-medium">Clavier sécurisé</span>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <input id="password" type="password" name="password" required
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                        placeholder="••••••••••••">
                </div>
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <span>Mémoriser cet appareil</span>
                </label>
                <a href="{{ route('portal.support') }}" class="text-emerald-700 hover:text-emerald-800 font-medium">
                    Assistance connexion
                </a>
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="w-full py-3 px-4 bg-gradient-to-r from-emerald-600 to-teal-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-emerald-700/25 hover:from-emerald-700 hover:to-teal-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition active:scale-[0.99] flex items-center justify-center gap-2">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    <span>Se connecter à mes comptes</span>
                </button>
            </div>

            <!-- Biometric Login Button Simulation -->
            <div class="pt-2 text-center">
                <button type="button" onclick="alert('Authentification biométrique disponible sur smartphone via l\'application Venatomar.')"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-full border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-semibold transition">
                    <svg class="size-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                    </svg>
                    <span>Connexion par Biométrie (Face ID / Empreinte)</span>
                </button>
            </div>
        </form>

        <!-- Compliance & Security Footnote -->
        <div class="mt-6 pt-5 border-t border-slate-100 text-center">
            <p class="text-[11px] text-slate-400 leading-relaxed">
                🔒 Espace certifié PCI-DSS et chiffré AES-256.<br>
                L'ouverture de compte est exclusivement effectuée par les conseillers bancaires Venatomar.
            </p>
        </div>

    </div>
</div>

</body>
</html>
