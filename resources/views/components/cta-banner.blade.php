@props(['title', 'text' => null, 'cta', 'href'])
@php
    $symbol = ['EUR' => '€', 'GBP' => '£', 'MGA' => 'Ar'][app(\App\Support\Country::class)->currency] ?? '€';
    $coins = [
        'left-[6%] top-[14%] w-14 -rotate-12 md:w-20',
        'left-[14%] top-[38%] w-20 rotate-12 md:w-28',
        'left-[2%] top-[62%] w-16 -rotate-6 md:w-24',
        'left-[18%] bottom-[8%] w-9 rotate-45 md:w-12',
        'right-[2%] -top-[6%] w-16 rotate-12 md:w-24',
        'right-[14%] top-[22%] w-20 -rotate-12 md:w-28',
        'right-[4%] top-[50%] w-20 rotate-[24deg] md:w-28',
        'right-[16%] bottom-[10%] w-14 -rotate-6 md:w-20',
    ];
@endphp
<section class="py-8 md:py-12">
    <div class="container-pf">
        <div class="relative overflow-hidden rounded-xl bg-gradient-to-b from-[#1CCB9E] to-[#0FA57E] px-6 pt-10 text-center md:pt-12">
            @foreach ($coins as $i => $pos)
                @include('partials.coin', ['class' => 'pointer-events-none absolute drop-shadow-[0_10px_14px_rgb(0_60_40/.3)] '.$pos.($i % 2 ? ' hidden sm:block' : ''), 'symbol' => $symbol])
            @endforeach
            <div class="relative">
                <h2 class="font-display text-[clamp(1.75rem,3.5vw,2.5rem)] leading-[1.15] font-extrabold text-white">{{ $title }}</h2>
                @if ($text)
                    <p class="mt-1 font-medium text-white/95">{{ $text }}</p>
                @endif
                <x-button variant="white" :href="$href" class="mt-5 text-ink-900">{{ $cta }}</x-button>
            </div>
            @include('partials.gift', ['class' => 'pointer-events-none relative mx-auto mt-6 block w-44 md:w-52'])
        </div>
    </div>
</section>
