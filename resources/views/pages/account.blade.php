<x-layouts::app :title="trans('portal.login_title') . ' · ' . config('venatomar.brand')" noindex>
    <section class="py-10 md:py-16 bg-[#F5F7FA]">
        <div class="mx-auto w-full max-w-[480px] px-4">
            
            <!-- Branding icon & title -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center size-14 rounded-2xl bg-[#E6F5F1] text-[#0FA57E] mb-3 shadow-sm border border-[#0FA57E]/30">
                    <img src="{{ asset('images/logo.svg') }}" alt="Venatomar" class="size-8">
                </div>
                <h1 class="text-center font-display text-2xl sm:text-3xl font-extrabold text-[#0B1F3A]">
                    {{ __('portal.login_title') }}
                </h1>
                <p class="mt-2 text-center text-sm text-slate-500">
                    {{ __('portal.login_subtitle') }}
                </p>
            </div>

            <!-- Login Card -->
            <div class="rounded-2xl bg-white p-6 sm:p-8 shadow-[0_4px_20px_rgba(11,31,58,0.06)] border border-slate-200/80">
                
                @if (session('status'))
                    <div role="status" class="mb-4 rounded-xl bg-[#E6F5F1] p-3.5 text-xs sm:text-sm font-medium text-[#0A5E4E] border border-[#0FA57E]/30 flex items-center gap-2">
                        <svg class="size-4 shrink-0 text-[#0FA57E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if ($errors->any() || session('account_error'))
                    <div role="alert" class="mb-4 rounded-xl bg-rose-50 p-3.5 text-xs sm:text-sm font-medium text-rose-700 border border-rose-200 flex items-center gap-2">
                        <svg class="size-4 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ $errors->first() ?: session('account_error') }}</span>
                    </div>
                @endif

                <!-- Bank Client Login Form -->
                <form method="POST" action="{{ route('account.client.login', ['locale' => app()->getLocale()]) }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-[#0B1F3A] mb-1.5">
                            {{ __('portal.email_label') }}
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/>
                                </svg>
                            </div>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                class="w-full pl-10 pr-4 py-2.5 bg-[#F5F7FA] border border-slate-200 rounded-xl text-sm font-medium text-[#0B1F3A] placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0FA57E] transition"
                                placeholder="client@domaine.com">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-[#0B1F3A]">
                                {{ __('portal.password_label') }}
                            </label>
                            <span class="text-[11px] text-slate-400 font-medium">SSL 256-bit</span>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input id="password" type="password" name="password" required
                                class="w-full pl-10 pr-4 py-2.5 bg-[#F5F7FA] border border-slate-200 rounded-xl text-sm font-medium text-[#0B1F3A] placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0FA57E] transition"
                                placeholder="••••••••••••">
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1">
                        <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                            <input type="checkbox" name="remember" class="rounded border-slate-300 text-[#0FA57E] focus:ring-[#0FA57E]">
                            <span>{{ __('portal.remember_me') }}</span>
                        </label>
                        <span class="text-slate-400 font-medium">{{ __('portal.need_help') }}</span>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="w-full py-3 px-4 bg-[#0FA57E] hover:bg-[#0E7C66] text-white font-bold text-sm rounded-xl shadow-md transition active:scale-[0.99] flex items-center justify-center gap-2">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            <span>{{ __('portal.btn_login') }}</span>
                        </button>
                    </div>

                    <!-- Biometric Button Hint -->
                    <div class="pt-2 text-center">
                        <button type="button" onclick="alert('{{ __('portal.biometric_alert') }}')"
                            class="inline-flex items-center gap-2 px-3.5 py-2 rounded-full border border-slate-200 bg-[#F5F7FA] hover:bg-slate-100 text-slate-600 text-xs font-semibold transition">
                            <svg class="size-4 text-[#0FA57E]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                            </svg>
                            <span>{{ __('portal.biometric_btn') }}</span>
                        </button>
                    </div>
                </form>

                <!-- Footnote -->
                <p class="text-[11px] text-slate-400 text-center leading-relaxed mt-6 pt-4 border-t border-slate-100">
                    {{ __('portal.security_footnote') }}
                </p>

            </div>

            <!-- Optional: Magic link for loan applicants -->
            <div x-data="{ showApplicant: false }" class="mt-6 text-center">
                <button type="button" @click="showApplicant = !showApplicant" class="text-xs text-slate-500 hover:text-slate-700 underline font-medium">
                    <span x-show="!showApplicant">Suivi de dossier de demande de prêt (Lien temporaire)</span>
                    <span x-show="showApplicant">Masquer le suivi de dossier</span>
                </button>
                <div x-show="showApplicant" x-cloak class="mt-4 p-4 rounded-xl bg-white border border-slate-200 text-left">
                    <livewire:magic-link-request :locale="app()->getLocale()" />
                </div>
            </div>

        </div>
    </section>
</x-layouts::app>
