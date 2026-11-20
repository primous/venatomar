@php use App\Support\Venatomar as PF; @endphp
{!! __('mail.accepted.greeting') !!}

{!! __('mail.accepted.intro') !!}

- {!! __('mail.confirmation.reference') !!} : {!! $reference !!}
- {!! __('mail.confirmation.amount') !!} : {!! PF::money($amount, $country) !!}
- {!! __('mail.confirmation.term') !!} : {!! trans_choice('form.months', $term, ['count' => $term]) !!}
@if(!empty($documentFee))
- {!! __('mail.accepted.document_fee') !!} : {!! $documentFee !!}
@endif
- {!! __('mail.accepted.attached_document') !!} : {!! $docReference ?: $reference !!}.pdf

{!! __('mail.accepted.document_notice') !!}

{!! __('mail.accepted.action_required') !!}

{!! __('mail.accepted.questions') !!}

{!! __('mail.accepted.closing') !!}
{!! __('mail.accepted.service') !!}

—
{!! __('mail.footer.why', ['brand' => config('venatomar.brand')]) !!} {!! __('footer.privacy') !!}: {!! $privacyUrl !!}
{!! PF::legal('company_name') !!} · {!! PF::legal('address') !!} · {!! PF::legal('tax_id') !!}
