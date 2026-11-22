<x-layouts::app :title="__('apply.meta.title', ['brand' => config('venatomar.brand')])" :description="__('apply.meta.description')">
    <section class="rounded-b-[2rem] bg-gradient-to-b from-mint-200 to-mint-100/30 pt-8 pb-12 md:pt-12 md:pb-16">
        <div class="mx-auto w-full max-w-[580px] px-4">
            <h1 class="text-center font-display text-[1.75rem] font-extrabold text-ink-900 md:text-[2rem]">{{ __('apply.title') }}</h1>
            <p class="mt-2 text-center text-ink-700">{{ __('apply.subtitle') }}</p>
            <div class="mt-6">
                <livewire:apply-loan
                    :locale="app()->getLocale()"
                    channel="ayuda"
                    admin-email="ayuda@venatomar.com"
                    thanks-route="solicitar.emprestimo.thanks"
                />
            </div>
        </div>
    </section>
</x-layouts::app>
