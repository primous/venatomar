<x-layouts::app :title="__('account.dashboard_title').' · '.config('venatomar.brand')" noindex>
    <section class="rounded-b-[2rem] bg-gradient-to-b from-mint-200 to-mint-100/30 py-10 md:py-16">
        <div class="container-pf max-w-[880px]">
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="h2">{{ __('account.dashboard_title') }}</h1>
                    <p class="mt-1 text-ink-500">{{ __('account.dashboard_intro') }}</p>
                </div>
                <form method="POST" action="{{ route('account.logout') }}">
                    @csrf
                    <x-button type="submit" variant="secondary"><x-icon name="logout" /> {{ __('account.logout') }}</x-button>
                </form>
            </div>
            <livewire:account-dashboard :locale="app()->getLocale()" />
        </div>
    </section>
</x-layouts::app>
