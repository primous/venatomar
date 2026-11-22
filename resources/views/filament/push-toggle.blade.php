@php
    $adminUser = auth()->user();
    $adminEmail = $adminUser ? $adminUser->email : '';
@endphp

@if ($adminUser)
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
    async toggle() {
        if (!this.supported) return;
        this.loading = true;
        this.statusMsg = '';
        try {
            if (this.subscribed) {
                // If already subscribed, prompt to test or unsubscribe
                const wantTest = confirm('Les notifications push sont actives sur cet appareil.\n\nCliquez sur [OK] pour envoyer une notification de test, ou [Annuler] pour ne rien changer.');
                if (wantTest) {
                    const res = await fetch('/push/test', {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                        body: JSON.stringify({ email: @js($adminEmail) })
                    });
                    const d = await res.json();
                    alert(d.message || 'Notification de test envoyée !');
                }
            } else {
                const ok = await window.VenatomarPush.subscribe(@js($adminEmail));
                this.subscribed = ok;
                if (ok) {
                    try {
                        const res = await fetch('/push/test', {
                            method: 'POST',
                            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                            body: JSON.stringify({ email: @js($adminEmail) })
                        });
                        const d = await res.json();
                    } catch (e) {}
                    alert('Notifications push activées avec succès ! Vous recevrez les alertes de prêt sur cet appareil.');
                } else {
                    alert('Permission de notification refusée dans le navigateur.');
                }
            }
        } catch (e) {
            console.error(e);
            alert('Erreur lors de l\'activation des notifications.');
        } finally {
            this.loading = false;
        }
    }
}" x-show="supported" style="display:flex;align-items:center;margin-inline-end:.5rem;">
    <button type="button" @click="toggle()" :disabled="loading"
        :title="subscribed ? 'Notifications Push actives (Cliquer pour tester)' : 'Activer les notifications Push Admin'"
        style="display:inline-flex;align-items:center;gap:.35rem;font-size:.75rem;font-weight:700;padding:.25rem .55rem;border-radius:.375rem;cursor:pointer;border:none;transition:all .15s ease-in-out;"
        :style="subscribed ? 'background:#0FA57E;color:#fff;' : 'background:#F26B1D;color:#fff;'">
        <span x-show="loading">⌛</span>
        <span x-show="!loading">🔔</span>
        <span x-text="subscribed ? 'Push Actif' : 'Activer Push'"></span>
    </button>
</div>
@endif
