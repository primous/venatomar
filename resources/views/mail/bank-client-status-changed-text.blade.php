{!! __('mail.portal.welcome_title', ['name' => $name]) !!}

@if($isFrozen)
{!! __('mail.portal.status_frozen_intro') !!}
@if(!empty($reason))

{!! $reason !!}
@endif

{!! __('mail.portal.status_frozen_action') !!}
@else
{!! __('mail.portal.status_active_intro') !!}
@endif

{!! __('mail.portal.btn_access') !!} : {!! $loginUrl !!}

{!! __('mail.accepted.closing') !!}
{!! __('mail.accepted.service') !!}

—
{!! __('mail.footer.why', ['brand' => config('venatomar.brand')]) !!} {!! __('footer.privacy') !!}: {!! $privacyUrl !!}
