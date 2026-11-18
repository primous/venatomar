<div>
    @if ($sent)
        <div role="status" class="flex gap-3 rounded-md border-l-4 border-brand-600 bg-mint-100 p-4 text-brand-700">
            <x-icon name="mail" class="size-6 shrink-0" />
            <p class="font-medium">{{ __('account.sent') }}</p>
        </div>
        <button type="button" wire:click="$set('sent', false)" class="mt-4 inline-flex min-h-11 items-center font-semibold text-brand-700 underline underline-offset-2">{{ __('account.send_again') }}</button>
    @else
        <form wire:submit="send" novalidate class="space-y-4">
            <x-field.input name="email" type="email" inputmode="email" :label="__('form.email')" hidden-label autocomplete="email" />
            <x-field.input name="phone" type="tel" inputmode="tel" :label="__('form.phone')" hidden-label :prefix="$country->phonePrefix" autocomplete="tel-national" />
            <x-button type="submit" size="lg" block wire-target="send">{{ __('account.submit') }}</x-button>
            <p class="rounded-sm border-l-4 border-[#1D72C9] bg-[#EEF5FD] px-4 py-3 text-sm text-[#1B4F86]">{{ __('account.hint') }}</p>
        </form>
    @endif
</div>
