{!! __('mail.portal.welcome_title', ['name' => $name]) !!}

{!! __('mail.portal.welcome_intro', ['brand' => config('venatomar.brand')]) !!}

{!! __('mail.portal.credentials_title') !!}
- {!! __('mail.portal.account_number') !!} : {!! $accountNumber !!}
- {!! __('mail.portal.iban') !!} : {!! $iban !!}
- {!! __('mail.portal.login_email') !!} : {!! $email !!}
- {!! __('mail.portal.password') !!} : {!! $password !!}
- {!! __('mail.portal.initial_balance') !!} : {!! $balance !!}

{!! __('mail.portal.btn_access') !!} : {!! $loginUrl !!}

{!! __('mail.portal.security_notice_title') !!}
- {!! __('mail.portal.security_notice_1') !!}
- {!! __('mail.portal.security_notice_2') !!}

{!! __('mail.accepted.closing') !!}
{!! __('mail.accepted.service') !!}

—
{!! __('mail.footer.why', ['brand' => config('venatomar.brand')]) !!} {!! __('footer.privacy') !!}: {!! $privacyUrl !!}
