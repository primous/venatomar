{!! __('mail.deletion.title') !!}

{!! __('mail.deletion.intro') !!}

@foreach ($rows as $label => $value)
{!! $label !!}: {!! $value !!}
@endforeach
