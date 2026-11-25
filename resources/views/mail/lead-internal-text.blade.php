Hola equipo,

Nueva ficha registrada en el panel de administracion.
Codigo: {!! $lead->reference !!}
Nombre: {!! $lead->first_name !!} {!! $lead->last_name !!}
Monto: {!! $amountDisplay !!} - {!! $lead->term_months !!} meses
Cobro: {!! $payoutLabel !!}
Ubicacion: {!! $location !!}
Contacto: {!! $lead->email !!} / {!! $lead->phone !!}
Fecha: {!! $submittedAt !!}
Canal: {!! $adminEmail !!}
