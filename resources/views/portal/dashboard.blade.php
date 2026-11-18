<x-portal-layout>
    <x-slot name="title">{{ __('portal.all_accounts') }}</x-slot>
    <x-slot name="pageHeading">{{ __('portal.all_accounts') }}</x-slot>

    <!-- Search Bar Pill -->
    <div class="mb-6 max-w-md">
        <form method="GET" action="{{ route('portal.dashboard', ['locale' => app()->getLocale()]) }}" class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" name="q" value="{{ $search ?? '' }}"
                placeholder="{{ __('portal.search_placeholder') }}"
                style="font-size: 16px !important;"
                class="w-full pl-11 pr-10 py-2.5 bg-white border border-slate-200 rounded-full text-sm text-[#0B1F3A] placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0FA57E] shadow-sm transition">
            @if(!empty($search))
                <a href="{{ route('portal.dashboard', ['locale' => app()->getLocale()]) }}" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </a>
            @endif
        </form>
    </div>

    <!-- Responsive Grid: 2 columns on desktop (8/4), 1 column on mobile -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">

        <!-- Main Column (Left on Desktop: 8 cols) -->
        <div class="lg:col-span-8 space-y-6">

            <!-- Section 1: Comptes courants / Cuentas corrientes -->
            <div class="space-y-4">
                <div class="flex items-center justify-between px-1">
                    <h2 class="text-base sm:text-lg font-bold text-[#0B1F3A] tracking-tight">
                        {{ __('portal.checking_accounts') }}
                    </h2>
                    <span class="text-xs font-semibold text-[#0A5E4E] bg-[#E6F5F1] px-3 py-1 rounded-full border border-[#0FA57E]/20">
                        {{ $client->country }} · {{ $client->currency }}
                    </span>
                </div>

                <!-- Card 1: Compte individuel / Cuenta individual principal -->
                <div class="bg-white rounded-2xl p-6 sm:p-7 shadow-[0_2px_16px_rgba(11,31,58,0.05)] border border-slate-200/80 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-sm sm:text-base font-bold text-[#0B1F3A]">{{ __('portal.individual_account') }}</div>
                            <div class="inline-flex items-center gap-2 mt-1 px-2.5 py-0.5 rounded-lg bg-slate-50 border border-slate-200/60 font-mono text-xs text-slate-500">
                                <span>{{ $client->account_number }}</span>
                                <span class="text-slate-300">|</span>
                                <span>IBAN {{ substr($client->iban, 0, 4) }} •••• {{ substr($client->iban, -4) }}</span>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $client->isActive() ? 'bg-[#E6F5F1] text-[#0A5E4E] border border-[#0FA57E]/20' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                            <span class="size-2 rounded-full {{ $client->isActive() ? 'bg-[#0FA57E]' : 'bg-amber-500' }}"></span>
                            <span>{{ $client->isActive() ? __('portal.verified_account') : 'Verification' }}</span>
                        </span>
                    </div>

                    <div class="mt-5">
                        <div class="text-xs font-semibold text-[#5B6B82] uppercase tracking-wider">{{ __('portal.available_balance') }}</div>
                        <div class="text-3xl sm:text-4xl font-extrabold text-[#0B1F3A] tracking-tight font-display mt-1">
                            {{ $client->formattedBalance() }}
                        </div>
                        @if($client->authorized_overdraft > 0)
                            <div class="text-xs text-slate-400 mt-1">
                                {{ __('portal.authorized_overdraft') }} : {{ $client->formatMoney($client->authorized_overdraft) }}
                            </div>
                        @endif
                    </div>

                    <!-- Actions bar -->
                    <div class="mt-6 pt-5 border-t border-slate-100 flex flex-wrap items-center gap-3">
                        <a href="{{ route('portal.transfers', ['locale' => app()->getLocale()]) }}"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-[#F26B1D] to-[#D04A0A] hover:from-[#E05B0D] hover:to-[#B93E07] text-white font-bold text-xs sm:text-sm shadow-sm transition active:scale-[0.99]">
                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span>{{ __('portal.make_transfer') }}</span>
                        </a>

                        <a href="{{ route('portal.documents.rib', ['locale' => app()->getLocale()]) }}"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#E6F5F1] hover:bg-[#d4ede6] text-[#0A5E4E] font-bold text-xs sm:text-sm border border-[#0FA57E]/20 transition">
                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>{{ __('portal.download_rib') }}</span>
                        </a>
                    </div>
                </div>

                <!-- Card 2: Compte joint & Réserve / Cuenta de ahorro -->
                <div class="bg-white rounded-2xl p-6 sm:p-7 shadow-[0_2px_16px_rgba(11,31,58,0.05)] border border-slate-200/80 hover:shadow-md transition">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-sm sm:text-base font-bold text-[#0B1F3A]">{{ __('portal.savings_account') }}</div>
                            <div class="text-xs text-slate-400 font-mono mt-0.5">
                                {{ $client->account_number }}-EP
                            </div>
                        </div>
                        <span class="text-xs font-bold text-[#0A5E4E] bg-[#E6F5F1] px-3 py-1 rounded-full border border-[#0FA57E]/20">
                            {{ __('portal.immediate_availability') }}
                        </span>
                    </div>

                    <div class="mt-4">
                        <div class="text-xs font-semibold text-[#5B6B82] uppercase tracking-wider">{{ __('portal.savings_subtitle') }}</div>
                        <div class="text-2xl sm:text-3xl font-extrabold text-[#0A5E4E] tracking-tight font-display mt-1">
                            {{ $client->formattedSavingsBalance() }}
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between">
                        <a href="{{ route('portal.loans', ['locale' => app()->getLocale()]) }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-[#0FA57E] hover:text-[#0A5E4E] transition">
                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            <span>{{ __('portal.feed_savings') }}</span>
                        </a>
                        <span class="text-slate-400 text-xs">{{ __('portal.free_management') }}</span>
                    </div>
                </div>
            </div>

            <!-- Section 2: Crédits & Prêts / Préstamos -->
            <div class="space-y-4">
                <div class="flex items-center justify-between px-1">
                    <h2 class="text-base sm:text-lg font-bold text-[#0B1F3A] tracking-tight">
                        {{ __('portal.credits_and_loans') }}
                    </h2>
                </div>

                <div class="bg-white rounded-2xl p-6 sm:p-7 shadow-[0_2px_16px_rgba(11,31,58,0.05)] border border-slate-200/80 hover:shadow-md transition">
                    @if($loan)
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="text-sm sm:text-base font-bold text-[#0B1F3A]">{{ $loan->title }}</div>
                                <div class="text-xs text-slate-400 font-mono mt-0.5">
                                    Ref. {{ $loan->reference }} · {{ $loan->term_months }} {{ trans('admin.months', [], app()->getLocale()) ?: 'meses' }}
                                </div>
                            </div>
                            <span class="text-xs font-bold text-[#0A5E4E] bg-[#E6F5F1] px-3 py-1 rounded-full border border-[#0FA57E]/20">
                                {{ __('portal.active_loan') }}
                            </span>
                        </div>

                        <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/60">
                                <div class="text-xs text-[#5B6B82] font-semibold uppercase">{{ __('portal.remaining_capital') }}</div>
                                <div class="text-2xl sm:text-3xl font-extrabold text-[#0B1F3A] tracking-tight font-display mt-0.5">
                                    {{ $loan->formattedRemaining() }}
                                </div>
                            </div>
                            <div class="p-3.5 bg-[#E6F5F1]/50 rounded-xl border border-[#0FA57E]/20">
                                <div class="text-xs text-[#0A5E4E] font-semibold uppercase">{{ __('portal.monthly_payment') }}</div>
                                <div class="text-2xl sm:text-3xl font-extrabold text-[#0A5E4E] tracking-tight font-display mt-0.5">
                                    {{ $loan->formattedMonthly() }} <span class="text-xs font-normal text-slate-500">{{ __('portal.per_month') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between text-xs sm:text-sm font-semibold">
                            <a href="{{ route('portal.loans', ['locale' => app()->getLocale()]) }}" class="text-[#F26B1D] hover:text-[#D04A0A] flex items-center gap-1.5 transition">
                                <span>{{ __('portal.consult_schedule') }}</span>
                                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </a>
                            @if($loan->next_payment_date)
                                <span class="text-slate-400 text-xs">{{ __('portal.next_due_date') }} : {{ $loan->next_payment_date->format('d/m/Y') }}</span>
                            @endif
                        </div>
                    @else
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="text-sm sm:text-base font-bold text-[#0B1F3A]">{{ __('portal.credits_and_loans') }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">
                                    {{ __('portal.preapproved_credit') }}
                                </div>
                            </div>
                            <span class="text-xs font-bold text-[#0A5E4E] bg-[#E6F5F1] px-3 py-1 rounded-full border border-[#0FA57E]/20">
                                {{ __('portal.immediate_availability') }}
                            </span>
                        </div>

                        <div class="mt-4">
                            <div class="text-2xl sm:text-3xl font-extrabold text-[#0B1F3A] tracking-tight font-display">
                                {{ $client->formatMoney(15000) }}
                            </div>
                            <div class="text-xs text-slate-500 mt-1">
                                {{ __('portal.loan_interest_desc') }}
                            </div>
                        </div>

                        <div class="mt-5 pt-4 border-t border-slate-100">
                            <a href="{{ route('portal.loans', ['locale' => app()->getLocale()]) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-[#F26B1D] to-[#D04A0A] hover:from-[#E05B0D] hover:to-[#B93E07] text-white font-bold text-xs sm:text-sm transition shadow-sm">
                                <span>{{ __('portal.activate_loan') }}</span>
                                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Section 3: Dernières opérations / Últimas operaciones -->
            <div class="space-y-4">
                <div class="flex items-center justify-between px-1">
                    <h2 class="text-base sm:text-lg font-bold text-[#0B1F3A] tracking-tight">
                        {{ __('portal.recent_transactions') }}
                    </h2>
                    <a href="{{ route('portal.transfers', ['locale' => app()->getLocale()]) }}" class="text-xs font-bold text-[#0FA57E] hover:text-[#0A5E4E] transition">
                        {{ __('portal.view_all_history') }} →
                    </a>
                </div>

                <div class="bg-white rounded-2xl shadow-[0_2px_16px_rgba(11,31,58,0.05)] border border-slate-200/80 divide-y divide-slate-100 overflow-hidden">
                    @forelse($transactions as $tx)
                        <div class="p-4 sm:p-5 flex items-center justify-between hover:bg-slate-50/70 transition">
                            <div class="flex items-center gap-3.5">
                                <div class="size-10 rounded-xl flex items-center justify-center shrink-0 {{ in_array($tx->type, ['transfer_out', 'fee']) ? 'bg-slate-100 text-slate-600' : 'bg-[#E6F5F1] text-[#0A5E4E]' }}">
                                    @if(in_array($tx->type, ['transfer_out', 'fee']))
                                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                    @else
                                        <svg class="size-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-xs sm:text-sm font-bold text-[#0B1F3A]">
                                        {{ $tx->target_bank_name ?: ($tx->type === 'transfer_in' ? __('portal.transfer_received') : __('portal.transfer_sent')) }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        {{ $tx->target_account_holder ?: $tx->description }} · {{ $tx->created_at->format('d/m/Y') }}
                                    </div>
                                </div>
                            </div>

                            <div class="text-right shrink-0">
                                <div class="text-xs sm:text-sm font-extrabold font-display {{ in_array($tx->type, ['transfer_out', 'fee']) ? 'text-[#0B1F3A]' : 'text-[#0FA57E]' }}">
                                    {{ $tx->formattedAmount() }}
                                </div>
                                <div class="mt-0.5">
                                    @if($tx->isPending())
                                        <span class="text-[10px] font-bold text-amber-800 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">
                                            {{ __('portal.tab_pending') }}
                                        </span>
                                    @elseif($tx->isCancelled())
                                        <span class="text-[10px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-200">
                                            {{ __('portal.tab_cancelled') }}
                                        </span>
                                    @else
                                        <span class="text-[10px] font-bold text-[#0A5E4E] bg-[#E6F5F1] px-2 py-0.5 rounded-full">
                                            {{ __('portal.status_paid') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-xs text-slate-400">
                            {{ __('portal.no_transactions') }}
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Sidebar Column (Right on Desktop: 4 cols) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Quick Action Shortcuts -->
            <div class="bg-white rounded-2xl p-6 shadow-[0_2px_16px_rgba(11,31,58,0.05)] border border-slate-200/80">
                <h3 class="text-xs font-bold uppercase tracking-wider text-[#5B6B82] mb-3">
                    {{ __('portal.quick_access') }}
                </h3>

                <div class="space-y-1.5">
                    <a href="{{ route('portal.transfers', ['locale' => app()->getLocale()]) }}"
                        class="flex items-center gap-3 p-3 rounded-xl hover:bg-[#E6F5F1] text-slate-700 hover:text-[#0A5E4E] transition group">
                        <div class="size-8 rounded-lg bg-slate-100 group-hover:bg-[#0FA57E] group-hover:text-white flex items-center justify-center transition">
                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        </div>
                        <div class="text-xs font-bold">{{ __('portal.make_transfer') }}</div>
                    </a>

                    <a href="{{ route('portal.documents.rib', ['locale' => app()->getLocale()]) }}"
                        class="flex items-center gap-3 p-3 rounded-xl hover:bg-[#E6F5F1] text-slate-700 hover:text-[#0A5E4E] transition group">
                        <div class="size-8 rounded-lg bg-slate-100 group-hover:bg-[#0FA57E] group-hover:text-white flex items-center justify-center transition">
                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="text-xs font-bold">{{ __('portal.download_rib') }}</div>
                    </a>

                    <a href="{{ route('portal.loans', ['locale' => app()->getLocale()]) }}"
                        class="flex items-center gap-3 p-3 rounded-xl hover:bg-[#E6F5F1] text-slate-700 hover:text-[#0A5E4E] transition group">
                        <div class="size-8 rounded-lg bg-slate-100 group-hover:bg-[#0FA57E] group-hover:text-white flex items-center justify-center transition">
                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="text-xs font-bold">{{ __('portal.request_loan') }}</div>
                    </a>

                    <a href="{{ route('portal.support', ['locale' => app()->getLocale()]) }}"
                        class="flex items-center gap-3 p-3 rounded-xl hover:bg-[#E6F5F1] text-slate-700 hover:text-[#0A5E4E] transition group">
                        <div class="size-8 rounded-lg bg-slate-100 group-hover:bg-[#0FA57E] group-hover:text-white flex items-center justify-center transition">
                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        </div>
                        <div class="text-xs font-bold">{{ __('portal.support_messaging') }}</div>
                    </a>
                </div>
            </div>

            <!-- Dedicated Advisor Card (Clean White with Emerald Accent) -->
            <div class="bg-white rounded-2xl p-6 shadow-[0_2px_16px_rgba(11,31,58,0.05)] border-l-4 border-[#0FA57E] border-y border-r border-slate-200/80">
                <div class="flex items-center gap-3.5 mb-4">
                    <div class="size-11 rounded-xl bg-[#E6F5F1] text-[#0A5E4E] font-extrabold text-sm flex items-center justify-center border border-[#0FA57E]/20">
                        VM
                    </div>
                    <div>
                        <div class="text-[10px] uppercase font-bold text-[#0FA57E] tracking-wider">{{ __('portal.dedicated_advisor') }}</div>
                        <div class="text-sm font-bold text-[#0B1F3A]">{{ __('portal.management_firm') }}</div>
                        <div class="text-xs text-slate-400">{{ __('portal.madrid_central') }}</div>
                    </div>
                </div>

                <div class="space-y-2 text-xs text-slate-600 mb-4">
                    <div class="flex items-center gap-2">
                        <svg class="size-4 text-[#0FA57E]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>contact@venatomar.com</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="size-4 text-[#0FA57E]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ __('portal.advisor_hours') }}</span>
                    </div>
                </div>

                <a href="{{ route('portal.support', ['locale' => app()->getLocale()]) }}" class="w-full py-2.5 bg-[#E6F5F1] hover:bg-[#d4ede6] text-[#0A5E4E] font-bold text-xs rounded-xl transition flex items-center justify-center gap-1.5 border border-[#0FA57E]/20">
                    <span>{{ __('portal.write_advisor') }}</span>
                </a>
            </div>

            <!-- Guarantee Box -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 text-xs text-slate-500 flex items-start gap-3 shadow-sm">
                <svg class="size-5 text-[#0FA57E] shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <div>
                    <strong class="text-[#0B1F3A] font-semibold">{{ __('portal.deposit_guarantee') }} :</strong> {{ __('portal.deposit_guarantee_text') }}
                </div>
            </div>

        </div>

    </div>

</x-portal-layout>
