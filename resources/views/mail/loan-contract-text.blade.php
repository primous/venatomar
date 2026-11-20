{!! __('mail.contract.greeting', ['name' => $name]) !!}

{!! __('mail.contract.intro') !!}

- {!! __('mail.confirmation.reference') !!} : {!! $reference !!}
- {!! __('mail.confirmation.amount') !!} : {!! $amount !!}
@if(!empty($monthlyPayment))
- {!! __('mail.contract.monthly_payment') !!} : {!! $monthlyPayment !!}
@endif
- {!! __('mail.confirmation.term') !!} : {!! trans_choice('form.months', $term, ['count' => $term]) !!}
@if(!empty($interestRate))
- {!! __('mail.contract.interest_rate') !!} : {!! number_format((float) $interestRate, 2, ',', ' ') !!} %
@endif
@if(!empty($documentFee))
- {!! __('mail.contract.document_fee') !!} : {!! $documentFee !!}
@endif
- {!! __('mail.contract.attached_document') !!} : Contrat_Pret_{!! $reference !!}.pdf

{!! __('mail.contract.instructions_title') !!}
1. {!! __('mail.contract.step1') !!}
2. {!! __('mail.contract.step2') !!}

{!! __('mail.confirmation.questions', ['email' => $contactEmail]) !!}

{!! __('mail.contract.closing') !!}
{!! __('mail.contract.service') !!}

—
{!! __('mail.footer.why', ['brand' => config('venatomar.brand')]) !!} {!! __('footer.privacy') !!}: {!! $privacyUrl !!}
