{{-- MODELO: deve ser validado por um jurista antes da publicação. --}}
<h2>1. Responsável pelo tratamento</h2>
@include('legal.partials.identity', ['l' => ['company' => 'Responsável', 'tax' => 'NIPC', 'register' => 'Registo comercial', 'address' => 'Morada', 'email' => 'Contacto de privacidade', 'credit' => 'N.º de registo de intermediário de crédito']])
<p>Encarregado da proteção de dados (se designado): {!! $legal->dpo_email !!}.</p>

<h2>2. Dados tratados</h2>
<ul>
    <li><strong>Rendimentos:</strong> montante e prazo pedidos, rendimento líquido, situação profissional, incidentes na Central de Responsabilidades de Crédito.</li>
    <li><strong>Dados pessoais:</strong> nome, apelidos, NIF, data de nascimento, e-mail e telemóvel.</li>
    <li><strong>Morada:</strong> código postal, distrito, localidade, rua, número e andar.</li>
    <li><strong>Conta bancária:</strong> IBAN.</li>
    <li><strong>Informação adicional:</strong> sexo, finalidade, estado civil, setor, habilitações, habitação, despesas, dívidas e veículo.</li>
    <li><strong>Dados técnicos:</strong> endereço IP, navegador, idioma, campanha de origem (UTM), data e versão do consentimento.</li>
</ul>
<p>O NIF, o IBAN e a data de nascimento são guardados cifrados.</p>

<h2>3. Finalidades e fundamentos</h2>
<ul>
    <li><strong>Tratar o seu pedido</strong> e transmiti-lo às entidades: diligências pré-contratuais a seu pedido (art. 6.º, n.º 1, al. b) RGPD).</li>
    <li><strong>Comunicações comerciais</strong>: apenas com o seu consentimento, que pode retirar a qualquer momento (al. a)).</li>
    <li><strong>Prevenção da fraude e segurança</strong> (limites de envio, registos técnicos): interesse legítimo (al. f)).</li>
    <li><strong>Obrigações legais</strong>: al. c).</li>
</ul>

<h2>4. Destinatários</h2>
<ul>
    <li>Entidades parceiras a quem o pedido é transmitido: {!! $legal->partners !!}.</li>
    <li>Fornecedor de alojamento e e-mail: {!! $legal->hosting_provider !!}, como subcontratante.</li>
    <li>Autoridades públicas, quando a lei o exija.</li>
</ul>
<p>Nunca vendemos os seus dados. Os campos do formulário não são partilhados com ferramentas de análise.</p>

<h2>5. Transferências internacionais</h2>
<p>Os dados são alojados no Espaço Económico Europeu. Se aceitar cookies de estatística ou de marketing, a Google ou a Meta podem tratar dados de navegação fora do EEE com as garantias previstas no RGPD (decisão de adequação ou cláusulas contratuais-tipo).</p>

<h2>6. Prazo de conservação</h2>
<p>Conservamos os dados do pedido durante {!! $legal->retention !!} e, depois, bloqueados durante os prazos legais de prescrição. Os dados técnicos de segurança são conservados pelo tempo mínimo necessário.</p>

<h2>7. Os seus direitos</h2>
<p>Pode exercer os direitos de acesso, retificação, apagamento, oposição, limitação e portabilidade escrevendo para <a href="mailto:{!! $legal->contact_email !!}">{!! $legal->contact_email !!}</a> ou em {!! $legal->accountLink('A minha conta') !!}, onde pode pedir a eliminação dos seus dados.</p>
<p>Pode apresentar reclamação à {!! $legal->authority() !!}.</p>

<h2>8. Decisões automatizadas</h2>
<p>O {!! $legal->brand() !!} não toma decisões baseadas exclusivamente em tratamento automatizado que produzam efeitos jurídicos sobre si. A decisão de crédito cabe a cada entidade, que o informará sobre os seus próprios tratamentos.</p>

<h2>9. Cookies</h2>
<p>Consulte a {!! $legal->link('cookie-policy', 'política de cookies') !!}.</p>
