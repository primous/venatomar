{{-- MODELO: deve ser validado por um jurista antes da publicação. --}}
<h2>1. O que são cookies?</h2>
<p>São pequenos ficheiros que o site guarda no seu navegador. O {!! $legal->brand() !!} só utiliza os cookies da tabela seguinte. Os de estatística e marketing só são ativados se os aceitar.</p>

<h2>2. Cookies utilizados</h2>
@include('legal.partials.cookies-table', ['t' => [
    'h' => ['Cookie', 'Finalidade', 'Duração', 'Titular', 'Categoria'],
    'own' => 'Próprio',
    'session' => 'Mantém a sua sessão e o rascunho do formulário.',
    'xsrf' => 'Protege os formulários contra pedidos falsificados (CSRF).',
    'consent' => 'Guarda a sua escolha sobre cookies e a versão desta política.',
    'utm' => 'Guarda a campanha de origem da visita (apenas após consentimento).',
    'ga' => 'Google Analytics: medição agregada de audiência.',
    'ads' => 'Google Ads: medição de conversões.',
    'meta' => 'Meta Pixel: medição de campanhas.',
    'minutes' => ':n minutos de inatividade',
    'days' => ':n dias',
    'provider' => 'Segundo o fornecedor',
    'necessary' => 'Necessário', 'statistics' => 'Estatística', 'marketing' => 'Marketing',
    'none' => 'Atualmente não é instalado nenhum cookie de terceiros.',
]])

<h2>3. Gerir o consentimento</h2>
<p>Pode alterar a sua escolha a qualquer momento com o botão «Definições de cookies» no rodapé. Também pode apagar os cookies nas definições do navegador.</p>
<p>Mais informação na {!! $legal->link('privacy-policy', 'política de privacidade') !!}.</p>
