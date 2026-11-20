@php use App\Support\Venatomar as PF; @endphp
{!! __('mail.confirmation.title', ['name' => $firstName]) !!}

{!! __('mail.confirmation.intro', ['brand' => config('venatomar.brand')]) !!}

{!! __('mail.confirmation.summary_title') !!}
- {!! __('mail.confirmation.reference') !!} : {!! $reference !!}
- {!! __('mail.confirmation.amount') !!} : {!! PF::money($amount, $country) !!}
- {!! __('mail.confirmation.term') !!} : {!! trans_choice('form.months', $term, ['count' => $term]) !!}
- {!! __('mail.confirmation.date') !!} : {!! $date !!}

{!! __('mail.confirmation.next_title') !!}
1. {!! __('mail.confirmation.next1') !!}
2. {!! __('mail.confirmation.next2') !!}

{!! __('mail.confirmation.questions', ['email' => $contactEmail]) !!}

{!! __('mail.confirmation.closing') !!}
{!! __('mail.confirmation.team', ['brand' => config('venatomar.brand')]) !!}

—
{!! __('mail.footer.why', ['brand' => config('venatomar.brand')]) !!} {!! __('footer.privacy') !!}: {!! $privacyUrl !!}
{!! PF::legal('company_name') !!} · {!! PF::legal('address') !!} · {!! PF::legal('tax_id') !!}
