@php use App\Support\Venatomar as PF; $latest = $leads->first(); @endphp
<div class="space-y-8">
    {{-- Requests --}}
    <section class="rounded-lg bg-white p-5 shadow-pop md:p-8" aria-labelledby="requests-title">
        <h2 id="requests-title" class="h3">{{ __('account.requests') }}</h2>
        @if ($leads->isEmpty())
            <p class="mt-4 text-ink-500">{{ __('account.no_requests') }}</p>
        @else
            <div class="mt-4 overflow-x-auto">
                <table class="w-full min-w-[560px] text-left text-sm">
                    <thead class="border-b border-line text-ink-500">
                        <tr>
                            <th scope="col" class="py-3 pr-4 font-semibold">{{ __('account.col.reference') }}</th>
                            <th scope="col" class="py-3 pr-4 font-semibold">{{ __('account.col.date') }}</th>
                            <th scope="col" class="py-3 pr-4 text-right font-semibold">{{ __('form.amount') }}</th>
                            <th scope="col" class="py-3 pr-4 text-right font-semibold">{{ __('form.term') }}</th>
                            <th scope="col" class="py-3 font-semibold">{{ __('account.col.status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($leads as $lead)
                            <tr wire:key="lead-{{ $lead->id }}">
                                <td class="py-3 pr-4 font-semibold text-ink-900 tabular-nums">{{ $lead->reference }}</td>
                                <td class="py-3 pr-4 tabular-nums">{{ $lead->created_at->translatedFormat('j M Y') }}</td>
                                <td class="py-3 pr-4 text-right tabular-nums">{{ PF::money($lead->amount, $lead->countryInfo()) }}</td>
                                <td class="py-3 pr-4 text-right tabular-nums">{{ trans_choice('form.months', $lead->term_months, ['count' => $lead->term_months]) }}</td>
                                <td class="py-3"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $lead->status->badgeClasses() }}">{{ $lead->status->label() }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    {{-- Personal data (masked) --}}
    @if ($latest)
        <section class="rounded-lg bg-white p-5 shadow-pop md:p-8" aria-labelledby="data-title">
            <h2 id="data-title" class="h3">{{ __('account.my_data') }}</h2>
            <p class="text-small mt-1">{{ __('account.my_data_help') }}</p>
            <dl class="mt-5 grid gap-x-6 gap-y-4 sm:grid-cols-2">
                <div><dt class="text-small">{{ __('form.first_name') }}</dt><dd class="font-semibold text-ink-900">{{ $latest->first_name }} {{ mb_substr($latest->last_name, 0, 1) }}.</dd></div>
                <div><dt class="text-small">{{ __('form.email') }}</dt><dd class="font-semibold text-ink-900">{{ PF::maskEmail($latest->email) }}</dd></div>
                <div><dt class="text-small">{{ __('form.phone') }}</dt><dd class="font-semibold text-ink-900 tabular-nums">{{ PF::maskTail($latest->phone, 3) }}</dd></div>
                <div><dt class="text-small">{{ $latest->payout_method === 'mobile_money' ? __('form.mm_number') : __('account.bank') }}</dt><dd class="font-semibold text-ink-900 tabular-nums">{{ $latest->maskedPayout() ?: __('opt.payout_method.'.$latest->payout_method) }}</dd></div>
                @if ($latest->document_number)
                    <div><dt class="text-small">{{ __('account.document') }}</dt><dd class="font-semibold text-ink-900 tabular-nums">{{ PF::maskTail($latest->document_number, 3) }}</dd></div>
                @endif
                <div><dt class="text-small">{{ __('form.geo_city') }}</dt><dd class="font-semibold text-ink-900">{{ $latest->geo_city }}</dd></div>
            </dl>
        </section>
    @endif

    {{-- Review (moderated; badge "Verificado" because the applicant has a real request) --}}
    <livewire:leave-review :locale="$locale" />

    {{-- GDPR erasure --}}
    <section class="rounded-lg bg-white p-5 shadow-pop md:p-8" aria-labelledby="delete-title">
        <h2 id="delete-title" class="h3">{{ __('account.delete_title') }}</h2>
        @if ($pendingDeletion)
            <p role="status" class="mt-3 rounded-md bg-brand-100 p-4 text-sm font-medium text-brand-700">
                {{ __('account.delete_pending', ['date' => $pendingDeletion->created_at->translatedFormat('j M Y')]) }}
            </p>
        @elseif ($confirmingDeletion)
            <p class="mt-3">{{ __('account.delete_confirm') }}</p>
            <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                <x-button wire:click="requestDeletion" wire-target="requestDeletion">{{ __('account.delete_yes') }}</x-button>
                <x-button variant="ghost" wire:click="$set('confirmingDeletion', false)">{{ __('account.delete_cancel') }}</x-button>
            </div>
        @else
            <p class="mt-2 text-ink-500">{{ __('account.delete_text') }}</p>
            <x-button variant="secondary" class="mt-4" wire:click="$set('confirmingDeletion', true)">
                <x-icon name="trash" /> {{ __('account.delete_button') }}
            </x-button>
        @endif
    </section>
</div>
