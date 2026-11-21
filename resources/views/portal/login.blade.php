<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full bg-[#F5F7FA]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>{{ __('account.login_title') }} · Venatomar Banking</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased bg-[#F5F7FA] text-[#24324A] flex items-center justify-center p-4">

<div class="w-full max-w-[400px]">
    
    <!-- Header Branding -->
    <div class="text-center mb-6">
        <a href="/" class="inline-flex items-center gap-2 mb-3">
            <img src="{{ asset('images/logo.svg') }}" alt="Venatomar" class="size-9">
            <span class="font-display font-extrabold text-xl text-[#0B1F3A] tracking-tight">VENATOMAR</span>
        </a>
        <h1 class="font-display text-2xl font-bold text-[#0B1F3A] tracking-tight">{{ __('account.login_title') }}</h1>
        <p class="text-xs sm:text-sm text-[#5B6B82] mt-1">{{ __('account.login_subtitle') }}</p>
    </div>

    <!-- Login Card -->
    <div class="bg-white rounded-2xl p-6 sm:p-7 shadow-[0_2px_16px_rgba(11,31,58,0.06)]">
        
        @if(session('status'))
            <div class="mb-4 p-3 bg-[#E6F5F1] rounded-xl text-[#0A5E4E] text-xs font-medium flex items-center gap-2">
                <svg class="size-4 shrink-0 text-[#0FA57E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if($errors->any() || session('account_error'))
            <div class="mb-4 p-3 bg-rose-50 rounded-xl text-rose-700 text-xs font-medium flex items-center gap-2">
                <svg class="size-4 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ $errors->first() ?: session('account_error') }}</span>
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('portal.login.submit') }}" class="space-y-4">
            @csrf

            <div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    placeholder="Correo electrónico"
                    style="font-size: 16px !important;"
                    class="w-full h-12 px-4 bg-[#F1F3F6] border border-slate-200/50 rounded-lg text-[16px] text-[#0B1F3A] placeholder:text-[#5B6B82] focus:bg-white focus:border-[#0FA57E] focus:ring-1 focus:ring-[#0FA57E] focus:outline-none transition-colors">
            </div>

            <div>
                <input id="password" type="password" name="password" required
                    placeholder="Contraseña"
                    style="font-size: 16px !important;"
                    class="w-full h-12 px-4 bg-[#F1F3F6] border border-slate-200/50 rounded-lg text-[16px] text-[#0B1F3A] placeholder:text-[#5B6B82] focus:bg-white focus:border-[#0FA57E] focus:ring-1 focus:ring-[#0FA57E] focus:outline-none transition-colors">
            </div>

            <button type="submit"
                class="w-full h-12 bg-gradient-to-r from-[#F26B1D] to-[#D04A0A] hover:from-[#E05B0D] hover:to-[#B93E07] text-white font-bold text-base rounded-xl shadow-[0_4px_14px_rgba(242,107,29,0.3)] transition duration-150 flex items-center justify-center active:scale-[0.99]">
                {{ __('account.login_title') }}
            </button>

            <div class="rounded-md border-l-4 border-[#1D72C9] bg-[#EEF5FD] px-4 py-3 text-xs sm:text-sm text-[#1B4F86] leading-relaxed">
                Accede con tu correo electrónico y tu contraseña para gestionar tu cuenta.
            </div>
        </form>

    </div>

</div>

</body>
</html>
