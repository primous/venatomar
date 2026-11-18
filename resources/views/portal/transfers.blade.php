<x-portal-layout>
    <x-slot name="title">{{ __('portal.transfers_title') }}</x-slot>
    <x-slot name="pageHeading">{{ __('portal.transfers_title') }}</x-slot>

    <!-- OTP Verification Banner if transaction is currently pending -->
    @if($pendingTx)
        <div class="mb-6 p-6 bg-white rounded-2xl shadow-md border-2 border-[#F26B1D] relative overflow-hidden">
            <div class="flex items-start gap-4">
                <div class="size-11 rounded-xl bg-[#FFF1E8] text-[#F26B1D] flex items-center justify-center shrink-0">
                    <svg class="size-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <h3 class="text-base font-extrabold text-[#0B1F3A]">
                            {{ __('portal.otp_pending_title') }} (Réf: {{ $pendingTx->reference }})
                        </h3>
                        <span class="inline-block text-xs font-bold text-[#B23F08] bg-[#FFF1E8] px-3 py-1 rounded-full border border-[#F26B1D]/30 w-fit">
                            {{ __('portal.otp_required_badge') }}
                        </span>
                    </div>

                    <div class="mt-3 text-xs text-slate-600 space-y-1 bg-slate-50 p-3.5 rounded-xl border border-slate-200/80">
                        <div>{{ __('portal.amount_to_send') }} : <strong class="text-[#0B1F3A] font-bold text-sm">{{ $pendingTx->formattedAmount() }}</strong></div>
                        <div>{{ __('portal.destination_bank') }} : <strong class="text-[#0B1F3A]">{{ $pendingTx->target_bank_name }}</strong></div>
                        <div>{{ __('portal.beneficiary_name') }} : <strong>{{ $pendingTx->target_account_holder }}</strong> ({{ $pendingTx->target_account_number }})</div>
                    </div>

                    <!-- Security Notice -->
                    <div class="mt-3 text-xs leading-relaxed">
                        @if($client->can_receive_otp_email)
                            <div class="text-[#0A5E4E] font-semibold flex items-center gap-1.5">
                                <svg class="size-4 text-[#0FA57E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span>{{ __('portal.otp_email_notice') }} {{ $client->email }}</span>
                            </div>
                        @else
                            <div class="text-[#B23F08] font-bold flex items-center gap-1.5">
                                <svg class="size-4 text-[#F26B1D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>{{ __('portal.otp_admin_notice') }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- OTP Code Entry Form with anti-zoom (font-size: 16px) -->
                    <form method="POST" action="{{ route('portal.transfers.verify-otp', ['locale' => app()->getLocale(), 'transaction' => $pendingTx]) }}" class="mt-4 flex flex-col sm:flex-row items-center gap-3">
                        @csrf
                        <input type="text" name="otp_code" maxlength="6" pattern="[0-9]{6}" required
                            placeholder="Code (ex: 492810)"
                            style="font-size: 16px !important;"
                            class="w-full sm:w-64 px-4 py-2.5 bg-white border-2 border-slate-300 rounded-xl text-center font-mono text-base font-bold tracking-widest text-[#0B1F3A] focus:outline-none focus:border-[#0FA57E]">
                        
                        <button type="submit"
                            class="w-full sm:w-auto px-6 py-2.5 bg-gradient-to-r from-[#F26B1D] to-[#D04A0A] hover:from-[#E05B0D] hover:to-[#B93E07] text-white font-bold text-xs sm:text-sm rounded-xl transition shadow-sm">
                            {{ __('portal.btn_verify_otp') }}
                        </button>
                    </form>

                    <!-- Cancel transfer option -->
                    <div class="mt-3 flex justify-end">
                        <form method="POST" action="{{ route('portal.transfers.cancel', ['locale' => app()->getLocale(), 'transaction' => $pendingTx]) }}" onsubmit="return confirm('{{ __('portal.confirm_cancellation') }}')">
                            @csrf
                            <button type="submit" class="text-xs text-rose-600 hover:text-rose-800 font-semibold underline">
                                {{ __('portal.btn_cancel_transfer') }}
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 mb-8">

        <!-- Left / Main Form (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl p-6 sm:p-7 shadow-[0_2px_16px_rgba(11,31,58,0.05)] border border-slate-200/80">
            <div class="flex items-center justify-between mb-5 border-b border-slate-100 pb-4">
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-[#0B1F3A]">{{ __('portal.initiate_transfer') }}</h2>
                    <p class="text-xs text-slate-500 mt-0.5">{{ __('portal.partner_banks') }} {{ $client->country }}</p>
                </div>
                <div class="text-right">
                    <span class="text-xs text-[#5B6B82] uppercase font-semibold">{{ __('portal.available_balance') }}</span>
                    <div class="text-base sm:text-lg font-extrabold text-[#0FA57E] font-display">{{ $client->formattedBalance() }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('portal.transfers.store', ['locale' => app()->getLocale()]) }}" class="space-y-4">
                @csrf

                <!-- Bank Selection List -->
                <div>
                    <label for="target_bank_name" class="block text-xs font-bold uppercase tracking-wider text-[#0B1F3A] mb-1.5">
                        {{ __('portal.destination_bank') }}
                    </label>
                    <select id="target_bank_name" name="target_bank_name" required
                        style="font-size: 16px !important;"
                        class="w-full px-4 py-2.5 bg-[#F5F7FA] border border-slate-200 rounded-xl text-sm font-semibold text-[#0B1F3A] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0FA57E] transition">
                        <option value="">{{ __('portal.select_bank') }}</option>
                        @foreach($banks as $b)
                            <option value="{{ $b }}" {{ old('target_bank_name') === $b ? 'selected' : '' }}>
                                {{ $b }}
                            </option>
                        @endforeach
                        <option value="{{ __('portal.other_bank') }}">{{ __('portal.other_bank') }}</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="target_account_holder" class="block text-xs font-bold uppercase tracking-wider text-[#0B1F3A] mb-1.5">
                            {{ __('portal.beneficiary_name') }}
                        </label>
                        <input id="target_account_holder" type="text" name="target_account_holder" value="{{ old('target_account_holder') }}" required
                            placeholder="Ex: Carlos Mendoza / Marie Laurent"
                            style="font-size: 16px !important;"
                            class="w-full px-4 py-2.5 bg-[#F5F7FA] border border-slate-200 rounded-xl text-sm text-[#0B1F3A] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0FA57E] transition">
                    </div>

                    <div>
                        <label for="target_account_number" class="block text-xs font-bold uppercase tracking-wider text-[#0B1F3A] mb-1.5">
                            {{ __('portal.account_iban') }}
                        </label>
                        <input id="target_account_number" type="text" name="target_account_number" value="{{ old('target_account_number') }}" required
                            placeholder="IBAN / CLABE / N°"
                            style="font-size: 16px !important;"
                            class="w-full px-4 py-2.5 bg-[#F5F7FA] border border-slate-200 rounded-xl text-sm font-mono text-[#0B1F3A] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0FA57E] transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="amount" class="block text-xs font-bold uppercase tracking-wider text-[#0B1F3A] mb-1.5">
                            {{ __('portal.amount_to_send') }} ({{ $client->currency }})
                        </label>
                        <div class="relative">
                            <input id="amount" type="number" step="0.01" min="1" name="amount" value="{{ old('amount') }}" required
                                placeholder="0.00"
                                style="font-size: 16px !important;"
                                class="w-full px-4 py-2.5 bg-[#F5F7FA] border border-slate-200 rounded-xl text-base font-bold text-[#0B1F3A] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0FA57E] transition">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-xs font-bold text-slate-400">
                                {{ $client->currency }}
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-bold uppercase tracking-wider text-[#0B1F3A] mb-1.5">
                            {{ __('portal.concept_optional') }}
                        </label>
                        <input id="description" type="text" name="description" value="{{ old('description') }}"
                            placeholder="Ex: Factura, Nómina, Ahorro"
                            style="font-size: 16px !important;"
                            class="w-full px-4 py-2.5 bg-[#F5F7FA] border border-slate-200 rounded-xl text-sm text-[#0B1F3A] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0FA57E] transition">
                    </div>
                </div>

                <div class="pt-3">
                    <button type="submit"
                        class="w-full py-3.5 px-4 bg-gradient-to-r from-[#F26B1D] to-[#D04A0A] hover:from-[#E05B0D] hover:to-[#B93E07] text-white font-bold text-sm sm:text-base rounded-xl shadow-md shadow-orange-500/20 transition flex items-center justify-center gap-2 active:scale-[0.99]">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        <span>{{ __('portal.btn_send') }}</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right Side: Security Standards (5 cols) -->
        <div class="lg:col-span-5 space-y-4">
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-[0_2px_16px_rgba(11,31,58,0.05)]">
                <h3 class="text-xs font-bold uppercase tracking-wider text-[#0B1F3A] mb-3 flex items-center gap-2">
                    <svg class="size-4 text-[#0FA57E]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>{{ __('portal.security_standards') }}</span>
                </h3>
                <ul class="text-xs text-slate-600 space-y-2.5 leading-relaxed">
                    <li class="flex items-start gap-2">
                        <span class="text-[#0FA57E] font-bold">✓</span>
                        <span>{{ __('portal.sec_double_factor') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-[#0FA57E] font-bold">✓</span>
                        <span>{{ __('portal.sec_network', ['country' => $client->country]) }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-[#0FA57E] font-bold">✓</span>
                        <span>{{ __('portal.sec_limit', ['tier' => strtoupper($client->tier)]) }}</span>
                    </li>
                </ul>
            </div>

            <div class="bg-[#E6F5F1] rounded-2xl p-4 border border-[#0FA57E]/20 text-xs text-[#0A5E4E]">
                <strong class="font-bold">Venatomar Security :</strong> {{ __('portal.sec_advisor_note') }}
            </div>
        </div>

    </div>

    <!-- Section: Historique des Opérations (Envois, Reçus, Annulés) -->
    <div class="bg-white rounded-2xl p-6 sm:p-7 shadow-[0_2px_16px_rgba(11,31,58,0.05)] border border-slate-200/80">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
            <h2 class="text-base sm:text-lg font-bold text-[#0B1F3A]">{{ __('portal.recent_transactions') }}</h2>

            <!-- Filter Tabs -->
            <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar text-xs font-semibold">
                <a href="{{ route('portal.transfers', ['locale' => app()->getLocale(), 'tab' => 'all']) }}"
                    class="px-3 py-1.5 rounded-lg transition {{ $tab === 'all' ? 'bg-[#0B1F3A] text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    {{ __('portal.tab_all') }}
                </a>
                <a href="{{ route('portal.transfers', ['locale' => app()->getLocale(), 'tab' => 'sent']) }}"
                    class="px-3 py-1.5 rounded-lg transition {{ $tab === 'sent' ? 'bg-[#0B1F3A] text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    {{ __('portal.tab_sent') }}
                </a>
                <a href="{{ route('portal.transfers', ['locale' => app()->getLocale(), 'tab' => 'received']) }}"
                    class="px-3 py-1.5 rounded-lg transition {{ $tab === 'received' ? 'bg-[#0B1F3A] text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    {{ __('portal.tab_received') }}
                </a>
                <a href="{{ route('portal.transfers', ['locale' => app()->getLocale(), 'tab' => 'pending']) }}"
                    class="px-3 py-1.5 rounded-lg transition {{ $tab === 'pending' ? 'bg-[#0B1F3A] text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    {{ __('portal.tab_pending') }}
                </a>
                <a href="{{ route('portal.transfers', ['locale' => app()->getLocale(), 'tab' => 'cancelled']) }}"
                    class="px-3 py-1.5 rounded-lg transition {{ $tab === 'cancelled' ? 'bg-[#0B1F3A] text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    {{ __('portal.tab_cancelled') }}
                </a>
            </div>
        </div>

        <div class="overflow-x-auto no-scrollbar border border-slate-100 rounded-xl">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-100">
                    <tr>
                        <th class="p-3.5">{{ __('portal.col_date') }}</th>
                        <th class="p-3.5">{{ __('portal.col_ref') }}</th>
                        <th class="p-3.5">{{ __('portal.col_beneficiary') }}</th>
                        <th class="p-3.5">{{ __('portal.col_status') }}</th>
                        <th class="p-3.5 text-right">{{ __('portal.col_amount') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="p-3.5 text-slate-500 font-medium whitespace-nowrap">{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                            <td class="p-3.5 font-mono font-bold text-slate-700 whitespace-nowrap">{{ $tx->reference }}</td>
                            <td class="p-3.5">
                                <div class="font-bold text-[#0B1F3A]">{{ $tx->target_bank_name ?: __('portal.internal_operation') }}</div>
                                <div class="text-[11px] text-slate-400">{{ $tx->target_account_holder ?: $tx->description }}</div>
                            </td>
                            <td class="p-3.5 whitespace-nowrap">
                                @if($tx->isPending())
                                    <span class="text-[10px] font-bold text-amber-800 bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-200">{{ __('portal.tab_pending') }}</span>
                                @elseif($tx->isCancelled())
                                    <span class="text-[10px] font-bold text-rose-700 bg-rose-50 px-2.5 py-0.5 rounded-full border border-rose-200">{{ __('portal.tab_cancelled') }}</span>
                                @else
                                    <span class="text-[10px] font-bold text-[#0A5E4E] bg-[#E6F5F1] px-2.5 py-0.5 rounded-full">{{ __('portal.status_paid') }}</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-right whitespace-nowrap font-extrabold font-display {{ in_array($tx->type, ['transfer_out', 'fee']) ? 'text-[#0B1F3A]' : 'text-[#0FA57E]' }}">
                                {{ $tx->formattedAmount() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400">
                                {{ __('portal.no_transactions') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="mt-4">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

</x-portal-layout>
