{{-- MODELY: tsy maintsy hamarinin'ny mpahay lalàna alohan'ny hamoahana azy. Mila hamarinin'ny mpiteny teratany koa. --}}
<h2>1. Inona no atao hoe cookies?</h2>
<p>Rakitra kely tehirizin'ny tranonkala ao amin'ny mpitety tranonkalanao izy ireo. Ireo cookies ao amin'ny tabilao etsy ambany ihany no ampiasain'ny {!! $legal->brand() !!}. Ny cookies antontan'isa sy varotra dia tsy mandeha raha tsy manaiky ianao.</p>

<h2>2. Cookies ampiasaina</h2>
@include('legal.partials.cookies-table', ['t' => [
    'h' => ['Cookie', 'Tanjona', 'Faharetana', 'Tompony', 'Sokajy'],
    'own' => 'Anay',
    'session' => 'Mitazona ny fidiranao sy ny drafitry ny taratasy fangatahana.',
    'xsrf' => 'Miaro ny taratasy fangatahana amin\'ny fangatahana sandoka (CSRF).',
    'consent' => 'Mitadidy ny safidinao momba ny cookies sy ny dikan-tenin\'ity politika ity.',
    'utm' => 'Mitadidy ny dokam-barotra niavianao (raha nanaiky ianao ihany).',
    'ga' => 'Google Analytics: fandrefesana ankapobeny ny mpitsidika.',
    'ads' => 'Google Ads: fandrefesana ny vokatry ny dokam-barotra.',
    'meta' => 'Meta Pixel: fandrefesana ny dokam-barotra.',
    'minutes' => 'Minitra :n tsy misy hetsika',
    'days' => ':n andro',
    'provider' => 'Araka ny mpamatsy',
    'necessary' => 'Ilaina', 'statistics' => 'Antontan\'isa', 'marketing' => 'Varotra',
    'none' => 'Amin\'izao fotoana izao dia tsy misy cookies avy amin\'olon-kafa apetraka.',
]])

<h2>3. Fitantanana ny fanekenao</h2>
<p>Azonao ovaina na oviana na oviana ny safidinao amin'ny bokotra «Fikirakirana cookies» eo ambany pejy. Azonao fafana koa ny cookies ao amin'ny fikirakirana ny mpitety tranonkalanao.</p>
<p>Fanazavana fanampiny ao amin'ny {!! $legal->link('privacy-policy', 'politikan\'ny fiainana manokana') !!}.</p>
