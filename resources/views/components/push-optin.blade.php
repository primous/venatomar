@props([
    'email' => null,
])

<div x-data="{
    supported: false,
    subscribed: false,
    loading: false,
    statusMsg: '',
    async init() {
        this.supported = window.VenatomarPush && window.VenatomarPush.isSupported();
        if (this.supported) {
            this.subscribed = await window.VenatomarPush.isSubscribed();
        }
    },
    async activatePush() {
        if (!this.supported) return;
        this.loading = true;
        this.statusMsg = '';
        try {
            const ok = await window.VenatomarPush.subscribe(@js($email));
            this.subscribed = ok;
            if (ok) {
                this.statusMsg = '{{ __('thanks.push_test_success') }}';
                try {
                    await fetch('/push/test', {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                        body: JSON.stringify({ email: @js($email) })
                    });
                } catch (e) {}
            } else {
                this.statusMsg = '{{ __('thanks.push_denied') }}';
            }
        } catch (e) {
            this.statusMsg = '{{ __('thanks.push_denied') }}';
        } finally {
            this.loading = false;
        }
    },
    async sendTest() {
        this.loading = true;
        this.statusMsg = '';
        try {
            const res = await fetch('/push/test', {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({ email: @js($email) })
            });
            const data = await res.json();
            this.statusMsg = data.message || '{{ __('thanks.push_test_success') }}';
        } catch (e) {
            this.statusMsg = 'Error al enviar prueba';
        } finally {
            this.loading = false;
        }
    }
}" x-show="supported" x-cloak class="mt-6 rounded-2xl border border-orange-200/80 bg-gradient-to-br from-orange-50/60 via-white to-amber-50/40 p-4 sm:p-5 text-left shadow-sm">
    <div class="flex items-start gap-3 sm:gap-4">
        <div class="inline-flex size-10 sm:size-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-[#F26B1D] to-[#D04A0A] text-white shadow-sm">
            <svg class="size-5 sm:size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
        </div>
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
                <h3 class="font-display text-sm sm:text-base font-bold text-ink-900">{{ __('thanks.push_title') }}</h3>
                <span x-show="subscribed" x-cloak class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-semibold text-emerald-800">
                    <span class="size-1.5 rounded-full bg-emerald-500"></span> {{ __('thanks.push_active') }}
                </span>
            </div>
            <p class="mt-1 text-xs sm:text-sm text-ink-500 leading-relaxed">{{ __('thanks.push_desc') }}</p>

            <div x-show="statusMsg" x-cloak class="mt-2 text-xs font-semibold text-[#F26B1D]" x-text="statusMsg"></div>

            <div class="mt-3 flex flex-wrap gap-2">
                <button type="button" x-show="!subscribed" @click="activatePush()" :disabled="loading"
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-[#F26B1D] to-[#D04A0A] px-4 py-2 text-xs sm:text-sm font-bold text-white shadow hover:opacity-95 active:scale-[0.98] transition">
                    <span x-show="loading" class="animate-spin inline-block">⌛</span>
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                    <span>{{ __('thanks.push_button') }}</span>
                </button>

                <button type="button" x-show="subscribed" x-cloak @click="sendTest()" :disabled="loading"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-ink-700 hover:bg-slate-50 transition shadow-sm">
                    <svg class="size-3.5 text-[#F26B1D]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    <span>{{ __('thanks.push_test_btn') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>
