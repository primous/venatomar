<x-portal-layout>
    <x-slot name="title">{{ __('portal.profile_title') }}</x-slot>
    <x-slot name="pageHeading">{{ __('portal.profile_title') }}</x-slot>

    <!-- Top Identity Card -->
    <div class="bg-white rounded-2xl p-6 sm:p-7 shadow-[0_2px_16px_rgba(11,31,58,0.06)] mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="size-16 rounded-2xl bg-gradient-to-br from-[#0B1F3A] to-[#0A5E4E] text-white font-extrabold text-xl flex items-center justify-center font-display shadow-md">
                    {{ strtoupper(substr($client->first_name, 0, 1) . substr($client->last_name, 0, 1)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h2 class="text-lg sm:text-xl font-extrabold text-[#0B1F3A] font-display">
                            {{ $client->fullName() }}
                        </h2>
                        <span class="text-[10px] font-bold uppercase px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900">
                            {{ strtoupper($client->tier) }}
                        </span>
                    </div>
                    <div class="text-xs text-slate-500 font-mono mt-0.5">{{ $client->account_number }} · {{ $client->email }}</div>
                    <div class="text-xs text-[#0FA57E] font-semibold mt-1 flex items-center gap-1.5">
                        <span class="size-2 rounded-full bg-[#0FA57E]"></span>
                        <span>{{ __('portal.verified_account') }} ({{ $client->country }})</span>
                    </div>
                </div>
            </div>

            <div class="text-left sm:text-right border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100">
                <div class="text-xs text-slate-400">{{ __('portal.opening_date_label') }}</div>
                <div class="text-xs font-bold text-slate-700 mt-0.5">{{ $client->created_at->format('d/m/Y') }}</div>
            </div>
        </div>
    </div>

    <!-- Security Preferences & Password Change Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 mb-8">

        <!-- Left: Password Update Form (6 cols) -->
        <div class="lg:col-span-6 bg-white rounded-2xl p-6 sm:p-7 shadow-[0_2px_16px_rgba(11,31,58,0.06)]">
            <h3 class="text-base sm:text-lg font-bold text-[#0B1F3A] mb-1">
                {{ __('portal.change_password_title') }}
            </h3>
            <p class="text-xs text-slate-500 mb-5">{{ __('portal.change_password_desc') }}</p>

            <form method="POST" action="{{ route('portal.profile.update', ['locale' => app()->getLocale()]) }}" class="space-y-4">
                @csrf
                <input type="hidden" name="action" value="password">

                <div>
                    <label for="current_password" class="block text-xs font-bold uppercase tracking-wider text-[#0B1F3A] mb-1.5">
                        {{ __('portal.current_password') }}
                    </label>
                    <input id="current_password" type="password" name="current_password" required
                        style="font-size: 16px !important;"
                        class="w-full px-4 py-2.5 bg-[#F5F7FA] border border-slate-200 rounded-xl text-sm text-[#0B1F3A] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0FA57E]">
                </div>

                <div>
                    <label for="new_password" class="block text-xs font-bold uppercase tracking-wider text-[#0B1F3A] mb-1.5">
                        {{ __('portal.new_password') }}
                    </label>
                    <input id="new_password" type="password" name="password" required
                        style="font-size: 16px !important;"
                        class="w-full px-4 py-2.5 bg-[#F5F7FA] border border-slate-200 rounded-xl text-sm text-[#0B1F3A] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0FA57E]">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-[#0B1F3A] mb-1.5">
                        {{ __('portal.confirm_password') }}
                    </label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        style="font-size: 16px !important;"
                        class="w-full px-4 py-2.5 bg-[#F5F7FA] border border-slate-200 rounded-xl text-sm text-[#0B1F3A] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0FA57E]">
                </div>

                <button type="submit"
                    class="w-full py-3 bg-[#0B1F3A] hover:bg-[#122A4E] text-white font-bold text-sm rounded-xl transition shadow-sm active:scale-[0.99]">
                    {{ __('portal.btn_save_password') }}
                </button>
            </form>
        </div>

        <!-- Right: Personal Coordinates (6 cols) -->
        <div class="lg:col-span-6 bg-white rounded-2xl p-6 sm:p-7 shadow-[0_2px_16px_rgba(11,31,58,0.06)]">
            <h3 class="text-base sm:text-lg font-bold text-[#0B1F3A] mb-1">
                {{ __('portal.contact_info_title') }}
            </h3>
            <p class="text-xs text-slate-500 mb-5">{{ __('portal.contact_info_desc') }}</p>

            <form method="POST" action="{{ route('portal.profile.update', ['locale' => app()->getLocale()]) }}" class="space-y-4">
                @csrf
                <input type="hidden" name="action" value="profile">

                <div>
                    <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-[#0B1F3A] mb-1.5">{{ __('portal.phone_field') }}</label>
                    <input id="phone" type="tel" name="phone" value="{{ old('phone', $client->phone) }}"
                        style="font-size: 16px !important;"
                        class="w-full px-4 py-2.5 bg-[#F5F7FA] border border-slate-200 rounded-xl text-sm text-[#0B1F3A] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0FA57E]">
                </div>

                <div>
                    <label for="address" class="block text-xs font-bold uppercase tracking-wider text-[#0B1F3A] mb-1.5">{{ __('portal.address_field') }}</label>
                    <input id="address" type="text" name="address" value="{{ old('address', $client->address) }}"
                        style="font-size: 16px !important;"
                        class="w-full px-4 py-2.5 bg-[#F5F7FA] border border-slate-200 rounded-xl text-sm text-[#0B1F3A] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0FA57E]">
                </div>

                <div class="grid grid-cols-2 gap-3.5">
                    <div>
                        <label for="postal_code" class="block text-xs font-bold uppercase tracking-wider text-[#0B1F3A] mb-1.5">{{ __('portal.postal_code_field') }}</label>
                        <input id="postal_code" type="text" name="postal_code" value="{{ old('postal_code', $client->postal_code) }}"
                            style="font-size: 16px !important;"
                            class="w-full px-4 py-2.5 bg-[#F5F7FA] border border-slate-200 rounded-xl text-sm text-[#0B1F3A] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0FA57E]">
                    </div>
                    <div>
                        <label for="city" class="block text-xs font-bold uppercase tracking-wider text-[#0B1F3A] mb-1.5">{{ __('portal.city_field') }}</label>
                        <input id="city" type="text" name="city" value="{{ old('city', $client->city) }}"
                            style="font-size: 16px !important;"
                            class="w-full px-4 py-2.5 bg-[#F5F7FA] border border-slate-200 rounded-xl text-sm text-[#0B1F3A] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0FA57E]">
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-3 bg-[#0FA57E] hover:bg-[#0E7C66] text-white font-bold text-sm rounded-xl transition shadow-sm active:scale-[0.99]">
                    {{ __('portal.btn_save_coords') }}
                </button>
            </form>
        </div>

    </div>

    <!-- Security Protocols Summary -->
    <div class="bg-white rounded-2xl p-6 sm:p-7 shadow-[0_2px_16px_rgba(11,31,58,0.06)] mb-6">
        <h3 class="text-xs font-bold uppercase tracking-wider text-[#5B6B82] mb-4">
            {{ __('portal.security_settings') }}
        </h3>

        <div class="space-y-3">
            <div class="flex items-center justify-between p-4 rounded-xl bg-[#F5F7FA]">
                <div>
                    <div class="font-bold text-sm text-[#0B1F3A]">{{ __('portal.otp_transmission') }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">
                        @if($client->can_receive_otp_email)
                            {{ __('portal.otp_email_active') }} ({{ $client->email }})
                        @else
                            {{ __('portal.otp_admin_active') }}
                        @endif
                    </div>
                </div>
                <span class="text-xs font-bold px-3 py-1 rounded-full {{ $client->can_receive_otp_email ? 'bg-[#E6F5F1] text-[#0A5E4E]' : 'bg-amber-100 text-amber-800' }}">
                    {{ $client->can_receive_otp_email ? 'Email' : 'Admin OTP' }}
                </span>
            </div>
        </div>
    </div>

</x-portal-layout>
