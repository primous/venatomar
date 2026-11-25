{!! __('mail.accepted.greeting', ['name' => $firstName]) !!}

{!! $messageBody !!}

{{ __('mail.confirmation.reference') }}: {!! $reference !!}
{{ __('mail.confirmation.cta') }}: {!! $accountUrl !!}

{!! strip_tags(__('mail.confirmation.questions', ['email' => $contactEmail])) !!}

{!! __('mail.confirmation.closing') !!} {!! __('mail.confirmation.team', ['brand' => config('venatomar.brand')]) !!}
