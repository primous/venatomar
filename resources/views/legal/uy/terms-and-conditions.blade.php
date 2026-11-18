{{-- PLANTILLA: debe ser validada por un jurista antes de su publicación. --}}
<h2>1. Identidad del titular</h2>
<p>El sitio web {!! $legal->brand() !!} ({!! $legal->url() !!}) es titularidad de:</p>
@include('legal.partials.identity', ['l' => ['company' => 'Razón social', 'tax' => 'NIF', 'register' => 'Registro Mercantil', 'address' => 'Domicilio', 'email' => 'Correo de contacto', 'credit' => 'N.º de registro como intermediario de crédito']])

<h2>2. Objeto del servicio</h2>
<p>{!! $legal->brand() !!} es un servicio gratuito de comparación e intermediación de préstamos personales. Recoge la solicitud del usuario y la transmite a entidades financieras colaboradoras. <strong>{!! $legal->brand() !!} no concede préstamos</strong>, no decide sobre su aprobación y no forma parte del contrato de préstamo.</p>

<h2>3. Gratuidad para el usuario</h2>
<p>El uso del comparador es gratuito. {!! $legal->brand() !!} nunca pide al usuario un pago, un anticipo ni gastos de estudio.</p>

<h2>4. Remuneración</h2>
<p>{!! $legal->brand() !!} puede recibir una remuneración de las entidades colaboradoras cuando se les transmite una solicitud o se formaliza un contrato. Esta remuneración no aumenta el coste del préstamo para el usuario. Entidades colaboradoras: {!! $legal->partners !!}.</p>

<h2>5. Condiciones de los préstamos</h2>
<p>El importe, el plazo, el tipo de interés, la TAE y el resto de condiciones los fija exclusivamente cada entidad tras estudiar la solicitud. Antes de firmar, la entidad debe entregar al usuario la información normalizada europea (INE) y el contrato.</p>

<h2>6. Ejemplo representativo</h2>
<x-legal-example />

<h2>7. Obligaciones del usuario</h2>
<ul>
    <li>Ser mayor de edad y actuar en nombre propio.</li>
    <li>Facilitar datos exactos, completos y actualizados.</li>
    <li>No utilizar datos de terceros ni el servicio con fines fraudulentos.</li>
</ul>

<h2>8. Responsabilidad</h2>
<p>{!! $legal->brand() !!} no garantiza que una entidad acepte la solicitud ni las condiciones que ofrezca. No responde de las decisiones, ofertas o contratos de las entidades, ni de daños derivados de datos inexactos facilitados por el usuario. El sitio puede interrumpirse temporalmente por mantenimiento.</p>

<h2>9. Derecho de desistimiento</h2>
<p>El usuario puede desistir del contrato de préstamo celebrado con la entidad en un plazo de 14 días naturales, sin indicar motivo, conforme a la normativa de crédito al consumo. Debe comunicarlo a la entidad y devolver el capital y los intereses devengados.</p>

<h2>10. Propiedad intelectual</h2>
<p>Los textos, diseños, logotipos y código del sitio pertenecen a {!! $legal->company_name !!} o a sus licenciantes. Queda prohibida su reproducción sin autorización.</p>

<h2>11. Modificación de las condiciones</h2>
<p>Estas condiciones pueden modificarse. La versión aplicable es la publicada en el momento de enviar la solicitud.</p>

<h2>12. Ley aplicable y jurisdicción</h2>
<p>Estas condiciones se rigen por la ley española. Para cualquier controversia serán competentes los juzgados de {!! $legal->jurisdiction !!}, sin perjuicio del fuero que corresponda al consumidor.</p>
