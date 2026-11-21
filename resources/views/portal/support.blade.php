<x-portal-layout>
    <x-slot name="title">{{ __('portal.support_title') }}</x-slot>
    <x-slot name="pageHeading">{{ __('portal.support_title') }}</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 mb-8">

        <!-- Form Column (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl p-6 sm:p-7 shadow-[0_2px_16px_rgba(11,31,58,0.06)]">
            <div class="mb-5 border-b border-slate-100 pb-4">
                <h2 class="text-base sm:text-lg font-bold text-[#0B1F3A]">{{ __('portal.new_message_title') }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">{{ __('portal.new_message_desc') }}</p>
            </div>

            <form method="POST" action="{{ route('portal.support.store', ['locale' => app()->getLocale()]) }}" class="space-y-4">
                @csrf

                <div>
                    <label for="category" class="block text-xs font-bold uppercase tracking-wider text-[#0B1F3A] mb-1.5">
                        {{ __('portal.category_field') }}
                    </label>
                    <select id="category" name="category" required
                        style="font-size: 16px !important;"
                        class="w-full px-4 py-2.5 bg-[#F5F7FA] border border-slate-200 rounded-xl text-sm font-semibold text-[#0B1F3A] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0FA57E]">
                        <option value="general">{{ __('portal.cat_general') }}</option>
                        <option value="fraud_alert">{{ __('portal.cat_fraud') }}</option>
                        <option value="transaction_dispute">{{ __('portal.cat_dispute') }}</option>
                        <option value="loan_inquiry">{{ __('portal.cat_loan') }}</option>
                        <option value="technical">{{ __('portal.cat_tech') }}</option>
                    </select>
                </div>

                <div>
                    <label for="bank_transaction_id" class="block text-xs font-bold uppercase tracking-wider text-[#0B1F3A] mb-1.5">
                        {{ __('portal.related_tx') }}
                    </label>
                    <select id="bank_transaction_id" name="bank_transaction_id"
                        style="font-size: 16px !important;"
                        class="w-full px-4 py-2.5 bg-[#F5F7FA] border border-slate-200 rounded-xl text-sm text-[#0B1F3A] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0FA57E]">
                        <option value="">{{ __('portal.no_related_tx') }}</option>
                        @foreach($transactions as $t)
                            <option value="{{ $t->id }}">
                                {{ $t->reference }} · {{ $t->created_at->format('d/m/Y') }} · {{ $t->target_bank_name ?: __('portal.internal_operation') }} ({{ $t->formattedAmount() }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="subject" class="block text-xs font-bold uppercase tracking-wider text-[#0B1F3A] mb-1.5">
                        {{ __('portal.subject_field') }}
                    </label>
                    <input id="subject" type="text" name="subject" value="{{ old('subject') }}" required
                        placeholder="{{ __('portal.subject_placeholder') }}"
                        style="font-size: 16px !important;"
                        class="w-full px-4 py-2.5 bg-[#F5F7FA] border border-slate-200 rounded-xl text-sm text-[#0B1F3A] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0FA57E]">
                </div>

                <div>
                    <label for="message" class="block text-xs font-bold uppercase tracking-wider text-[#0B1F3A] mb-1.5">
                        {{ __('portal.message_field') }}
                    </label>
                    <textarea id="message" name="message" rows="4" required
                        placeholder="{{ __('portal.message_placeholder') }}"
                        style="font-size: 16px !important;"
                        class="w-full px-4 py-2.5 bg-[#F5F7FA] border border-slate-200 rounded-xl text-sm text-[#0B1F3A] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0FA57E]">{{ old('message') }}</textarea>
                </div>

                <button type="submit"
                    class="w-full py-3.5 bg-[#0FA57E] hover:bg-[#0E7C66] text-white font-bold text-sm sm:text-base rounded-xl transition shadow-md shadow-emerald-600/20 flex items-center justify-center gap-2 active:scale-[0.99]">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    <span>{{ __('portal.btn_send_message') }}</span>
                </button>
            </form>
        </div>

        <!-- Advisor Info Column (5 cols) -->
        <div class="lg:col-span-5 space-y-4">
            <div class="bg-white rounded-2xl p-6 shadow-[0_2px_16px_rgba(11,31,58,0.06)]">
                <div class="flex items-center gap-3.5 mb-4">
                    <div class="size-12 rounded-xl bg-[#E6F5F1] text-[#0A5E4E] font-extrabold text-base flex items-center justify-center">
                        VM
                    </div>
                    <div>
                        <div class="text-[10px] uppercase font-bold text-[#0FA57E] tracking-wider">{{ __('portal.customer_service_dept') }}</div>
                        <div class="text-base font-extrabold text-[#0B1F3A]">{{ __('portal.management_firm') }}</div>
                        <div class="text-xs text-slate-400">{{ __('portal.madrid_central') }}</div>
                    </div>
                </div>

                <div class="text-xs text-slate-600 space-y-2.5 border-t border-slate-100 pt-4 leading-relaxed">
                    <div class="flex items-center gap-2">
                        <span class="text-base">📧</span>
                        <span class="font-semibold text-[#0B1F3A]">contact@venatomar.com</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-base">🛡️</span>
                        <span>Service client & conformité européenne</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-base">⏱️</span>
                        <span>{{ __('portal.advisor_hours') }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-[#FFF1E8] rounded-2xl p-5 text-xs text-[#B23F08] leading-relaxed">
                <strong class="font-bold">Venatomar Security :</strong> {{ __('portal.anti_fraud_notice') }}
            </div>
        </div>

    </div>

    <!-- Ticket History List -->
    <div class="bg-white rounded-2xl p-6 sm:p-7 shadow-[0_2px_16px_rgba(11,31,58,0.06)]">
        <h3 class="text-base sm:text-lg font-bold text-[#0B1F3A] mb-5">
            {{ __('portal.support_history') }}
        </h3>

        <div class="space-y-4">
            @forelse($messages as $msg)
                <div class="p-4 sm:p-5 rounded-2xl transition bg-slate-50/70">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-2.5">
                        <div class="flex items-center gap-2.5">
                            <span class="text-[10px] font-bold uppercase px-2.5 py-0.5 rounded-full {{ in_array($msg->category, ['fraud_alert', 'transaction_dispute']) ? 'bg-rose-100 text-rose-800' : 'bg-slate-200 text-slate-700' }}">
                                {{ $msg->category === 'fraud_alert' ? __('portal.cat_fraud') : ($msg->category === 'transaction_dispute' ? __('portal.cat_dispute') : __('portal.support_title')) }}
                            </span>
                            <span class="font-bold text-sm sm:text-base text-[#0B1F3A]">{{ $msg->subject }}</span>
                        </div>
                        <span class="text-xs text-slate-400">{{ $msg->created_at->format('d/m/Y H:i') }}</span>
                    </div>

                    <div class="text-xs sm:text-sm text-slate-700 bg-white p-3.5 rounded-xl leading-relaxed shadow-sm">
                        {{ $msg->message }}
                    </div>

                    @if($msg->admin_reply)
                        <div class="mt-3.5 pl-4 border-l-4 border-[#0FA57E] bg-[#E6F5F1]/60 p-4 rounded-r-xl">
                            <div class="text-xs font-bold text-[#0A5E4E] flex items-center gap-1.5 mb-1.5">
                                <svg class="size-4 text-[#0FA57E]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>{{ __('portal.official_reply') }} · {{ $msg->replied_at ? $msg->replied_at->format('d/m/Y') : '' }}</span>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                                {{ $msg->admin_reply }}
                            </p>
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center text-xs text-slate-400">
                    {{ __('portal.no_support_history') }}
                </div>
            @endforelse
        </div>
    </div>

</x-portal-layout>
