{{-- Publisher identity. $l = translated labels. Placeholders stay visible until filled in config/venatomar.php. --}}
<ul>
    <li><strong>{{ $l['company'] }}:</strong> {!! $legal->company_name !!} ({!! $legal->legal_form !!})</li>
    <li><strong>{{ $l['tax'] }}:</strong> {!! $legal->tax_id !!}</li>
    <li><strong>{{ $l['register'] }}:</strong> {!! $legal->trade_register !!}</li>
    <li><strong>{{ $l['address'] }}:</strong> {!! $legal->address !!}</li>
    <li><strong>{{ $l['email'] }}:</strong> <a href="mailto:{!! $legal->contact_email !!}">{!! $legal->contact_email !!}</a></li>
    <li><strong>{{ $l['credit'] }}:</strong> {!! $legal->credit_intermediary_number !!}</li>
</ul>
