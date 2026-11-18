<x-portal-layout>
    <x-slot name="title">{{ __('portal.loans_title') }}</x-slot>
    <x-slot name="pageHeading">{{ __('portal.loans_title') }}</x-slot>

    <!-- Top Two-Panel Layout on Desktop -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 mb-8">

        <!-- Panel 1: Internal Savings Transfer (Checking <-> Savings) (6 cols) -->
        <div class="lg:col-span-6 bg-white rounded-2xl p-5 sm:p-6 shadow-[0_2px_12px_rgba(11,31,58,0.04)] border border-slate-200/80">
            <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-base font-bold text-[#0B1F3A]">{{ __('portal.internal_transfer_title') }}</h2>
                    <p class="text-xs text-slate-500">{{ __('portal.internal_transfer_desc') }}</p>
                </div>
                <span class="text-xs font-bold text-[#0A5E4E] bg-[#E6F5F1] px-2.5 py-1 rounded-full border border-[#0FA57E]/30">
                    {{ __('portal.instant_247') }}
                </span>
            </div>

            <!-- Balances summary -->
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="p-3.5 bg-[#F5F7FA] rounded-xl border border-slate-200">
                    <div class="text-[11px] text-slate-500 font-semibold uppercase">{{ __('portal.checking_balance_label') }}</div>
                    <div class="text-base sm:text-lg font-extrabold text-[#0B1F3A] font-display mt-0.5">{{ $client->formattedBalance() }}</div>
                </div>
                <div class="p-3.5 bg-[#E6F5F1] rounded-xl border border-[#0FA57E]/30">
                    <div class="text-[11px] text-[#0A5E4E] font-semibold uppercase">{{ __('portal.savings_balance_label') }}</div>
                    <div class="text-base sm:text-lg font-extrabold text-[#0A5E4E] font-display mt-0.5">{{ $client->formattedSavingsBalance() }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('portal.loans.savings-transfer', ['locale' => app()->getLocale()]) }}" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#0B1F3A] mb-1.5">{{ __('portal.transfer_direction') }}</label>
                    <select name="direction" required class="w-full px-3.5 py-2.5 bg-[#F5F7FA] border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-[#0B1F3A] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0FA57E]">
                        <option value="to_savings">{{ __('portal.checking_to_savings') }}</option>
                        <option value="from_savings">{{ __('portal.savings_to_checking') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#0B1F3A] mb-1.5">{{ __('portal.amount_to_transfer') }} ({{ $client->currency }})</label>
                    <input type="number" step="0.01" min="1" name="amount" required placeholder="0.00"
                        class="w-full px-3.5 py-2.5 bg-[#F5F7FA] border border-slate-200 rounded-xl text-sm font-bold text-[#0B1F3A] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0FA57E]">
                </div>

                <button type="submit"
                    class="w-full py-3 bg-[#0FA57E] hover:bg-[#0E7C66] text-white font-bold text-xs sm:text-sm rounded-xl transition flex items-center justify-center gap-2 shadow-sm">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    <span>{{ __('portal.btn_internal_transfer') }}</span>
                </button>
            </form>
        </div>

        <!-- Panel 2: Demande de Financement Express (6 cols) -->
        <div class="lg:col-span-6 bg-white rounded-2xl p-5 sm:p-6 shadow-[0_2px_12px_rgba(11,31,58,0.04)] border border-slate-200/80">
            <div class="mb-4 border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-[#0B1F3A]">{{ __('portal.request_loan_title') }}</h2>
                <p class="text-xs text-slate-500">{{ __('portal.request_loan_desc') }}</p>
            </div>

            <form method="POST" action="{{ route('portal.loans.apply', ['locale' => app()->getLocale()]) }}" class="space-y-3.5">
                @csrf

                <div>
                    <label for="title" class="block text-xs font-bold uppercase tracking-wider text-[#0B1F3A] mb-1">
                        {{ __('portal.loan_title_field') }}
                    </label>
                    <input id="title" type="text" name="title" value="{{ old('title', __('portal.loan_title_default')) }}" required
                        class="w-full px-3.5 py-2 bg-[#F5F7FA] border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-[#0B1F3A] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0FA57E]">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="amount" class="block text-xs font-bold uppercase tracking-wider text-[#0B1F3A] mb-1">
                            {{ __('portal.loan_amount_field') }} ({{ $client->currency }})
                        </label>
                        <input id="amount" type="number" step="100" min="500" max="500000" name="amount" value="{{ old('amount', 5000) }}" required
                            class="w-full px-3.5 py-2 bg-[#F5F7FA] border border-slate-200 rounded-xl text-sm font-bold text-[#0B1F3A] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0FA57E]">
                    </div>

                    <div>
                        <label for="term_months" class="block text-xs font-bold uppercase tracking-wider text-[#0B1F3A] mb-1">
                            {{ __('portal.loan_term_field') }}
                        </label>
                        <select id="term_months" name="term_months" required
                            class="w-full px-3.5 py-2 bg-[#F5F7FA] border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-[#0B1F3A] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0FA57E]">
                            <option value="12">12 {{ __('portal.loan_term_months') }}</option>
                            <option value="24" selected>24 {{ __('portal.loan_term_months') }}</option>
                            <option value="36">36 {{ __('portal.loan_term_months') }}</option>
                            <option value="48">48 {{ __('portal.loan_term_months') }}</option>
                            <option value="60">60 {{ __('portal.loan_term_months') }}</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="project_description" class="block text-xs font-bold uppercase tracking-wider text-[#0B1F3A] mb-1">
                        {{ __('portal.loan_details_field') }}
                    </label>
                    <textarea id="project_description" name="project_description" rows="2"
                        class="w-full px-3.5 py-2 bg-[#F5F7FA] border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1F3A] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0FA57E]">{{ old('project_description') }}</textarea>
                </div>

                <button type="submit"
                    class="w-full py-3 bg-[#F26B1D] hover:bg-[#D04A0A] text-white font-bold text-xs sm:text-sm rounded-xl transition shadow-md flex items-center justify-center gap-2">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ __('portal.btn_submit_loan') }}</span>
                </button>
            </form>
        </div>

    </div>

    <!-- Active Loan & Amortization Schedule Table (Échéancier) -->
    @if($activeLoan)
        <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-[0_2px_12px_rgba(11,31,58,0.04)] border border-slate-200/80 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 pb-4 mb-4 gap-2">
                <div>
                    <span class="text-[10px] font-bold text-[#0A5E4E] bg-[#E6F5F1] px-2.5 py-0.5 rounded-full border border-[#0FA57E]/30 uppercase tracking-wide">
                        {{ __('portal.active_loan') }}
                    </span>
                    <h2 class="text-lg font-extrabold text-[#0B1F3A] font-display mt-1">
                        {{ $activeLoan->title }}
                    </h2>
                    <div class="text-xs text-slate-400 font-mono">
                        {{ __('portal.col_ref') }} {{ $activeLoan->reference }} · {{ $client->formatMoney($activeLoan->amount) }}
                    </div>
                </div>

                <div class="flex items-center gap-4 text-right">
                    <div class="p-3 bg-[#F5F7FA] rounded-xl border border-slate-200">
                        <div class="text-[10px] font-bold text-slate-500 uppercase">{{ __('portal.remaining_capital') }}</div>
                        <div class="text-base sm:text-lg font-extrabold text-[#0B1F3A] font-display">{{ $activeLoan->formattedRemaining() }}</div>
                    </div>
                    <div class="p-3 bg-[#E6F5F1] rounded-xl border border-[#0FA57E]/30">
                        <div class="text-[10px] font-bold text-[#0A5E4E] uppercase">{{ __('portal.monthly_payment') }}</div>
                        <div class="text-base sm:text-lg font-extrabold text-[#0A5E4E] font-display">{{ $activeLoan->formattedMonthly() }}</div>
                    </div>
                </div>
            </div>

            <!-- Échéancier complet -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-[#0B1F3A] mb-3">
                    {{ __('portal.repayment_schedule_title') }}
                </h3>

                <div class="overflow-x-auto no-scrollbar border border-slate-100 rounded-xl">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-100">
                            <tr>
                                <th class="p-3">{{ __('portal.installment_col') }}</th>
                                <th class="p-3">{{ __('portal.due_date_col') }}</th>
                                <th class="p-3">{{ __('portal.installment_amount_col') }}</th>
                                <th class="p-3">{{ __('portal.interest_rate_col') }}</th>
                                <th class="p-3 text-right">{{ __('portal.status_col') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @php
                                $monthly = (float) $activeLoan->monthly_payment;
                                $rem = (float) $activeLoan->remaining_amount;
                                $paidCount = max(0, (int) round(($activeLoan->amount - $rem) / ($monthly ?: 1)));
                            @endphp
                            @for($i = 1; $i <= min(12, $activeLoan->term_months); $i++)
                                @php
                                    $isPaid = $i <= $paidCount;
                                    $dueDate = now()->startOfMonth()->addMonths($i - 1);
                                @endphp
                                <tr class="{{ $isPaid ? 'bg-[#E6F5F1]/30' : 'hover:bg-slate-50/70' }}">
                                    <td class="p-3 font-bold font-mono text-[#0B1F3A]">N° {{ sprintf('%02d', $i) }} / {{ $activeLoan->term_months }}</td>
                                    <td class="p-3 text-slate-600 font-medium">{{ $dueDate->format('05/m/Y') }}</td>
                                    <td class="p-3 font-bold text-[#0B1F3A] font-display">{{ $activeLoan->formattedMonthly() }}</td>
                                    <td class="p-3 text-slate-500">{{ $activeLoan->interest_rate }}%</td>
                                    <td class="p-3 text-right">
                                        @if($isPaid)
                                            <span class="text-[10px] font-bold text-[#0A5E4E] bg-[#E6F5F1] px-2.5 py-0.5 rounded-full border border-[#0FA57E]/30">{{ __('portal.status_paid') }}</span>
                                        @else
                                            <span class="text-[10px] font-bold text-slate-600 bg-slate-100 px-2.5 py-0.5 rounded-full">{{ __('portal.status_pending') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

</x-portal-layout>
