@php
    $actionRoute = request()->routeIs('solicitar*')
        ? route('solicitar.account.client.login', ['locale' => app()->getLocale()])
        : route('account.client.login', ['locale' => app()->getLocale()]);
@endphp
<x-layouts::app :title="trans('account.login_title') . ' · ' . config('venatomar.brand')" noindex>
    <section class="py-12 md:py-20 bg-[#F5F7FA] min-h-[calc(100vh-4rem)] flex items-center justify-center">
        <div class="w-full max-w-[390px] px-4">
            
            <!-- Header matching reference image -->
            <div class="text-center mb-6">
                <h1 class="font-display text-2xl sm:text-[1.625rem] font-bold text-[#0B1F3A] tracking-tight">
                    {{ __('account.login_title') }}
                </h1>
                <p class="mt-2 text-sm text-[#5B6B82]">
                    {{ __('account.login_subtitle') }}
                </p>
            </div>

            <!-- Card matching reference image -->
            <div class="rounded-2xl bg-white p-6 sm:p-7 shadow-[0_2px_16px_rgba(11,31,58,0.06)]">
                
                @if (session('status'))
                    <div role="status" class="mb-4 rounded-lg bg-[#E6F5F1] p-3 text-xs sm:text-sm font-medium text-[#0A5E4E] flex items-center gap-2">
                        <svg class="size-4 shrink-0 text-[#0FA57E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if ($errors->any() || session('account_error'))
                    <div role="alert" class="mb-4 rounded-lg bg-rose-50 p-3 text-xs sm:text-sm font-medium text-rose-700 flex items-center gap-2">
                        <svg class="size-4 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ $errors->first() ?: session('account_error') }}</span>
                    </div>
                @endif

                <!-- Form: Mail & Password with anti-zoom (font-size: 16px) -->
                <form method="POST" action="{{ $actionRoute }}" class="space-y-4">
                    @csrf

                    <!-- Input 1: Correo electrónico -->
                    <div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            placeholder="Correo electrónico"
                            style="font-size: 16px !important;"
                            class="w-full h-12 px-4 bg-[#F1F3F6] border border-slate-200/50 rounded-lg text-[16px] text-[#0B1F3A] placeholder:text-[#5B6B82] focus:bg-white focus:border-[#0FA57E] focus:ring-1 focus:ring-[#0FA57E] focus:outline-none transition-colors">
                    </div>

                    <!-- Input 2: Contraseña (Mail & Mot de passe) -->
                    <div>
                        <input id="password" type="password" name="password" required
                            placeholder="Contraseña"
                            style="font-size: 16px !important;"
                            class="w-full h-12 px-4 bg-[#F1F3F6] border border-slate-200/50 rounded-lg text-[16px] text-[#0B1F3A] placeholder:text-[#5B6B82] focus:bg-white focus:border-[#0FA57E] focus:ring-1 focus:ring-[#0FA57E] focus:outline-none transition-colors">
                    </div>

                    <!-- Button: Vibrant Orange CTA matching reference image -->
                    <button type="submit"
                        class="w-full h-12 bg-gradient-to-r from-[#F26B1D] to-[#D04A0A] hover:from-[#E05B0D] hover:to-[#B93E07] text-white font-bold text-base rounded-xl shadow-[0_4px_14px_rgba(242,107,29,0.3)] transition duration-150 flex items-center justify-center">
                        {{ __('account.login_title') }}
                    </button>

                    <!-- Info Box matching reference image -->
                    <div class="rounded-md border-l-4 border-[#1D72C9] bg-[#EEF5FD] px-4 py-3 text-xs sm:text-sm text-[#1B4F86] leading-relaxed">
                        Accede con tu correo electrónico y tu contraseña para gestionar tu cuenta.
                    </div>
                </form>

            </div>

            <!-- Optional: Magic link for loan applicants without password -->
            <div x-data="{ showLink: false }" class="mt-5 text-center">
                <button type="button" @click="showLink = !showLink" class="text-xs text-slate-500 hover:text-slate-700 underline font-medium">
                    <span x-show="!showLink">¿Deseas acceder con un enlace temporal a tu solicitud?</span>
                    <span x-show="showLink">Ocultar acceso por enlace</span>
                </button>
                <div x-show="showLink" x-cloak class="mt-4 p-5 rounded-2xl bg-white border border-slate-200 text-left shadow-sm">
                    <livewire:magic-link-request :locale="app()->getLocale()" />
                </div>
            </div>

        </div>
    </section>
</x-layouts::app>
