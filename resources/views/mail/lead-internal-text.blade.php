{!! __('mail.internal.title', ['reference' => $lead->reference]) !!}
{!! __('mail.internal.reply_hint') !!}
@foreach ($rows as $section => $fields)

== {!! $section !!} ==
@foreach ($fields as $label => $value)
{!! $label !!}: {!! $value !!}
@endforeach
@endforeach
