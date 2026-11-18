{{-- PLANTILLA: debe ser validada por un jurista antes de su publicación. --}}
<h2>1. ¿Qué son las cookies?</h2>
<p>Son pequeños archivos que el sitio guarda en tu navegador. {!! $legal->brand() !!} solo usa las cookies de la tabla siguiente. Las de estadística y marketing únicamente se activan si las aceptas.</p>

<h2>2. Cookies utilizadas</h2>
@include('legal.partials.cookies-table', ['t' => [
    'h' => ['Cookie', 'Finalidad', 'Duración', 'Titular', 'Categoría'],
    'own' => 'Propia',
    'session' => 'Mantiene tu sesión y el borrador del formulario.',
    'xsrf' => 'Protege los formularios contra peticiones falsificadas (CSRF).',
    'consent' => 'Recuerda tu elección sobre cookies y la versión de esta política.',
    'utm' => 'Recuerda la campaña de origen de tu visita (solo tras tu consentimiento).',
    'ga' => 'Google Analytics: medición agregada de audiencia.',
    'ads' => 'Google Ads: medición de conversiones.',
    'meta' => 'Meta Pixel: medición de campañas.',
    'minutes' => ':n minutos de inactividad',
    'days' => ':n días',
    'provider' => 'Según el proveedor',
    'necessary' => 'Necesaria', 'statistics' => 'Estadística', 'marketing' => 'Marketing',
    'none' => 'Actualmente no se instala ninguna cookie de terceros.',
]])

<h2>3. Gestionar tu consentimiento</h2>
<p>Puedes cambiar tu elección en cualquier momento con el botón «Configurar cookies» del pie de página. También puedes borrar las cookies desde la configuración de tu navegador.</p>
<p>Más información en la {!! $legal->link('privacy-policy', 'política de privacidad') !!}.</p>
