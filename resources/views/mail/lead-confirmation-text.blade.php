@php use App\Support\Venatomar as PF; @endphp
{!! __('mail.confirmation.title', ['name' => $firstName]) !!}

{!! __('mail.confirmation.intro') !!}

{!! __('mail.confirmation.reference') !!}: {!! $reference !!}
{!! __('form.amount') !!}: {!! PF::money($amount, $country) !!}
{!! __('form.term') !!}: {!! trans_choice('form.months', $term, ['count' => $term]) !!}

{!! __('thanks.next_title') !!}
1. {!! __('thanks.next1') !!}
2. {!! __('thanks.next2') !!}
3. {!! __('thanks.next3') !!}

{!! __('nav.account') !!}: {!! $accountUrl !!}

{!! __('mail.confirmation.not_you') !!}

--
{!! __('mail.footer.why', ['brand' => config('venatomar.brand')]) !!} {!! __('footer.privacy') !!}: {!! $privacyUrl !!}
{!! PF::legal('company_name') !!} · {!! PF::legal('address') !!} · {!! PF::legal('tax_id') !!}
