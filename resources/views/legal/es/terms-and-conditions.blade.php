{{-- PLANTILLA: debe ser validada por un jurista antes de su publicación. --}}
<h2>1. Identidad del titular</h2>
<p>El sitio web {!! $legal->brand() !!} ({!! $legal->url() !!}) es titularidad de:</p>
@include('legal.partials.identity', ['l' => ['company' => 'Razón social', 'tax' => 'NIF', 'register' => 'Registro Mercantil', 'address' => 'Domicilio', 'email' => 'Correo de contacto', 'credit' => 'N.º de registro como intermediario de crédito']])

<h2>2. Objeto del servicio</h2>
<p>{!! $legal->brand() !!} es una entidad de crédito y financiación personal online. Gestiona directamente las solicitudes de préstamo personal y concede préstamos a particulares conforme a las condiciones acordadas en el contrato suscrito con el usuario.</p>

<h2>3. Gratuidad del estudio</h2>
<p>La solicitud y el estudio de solvencia son totalmente gratuitos. {!! $legal->brand() !!} nunca pide al usuario un pago, un anticipo ni gastos de estudio por adelantado.</p>

<h2>4. Concesión directa del préstamo</h2>
<p>{!! $legal->brand() !!} evalúa y concede directamente la financiación tras la comprobación de la solvencia y la formalización del contrato con el prestatario.</p>

<h2>5. Condiciones de los préstamos</h2>
<p>El importe, el plazo, el tipo de interés, la TAE y el resto de condiciones se fijan formalmente tras estudiar la solicitud. Antes de firmar, se entrega al usuario la información normalizada europea (INE) y el contrato de préstamo.</p>

<h2>6. Ejemplo representativo</h2>
<x-legal-example />

<h2>7. Obligaciones del usuario</h2>
<ul>
    <li>Ser mayor de edad y actuar en nombre propio.</li>
    <li>Facilitar datos exactos, completos y actualizados.</li>
    <li>No utilizar datos de terceros ni el servicio con fines fraudulentos.</li>
</ul>

<h2>8. Responsabilidad</h2>
<p>{!! $legal->brand() !!} analiza cada solicitud conforme a criterios de solvencia y riesgo responsable. La concesión del préstamo queda sujeta a la aprobación de la solicitud y a la veracidad de los datos facilitados por el usuario. El sitio puede interrumpirse temporalmente por mantenimiento.</p>

<h2>9. Derecho de desistimiento</h2>
<p>El usuario puede desistir del contrato de préstamo celebrado con {!! $legal->brand() !!} en un plazo de 14 días naturales, sin indicar motivo, conforme a la normativa de crédito al consumo, devolviendo el capital recibido y los intereses devengados.</p>

<h2>10. Propiedad intelectual</h2>
<p>Los textos, diseños, logotipos y código del sitio pertenecen a {!! $legal->company_name !!} o a sus licenciantes. Queda prohibida su reproducción sin autorización.</p>

<h2>11. Modificación de las condiciones</h2>
<p>Estas condiciones pueden modificarse. La versión aplicable es la publicada en el momento de enviar la solicitud.</p>

<h2>12. Ley aplicable y jurisdicción</h2>
<p>Estas condiciones se rigen por la ley española. Para cualquier controversia serán competentes los juzgados de {!! $legal->jurisdiction !!}, sin perjuicio del fuero que corresponda al consumidor.</p>
