<div id="leave-review" class="rounded-lg bg-white p-5 shadow-pop md:p-8">
    <h3 class="font-display text-xl font-bold text-ink-900">{{ __('reviews.form.title') }}</h3>

    @if ($sent)
        <div role="status" class="mt-4 flex gap-3 rounded-md border-l-4 border-brand-600 bg-mint-100 p-4 text-brand-700">
            <x-icon name="check" class="size-6 shrink-0 stroke-[2.5]" />
            <div>
                <p class="font-bold">{{ __('reviews.form.thanks_title') }}</p>
                <p class="mt-0.5 text-sm">{{ __('reviews.form.thanks_text') }}</p>
            </div>
        </div>
        <button type="button" wire:click="again" class="mt-4 inline-flex min-h-11 items-center font-semibold text-brand-700 underline underline-offset-2">{{ __('reviews.form.again') }}</button>
    @else
        <p class="mt-1 text-sm text-ink-500">{{ __('reviews.form.intro', ['brand' => config('venatomar.brand')]) }}</p>
        @if ($verified)
            <p class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-mint-100 px-3 py-1 text-xs font-semibold text-brand-700">
                <x-icon name="check" class="size-4 stroke-[2.5]" /> {{ __('reviews.form.verified_note') }}
            </p>
        @endif

        <form wire:submit="submit" novalidate class="mt-5 space-y-4">
            <div class="sr-only" aria-hidden="true">
                <label for="r-website">{{ __('form.website') }}</label>
                <input type="text" id="r-website" wire:model="website" tabindex="-1" autocomplete="off">
            </div>

            {{-- Star picker: real radio buttons, keyboard friendly --}}
            <fieldset>
                <legend class="text-[0.9375rem] font-semibold text-ink-900">{{ __('reviews.form.rating') }}</legend>
                <div class="mt-2 flex gap-1.5" role="radiogroup">
                    @for ($i = 1; $i <= 5; $i++)
                        <label class="cursor-pointer">
                            <input type="radio" name="rating" value="{{ $i }}" class="peer sr-only" wire:click="setRating({{ $i }})" @checked($rating === $i)>
                            <span @class([
                                'grid size-11 place-items-center rounded-sm text-2xl leading-none transition peer-focus-visible:ring-2 peer-focus-visible:ring-brand-600',
                                'bg-star-500 text-white' => $i <= $rating,
                                'bg-field text-ink-300 hover:bg-mint-200' => $i > $rating,
                            ]) aria-hidden="true">★</span>
                            <span class="sr-only">{{ __('reviews.stars_aria', ['rating' => $i]) }}</span>
                        </label>
                    @endfor
                </div>
                @error('rating')
                    <p role="alert" class="mt-1.5 flex items-start gap-1.5 text-sm font-medium text-danger-600"><x-icon name="alert" class="mt-0.5 size-4 shrink-0" />{{ $message }}</p>
                @enderror
            </fieldset>

            <div class="grid gap-4 md:grid-cols-2">
                <x-field.input name="author_name" :label="__('reviews.form.name')" :help="__('reviews.form.name_hint')" autocomplete="given-name" maxlength="60" />
                <x-field.input name="title" :label="__('reviews.form.review_title')" optional maxlength="120" />
            </div>

            <div class="space-y-1.5">
                <label for="r-body" class="block text-[0.9375rem] font-semibold text-ink-900">{{ __('reviews.form.body') }}</label>
                <textarea id="r-body" wire:model.blur="body" rows="4" maxlength="1500"
                    @error('body') aria-invalid="true" aria-describedby="r-body-error" @enderror
                    @class([
                        'block w-full rounded-sm border-b-2 bg-field px-4 py-3 text-base text-ink-900 transition focus:bg-white focus:ring-1 focus:ring-line focus:outline-none',
                        'border-danger-600' => $errors->has('body'),
                        'border-ink-300/50 hover:border-ink-300 focus:border-brand-600' => ! $errors->has('body'),
                    ])></textarea>
                @error('body')
                    <p id="r-body-error" role="alert" class="flex items-start gap-1.5 text-sm font-medium text-danger-600"><x-icon name="alert" class="mt-0.5 size-4 shrink-0" />{{ $message }}</p>
                @enderror
            </div>

            <x-field.checkbox name="consent">{{ __('reviews.form.consent', ['brand' => config('venatomar.brand')]) }}</x-field.checkbox>

            @error('form')
                <p role="alert" class="rounded-sm bg-danger-100 px-4 py-3 text-sm font-medium text-danger-600">{{ $message }}</p>
            @enderror

            <x-button type="submit" size="lg" class="w-full sm:w-auto" wire-target="submit">{{ __('reviews.form.submit') }}</x-button>
            <p class="text-xs text-ink-500">{{ __('reviews.form.moderation') }}</p>
        </form>
    @endif
</div>
