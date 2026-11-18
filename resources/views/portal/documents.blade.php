<x-portal-layout>
    <x-slot name="title">{{ __('portal.docs_title') }}</x-slot>
    <x-slot name="pageHeading">{{ __('portal.docs_title') }}</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 mb-8">

        <!-- Left / Bank Card Showcase (5 cols) -->
        <div class="lg:col-span-5">
            <div class="bg-white rounded-2xl p-6 shadow-[0_2px_16px_rgba(11,31,58,0.05)] border border-slate-200/80">
                <h2 class="text-xs font-bold uppercase tracking-wider text-[#5B6B82] mb-4">
                    {{ __('portal.virtual_card') }}
                </h2>

                <!-- Premium Venatomar Debit Card -->
                <div class="relative w-full aspect-[1.586/1] rounded-2xl bg-gradient-to-tr from-[#0B1F3A] via-[#0A4B40] to-[#0FA57E] p-5 sm:p-6 text-white shadow-xl shadow-slate-900/15 overflow-hidden border border-white/20">
                    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>

                    <div class="relative z-10 flex flex-col justify-between h-full">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <img src="{{ asset('images/logo.svg') }}" alt="Venatomar" class="size-6 brightness-200">
                                <span class="font-display font-extrabold text-sm tracking-tight">VENATOMAR</span>
                            </div>
                            <span class="text-[9px] uppercase font-bold tracking-widest text-[#E6F5F1] px-2 py-0.5 rounded bg-white/20">
                                {{ __('portal.platinum_card') }}
                            </span>
                        </div>

                        <!-- Chip -->
                        <div class="flex items-center gap-3 my-auto">
                            <div class="w-10 h-7 rounded-md bg-gradient-to-br from-amber-200 via-amber-400 to-amber-600 border border-amber-300 shadow-sm relative overflow-hidden flex items-center justify-center">
                                <div class="w-full h-[1px] bg-amber-700/40 absolute top-2"></div>
                                <div class="w-full h-[1px] bg-amber-700/40 absolute bottom-2"></div>
                                <div class="h-full w-[1px] bg-amber-700/40 absolute left-3"></div>
                            </div>
                            <!-- Contactless wave -->
                            <svg class="size-5 text-emerald-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.111 16.404a5.5 5.5 0 010-7.778M11.646 19.94a10.5 10.5 0 000-14.85M15.182 23.475a15.5 15.5 0 000-21.95"/>
                            </svg>
                        </div>

                        <div>
                            <div class="font-mono text-base sm:text-lg tracking-widest font-bold">
                                •••• •••• •••• {{ substr($client->account_number, -4) }}
                            </div>

                            <div class="flex items-end justify-between mt-2 text-xs">
                                <div>
                                    <div class="text-[8.5px] uppercase tracking-wider text-emerald-200 font-semibold">{{ __('portal.card_holder') }}</div>
                                    <div class="font-bold tracking-wide uppercase">{{ $client->fullName() }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-[8.5px] uppercase tracking-wider text-emerald-200 font-semibold">{{ __('portal.card_expires') }}</div>
                                    <div class="font-bold font-mono">12/28</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span>{{ __('portal.card_status_prefix') }} <strong class="text-[#0FA57E]">{{ __('portal.card_active') }}</strong></span>
                    <span>{{ __('portal.card_contactless') }}</span>
                </div>
            </div>
        </div>

        <!-- Right / Official Downloads (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl p-6 sm:p-7 shadow-[0_2px_16px_rgba(11,31,58,0.05)] border border-slate-200/80">
            <h2 class="text-base sm:text-lg font-bold text-[#0B1F3A] mb-1">
                {{ __('portal.download_official_docs') }}
            </h2>
            <p class="text-xs text-slate-500 mb-5">{{ __('portal.instant_pdf_desc') }}</p>

            <div class="space-y-3">
                <!-- 1. RIB / IBAN PDF -->
                <a href="{{ route('portal.documents.rib', ['locale' => app()->getLocale()]) }}"
                    class="flex items-center justify-between p-4 sm:p-4.5 rounded-xl border border-slate-200 hover:border-[#0FA57E] hover:bg-[#E6F5F1]/30 transition group">
                    <div class="flex items-center gap-3.5">
                        <div class="size-11 rounded-xl bg-[#E6F5F1] text-[#0A5E4E] flex items-center justify-center shrink-0 border border-[#0FA57E]/20">
                            <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-[#0B1F3A] group-hover:text-[#0FA57E] transition">
                                {{ __('portal.doc_rib_title') }}
                            </div>
                            <div class="text-xs text-slate-400 mt-0.5">{{ __('portal.doc_rib_desc') }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs font-bold text-[#0FA57E] shrink-0">
                        <span>{{ __('portal.btn_download_pdf') }}</span>
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </div>
                </a>

                <!-- 2. Relevé de Compte Mensuel PDF -->
                <a href="{{ route('portal.documents.statement', ['locale' => app()->getLocale()]) }}"
                    class="flex items-center justify-between p-4 sm:p-4.5 rounded-xl border border-slate-200 hover:border-[#0FA57E] hover:bg-[#E6F5F1]/30 transition group">
                    <div class="flex items-center gap-3.5">
                        <div class="size-11 rounded-xl bg-[#E6F5F1] text-[#0A5E4E] flex items-center justify-center shrink-0 border border-[#0FA57E]/20">
                            <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-[#0B1F3A] group-hover:text-[#0FA57E] transition">
                                {{ __('portal.doc_statement_title') }}
                            </div>
                            <div class="text-xs text-slate-400 mt-0.5">{{ __('portal.doc_statement_desc') }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs font-bold text-[#0FA57E] shrink-0">
                        <span>{{ __('portal.btn_download_pdf') }}</span>
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </div>
                </a>

                <!-- 3. Attestation de Solde Bancaire PDF -->
                <a href="{{ route('portal.documents.attestation', ['locale' => app()->getLocale()]) }}"
                    class="flex items-center justify-between p-4 sm:p-4.5 rounded-xl border border-slate-200 hover:border-[#0FA57E] hover:bg-[#E6F5F1]/30 transition group">
                    <div class="flex items-center gap-3.5">
                        <div class="size-11 rounded-xl bg-[#E6F5F1] text-[#0A5E4E] flex items-center justify-center shrink-0 border border-[#0FA57E]/20">
                            <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-[#0B1F3A] group-hover:text-[#0FA57E] transition">
                                {{ __('portal.doc_certificate_title') }}
                            </div>
                            <div class="text-xs text-slate-400 mt-0.5">{{ __('portal.doc_certificate_desc') }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs font-bold text-[#0FA57E] shrink-0">
                        <span>{{ __('portal.btn_download_pdf') }}</span>
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </div>
                </a>
            </div>

            <!-- IBAN / BIC Detail table -->
            <div class="mt-6 pt-5 border-t border-slate-100">
                <h3 class="text-xs font-bold uppercase tracking-wider text-[#5B6B82] mb-3">
                    {{ __('portal.bank_coordinates') }}
                </h3>

                <div class="space-y-2.5 text-xs sm:text-sm">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <span class="text-slate-500">{{ __('portal.iban_label') }}</span>
                        <span class="font-mono font-bold text-[#0B1F3A]">{{ $client->iban }}</span>
                    </div>

                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <span class="text-slate-500">{{ __('portal.bic_label') }}</span>
                        <span class="font-mono font-bold text-[#0B1F3A]">{{ $client->bic }}</span>
                    </div>

                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <span class="text-slate-500">{{ __('portal.card_holder') }}</span>
                        <span class="font-bold text-[#0B1F3A]">{{ $client->fullName() }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">{{ __('portal.agency_label') }}</span>
                        <span class="font-bold text-[#0B1F3A]">Venatomar Financial Services (Europe)</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</x-portal-layout>
