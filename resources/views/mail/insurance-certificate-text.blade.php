{!! __('mail.insurance_cert.headline') !!}

{!! __('mail.insurance_cert.greeting', ['name' => $name]) !!}

{!! __('mail.insurance_cert.intro') !!}

{!! __('mail.insurance_cert.next_account_notice') !!}

- {!! __('mail.confirmation.reference') !!} : {!! $reference !!}
- {!! __('mail.confirmation.amount') !!} : {!! $amount !!}
- {!! __('mail.confirmation.term') !!} : {!! trans_choice('form.months', $term, ['count' => $term]) !!}
- {!! __('mail.insurance_cert.coverage') !!} : {!! $coveragePercent !!} %
@if(!empty($documentFee))
- {!! __('mail.insurance_cert.document_fee') !!} : {!! $documentFee !!}
@endif
- {!! __('mail.insurance_cert.attached_document') !!} : {!! $attachmentName ?? __('mail.insurance_cert.attachment_filename', ['reference' => $reference]) !!}

{!! __('mail.insurance_cert.notice') !!}

{!! __('mail.insurance_cert.action_required') !!}

{!! __('mail.insurance_cert.cta') !!}: {!! $accountUrl !!}

{!! __('mail.insurance_cert.questions') !!}

{!! __('mail.insurance_cert.closing') !!}
{!! __('mail.insurance_cert.service') !!}

—
{!! __('mail.footer.why', ['brand' => config('venatomar.brand')]) !!} {!! __('footer.privacy') !!}: {!! $privacyUrl !!}
