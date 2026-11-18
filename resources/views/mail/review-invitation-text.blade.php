@php use App\Support\Venatomar as PF; @endphp
{!! __('mail.review.title', ['name' => $firstName]) !!}

{!! __('mail.review.intro', ['reference' => $reference, 'brand' => config('venatomar.brand')]) !!}

{!! __('mail.review.button') !!}: {!! $reviewUrl !!}

{!! __('mail.review.once') !!}

--
{!! __('mail.footer.why', ['brand' => config('venatomar.brand')]) !!} {!! __('footer.privacy') !!}: {!! $privacyUrl !!}
{!! PF::legal('company_name') !!} · {!! PF::legal('address') !!} · {!! PF::legal('tax_id') !!}
