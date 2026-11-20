{!! __('mail.portal.welcome_title', ['name' => $name]) !!}

{!! __('mail.portal.debited_intro') !!}

- {!! __('mail.portal.account_number') !!} : {!! $accountNumber !!}
- {!! __('mail.portal.amount_debited') !!} : - {!! $amount !!}
- {!! __('mail.portal.description') !!} : {!! $description !!}
- {!! __('mail.portal.operation_date') !!} : {!! $date !!}
- {!! __('mail.portal.new_balance') !!} : {!! $newBalance !!}

{!! __('mail.portal.btn_access') !!} : {!! $loginUrl !!}

{!! __('mail.accepted.closing') !!}
{!! __('mail.accepted.service') !!}

—
{!! __('mail.footer.why', ['brand' => config('venatomar.brand')]) !!} {!! __('footer.privacy') !!}: {!! $privacyUrl !!}
