{{-- PLANTILLA: debe ser validada por un jurista antes de su publicación. --}}
<h2>1. Responsable del tratamiento</h2>
@include('legal.partials.identity', ['l' => ['company' => 'Responsable', 'tax' => 'NIF', 'register' => 'Registro Mercantil', 'address' => 'Domicilio', 'email' => 'Contacto de privacidad', 'credit' => 'N.º de registro como intermediario de crédito']])
<p>Delegado de protección de datos (si se ha designado): {!! $legal->dpo_email !!}.</p>

<h2>2. Datos que tratamos</h2>
<ul>
    <li><strong>Ingresos:</strong> importe y plazo solicitados, ingresos netos, situación laboral, inclusión en ficheros de morosidad.</li>
    <li><strong>Datos personales:</strong> nombre, apellidos, documento de identidad, fecha de nacimiento, correo y teléfono.</li>
    <li><strong>Domicilio:</strong> código postal, provincia, localidad, calle, número y piso.</li>
    <li><strong>Cuenta bancaria:</strong> IBAN (o, según el país, RIB o número Mobile Money).</li>
    <li><strong>Información adicional:</strong> sexo, finalidad, estado civil, sector, estudios, vivienda, gastos, deudas y vehículo.</li>
    <li><strong>Datos técnicos:</strong> dirección IP, navegador, idioma, campaña de origen (UTM), fecha y versión del consentimiento.</li>
</ul>
<p>El documento de identidad, la cuenta bancaria y la fecha de nacimiento se guardan cifrados.</p>

<h2>3. Finalidades y bases jurídicas</h2>
<ul>
    <li><strong>Tramitar tu solicitud</strong> y transmitirla a las entidades: medidas precontractuales solicitadas por ti (art. 6.1.b RGPD).</li>
    <li><strong>Comunicaciones comerciales</strong>: solo con tu consentimiento, que puedes retirar en cualquier momento (art. 6.1.a RGPD).</li>
    <li><strong>Prevención del fraude y seguridad</strong> (control de envíos, registros técnicos): interés legítimo (art. 6.1.f RGPD).</li>
    <li><strong>Cumplimiento de obligaciones legales</strong>: art. 6.1.c RGPD.</li>
</ul>

<h2>4. Destinatarios</h2>
<ul>
    <li>Entidades financieras colaboradoras a las que se transmite tu solicitud: {!! $legal->partners !!}.</li>
    <li>Proveedor de alojamiento y correo: {!! $legal->hosting_provider !!}, como encargado del tratamiento.</li>
    <li>Autoridades públicas, cuando lo exija la ley.</li>
</ul>
<p>Nunca vendemos tus datos. Los campos del formulario no se comparten con herramientas de analítica.</p>

<h2>5. Transferencias internacionales</h2>
<p>Los datos se alojan en el Espacio Económico Europeo. Si aceptas cookies de estadística o marketing, Google o Meta pueden tratar datos de navegación fuera del EEE con las garantías previstas en el RGPD (decisión de adecuación o cláusulas contractuales tipo).</p>

<h2>6. Plazo de conservación</h2>
<p>Conservamos los datos de la solicitud durante {!! $legal->retention !!} y, después, bloqueados durante los plazos de prescripción legal. Los datos técnicos de seguridad se conservan el tiempo mínimo necesario.</p>

<h2>7. Tus derechos</h2>
<p>Puedes ejercer los derechos de acceso, rectificación, supresión, oposición, limitación y portabilidad escribiendo a <a href="mailto:{!! $legal->contact_email !!}">{!! $legal->contact_email !!}</a> o desde {!! $legal->accountLink('Mi cuenta') !!}, donde puedes solicitar la eliminación de tus datos.</p>
<p>Si consideras que no hemos atendido correctamente tus derechos, puedes reclamar ante la {!! $legal->authority() !!}.</p>

<h2>8. Decisiones automatizadas</h2>
<p>{!! $legal->brand() !!} no toma decisiones basadas únicamente en tratamientos automatizados que produzcan efectos jurídicos sobre ti. La decisión sobre el préstamo corresponde a cada entidad, que te informará de sus propios tratamientos.</p>

<h2>9. Cookies</h2>
<p>Consulta la {!! $legal->link('cookie-policy', 'política de cookies') !!}.</p>
