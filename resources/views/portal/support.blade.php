<x-portal-layout>
    <x-slot name="title">{{ __('portal.support_title') }}</x-slot>
    <x-slot name="pageHeading">{{ __('portal.support_title') }}</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 mb-8">

        <!-- Form Column (7 cols) -->
        <div class="lg:col-span-7 bg-white rounded-2xl p-5 sm:p-6 shadow-[0_2px_12px_rgba(11,31,58,0.04)] border border-slate-200/80">
            <div class="mb-4 border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-[#0B1F3A]">{{ __('portal.new_message_title') }}</h2>
                <p class="text-xs text-slate-500">{{ __('portal.new_message_desc') }}</p>
            </div>

            <form method="POST" action="{{ route('portal.support.store', ['locale' => app()->getLocale()]) }}" class="space-y-4">
                @csrf

                <div>
                    <label for="category" class="block text-xs font-bold uppercase tracking-wider text-[#0B1F3A] mb-1.5">
                        {{ __('portal.category_field') }}
                    </label>
                    <select id="category" name="category" required
                        class="w-full px-3.5 py-2.5 bg-[#F5F7FA] border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold text-[#0B1F3A] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0FA57E]">
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
                        class="w-full px-3.5 py-2.5 bg-[#F5F7FA] border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1F3A] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0FA57E]">
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
                        class="w-full px-3.5 py-2.5 bg-[#F5F7FA] border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1F3A] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0FA57E]">
                </div>

                <div>
                    <label for="message" class="block text-xs font-bold uppercase tracking-wider text-[#0B1F3A] mb-1.5">
                        {{ __('portal.message_field') }}
                    </label>
                    <textarea id="message" name="message" rows="4" required
                        placeholder="{{ __('portal.message_placeholder') }}"
                        class="w-full px-3.5 py-2.5 bg-[#F5F7FA] border border-slate-200 rounded-xl text-xs sm:text-sm text-[#0B1F3A] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0FA57E]">{{ old('message') }}</textarea>
                </div>

                <button type="submit"
                    class="w-full py-3 bg-[#0FA57E] hover:bg-[#0E7C66] text-white font-bold text-xs sm:text-sm rounded-xl transition shadow-md flex items-center justify-center gap-2">
                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    <span>{{ __('portal.btn_send_message') }}</span>
                </button>
            </form>
        </div>

        <!-- Advisor Info Column (5 cols) -->
        <div class="lg:col-span-5 space-y-4">
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-[0_2px_12px_rgba(11,31,58,0.04)]">
                <div class="flex items-center gap-3.5 mb-4">
                    <div class="size-12 rounded-xl bg-[#E6F5F1] text-[#0A5E4E] font-extrabold text-base flex items-center justify-center">
                        VM
                    </div>
                    <div>
                        <div class="text-[10px] uppercase font-bold text-[#0FA57E] tracking-wider">{{ __('portal.customer_service_dept') }}</div>
                        <div class="text-sm font-extrabold text-[#0B1F3A]">{{ __('portal.management_firm') }}</div>
                        <div class="text-xs text-slate-400">{{ __('portal.madrid_central') }}</div>
                    </div>
                </div>

                <div class="text-xs text-slate-600 space-y-2 border-t border-slate-100 pt-3">
                    <div>📧 contact@venatomar.com</div>
                    <div>🛡️ +34 910 000 000</div>
                    <div>⏱️ {{ __('portal.advisor_hours') }}</div>
                </div>
            </div>

            <div class="bg-[#FFF1E8] rounded-2xl p-4 border border-[#F26B1D]/30 text-xs text-[#B23F08]">
                <strong>Venatomar Security :</strong> {{ __('portal.anti_fraud_notice') }}
            </div>
        </div>

    </div>

    <!-- Ticket History List -->
    <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-[0_2px_12px_rgba(11,31,58,0.04)] border border-slate-200/80">
        <h3 class="text-base font-bold text-[#0B1F3A] mb-4">
            {{ __('portal.support_history') }}
        </h3>

        <div class="space-y-4">
            @forelse($messages as $msg)
                <div class="p-4 rounded-xl border border-slate-200 hover:border-slate-300 transition bg-[#F5F7FA]">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-2">
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold uppercase px-2.5 py-0.5 rounded-full {{ in_array($msg->category, ['fraud_alert', 'transaction_dispute']) ? 'bg-rose-100 text-rose-800' : 'bg-slate-200 text-slate-700' }}">
                                {{ $msg->category === 'fraud_alert' ? __('portal.cat_fraud') : ($msg->category === 'transaction_dispute' ? __('portal.cat_dispute') : __('portal.support_title')) }}
                            </span>
                            <span class="font-bold text-sm text-[#0B1F3A]">{{ $msg->subject }}</span>
                        </div>
                        <span class="text-xs text-slate-400">{{ $msg->created_at->format('d/m/Y H:i') }}</span>
                    </div>

                    <div class="text-xs text-slate-600 bg-white p-3 rounded-lg border border-slate-100 leading-relaxed">
                        {{ $msg->message }}
                    </div>

                    @if($msg->admin_reply)
                        <div class="mt-3 pl-3 border-l-3 border-[#0FA57E] bg-[#E6F5F1]/50 p-3 rounded-r-lg">
                            <div class="text-[11px] font-bold text-[#0A5E4E] flex items-center gap-1.5 mb-1">
                                <svg class="size-4 text-[#0FA57E]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>{{ __('portal.official_reply') }} · {{ $msg->replied_at ? $msg->replied_at->format('d/m/Y') : '' }}</span>
                            </div>
                            <p class="text-xs text-slate-700 leading-relaxed">
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
