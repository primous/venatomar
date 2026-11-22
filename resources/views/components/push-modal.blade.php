<div x-data="{
    open: false,
    supported: false,
    loading: false,
    status: '',
    statusMsg: '',
    async init() {
        await this.$nextTick();
        this.checkStatus();
    },
    async checkStatus() {
        if (!('Notification' in window) || !('serviceWorker' in navigator)) {
            this.supported = false;
            return;
        }
        this.supported = true;

        // If notifications are ALREADY granted, never show the modal!
        if (Notification.permission === 'granted') {
            this.open = false;
            return;
        }

        // If permission was already permanently denied in browser settings,
        // we check if they already dismissed it this session
        if (Notification.permission === 'denied' && sessionStorage.getItem('vtm_push_denied_seen')) {
            this.open = false;
            return;
        }

        this.open = true;
    },
    async activate() {
        if (!this.supported) return;
        this.loading = true;
        this.statusMsg = '';

        try {
            const ok = await window.VenatomarPush.subscribe();
            if (ok) {
                this.status = 'success';
                this.statusMsg = '{{ __('push.modal_success') }}';

                // Send immediate test push to this device
                try {
                    await window.VenatomarPush.sendTest();
                } catch(e) {}

                // Close modal after 1.2s confirmation
                setTimeout(() => {
                    this.open = false;
                }, 1200);
            } else {
                this.status = 'denied';
                this.statusMsg = '{{ __('push.modal_denied') }}';
                sessionStorage.setItem('vtm_push_denied_seen', '1');
            }
        } catch (e) {
            console.error('[PushModal] Error:', e);
            this.status = 'denied';
            this.statusMsg = '{{ __('push.modal_denied') }}';
            sessionStorage.setItem('vtm_push_denied_seen', '1');
        } finally {
            this.loading = false;
        }
    },
    dismiss() {
        sessionStorage.setItem('vtm_push_denied_seen', '1');
        this.open = false;
    }
}"
x-show="open"
x-cloak
class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6"
style="display: none;"
role="dialog"
aria-modal="true"
aria-labelledby="push-modal-title">

    <!-- Dark Blurred Backdrop blocking interaction with page behind -->
    <div class="fixed inset-0 bg-slate-950/85 backdrop-blur-md transition-opacity"></div>

    <!-- Large Prominent Modal Card -->
    <div class="relative w-full max-w-lg overflow-hidden rounded-3xl bg-white p-6 sm:p-10 text-center shadow-2xl border border-slate-100 transform transition-all">

        <!-- Top orange gradient accent -->
        <div class="absolute top-0 inset-x-0 h-2 bg-gradient-to-r from-[#F26B1D] via-[#FF7E36] to-[#D04A0A]"></div>

        <!-- Big Bell Icon with pulsing aura -->
        <div class="relative mx-auto mb-6 flex size-20 sm:size-24 items-center justify-center rounded-3xl bg-gradient-to-br from-[#F26B1D] via-[#FF7E36] to-[#D04A0A] text-white shadow-xl shadow-orange-500/35">
            <span class="absolute -top-1 -right-1 flex size-5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-300 opacity-75"></span>
                <span class="relative inline-flex rounded-full size-5 bg-[#F26B1D] border-2 border-white"></span>
            </span>
            <svg class="size-10 sm:size-12 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
        </div>

        <!-- Title -->
        <h2 id="push-modal-title" class="font-display text-xl sm:text-2xl font-black text-ink-900 leading-snug sm:leading-tight">
            {{ __('push.modal_title') }}
        </h2>

        <!-- Description -->
        <p class="mt-3 text-sm sm:text-base text-ink-500 leading-relaxed">
            {{ __('push.modal_desc') }}
        </p>

        <!-- Success Feedback -->
        <div x-show="status === 'success'" x-cloak class="mt-4 inline-flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-2 text-xs sm:text-sm font-bold text-emerald-700 border border-emerald-200">
            <svg class="size-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
            <span x-text="statusMsg"></span>
        </div>

        <!-- Denied Feedback -->
        <div x-show="status === 'denied'" x-cloak class="mt-4 rounded-xl bg-amber-50 p-3.5 text-xs sm:text-sm text-amber-800 border border-amber-200 text-left">
            <p class="font-bold flex items-center gap-1.5">
                <span>⚠️</span> <span x-text="statusMsg"></span>
            </p>
            <p class="mt-1 text-xs text-amber-700">
                Puede activar las notificaciones en cualquier momento desde los ajustes de su navegador.
            </p>
        </div>

        <!-- Action Button (Activer uniquement !) -->
        <div class="mt-6 flex flex-col gap-3">
            <button type="button" @click="activate()" :disabled="loading"
                class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-[#F26B1D] via-[#FF7E36] to-[#D04A0A] hover:from-[#E05B0D] hover:to-[#B93E07] text-white font-extrabold text-base sm:text-lg shadow-xl shadow-orange-500/30 active:scale-[0.98] transition flex items-center justify-center gap-2.5 cursor-pointer">
                <span x-show="loading" class="animate-spin inline-block mr-1">⌛</span>
                <svg x-show="!loading" class="size-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span>{{ __('push.modal_btn') }}</span>
            </button>

            <!-- Fallback continue button only visible if permission was denied, to prevent user lockout -->
            <button type="button" x-show="status === 'denied'" x-cloak @click="dismiss()"
                class="w-full py-2.5 px-4 text-xs sm:text-sm font-semibold text-slate-500 hover:text-slate-800 transition">
                {{ __('push.modal_continue') }} &rarr;
            </button>
        </div>

        <!-- Subtext reassurance -->
        <p class="mt-4 text-[11px] text-slate-400">
            🔒 100% privado y seguro · Sin spam · Desactivable en cualquier momento
        </p>
    </div>
</div>
