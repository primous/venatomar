{{-- MODELO: deve ser validado por um jurista antes da publicação. --}}
<h2>1. Identificação do titular</h2>
<p>O site {!! $legal->brand() !!} ({!! $legal->url() !!}) é explorado por:</p>
@include('legal.partials.identity', ['l' => ['company' => 'Denominação social', 'tax' => 'NIPC', 'register' => 'Registo comercial', 'address' => 'Sede', 'email' => 'E-mail de contacto', 'credit' => 'N.º de registo de intermediário de crédito']])

<h2>2. Objeto do serviço</h2>
<p>O {!! $legal->brand() !!} é uma entidade de concessão de crédito pessoal online. Gere diretamente os pedidos de empréstimo e concede financiamento a particulares de acordo com os termos estabelecidos no contrato celebrado com o utilizador.</p>

<h2>3. Gratuitidade da análise</h2>
<p>A submissão do pedido e a avaliação de solvabilidade são totalmente gratuitas. O {!! $legal->brand() !!} nunca pede ao utilizador pagamentos adiantados, taxas de abertura prévias ou comissões de dossier.</p>

<h2>4. Concessão direta de crédito</h2>
<p>O {!! $legal->brand() !!} avalia e concede diretamente o financiamento após verificação creditícia positiva e formalização do contrato com o mutuário.</p>

<h2>5. Condições do crédito</h2>
<p>O montante, o prazo, a taxa de juro, a TAEG e as restantes condições contratuais são detalhados na Ficha de Informação Normalizada Europeia (FIN) e no contrato de crédito entregues ao utilizador antes da assinatura.</p>

<h2>6. Exemplo representativo</h2>
<x-legal-example />

<h2>7. Obrigações do utilizador</h2>
<ul>
    <li>Ser maior de idade e agir em nome próprio.</li>
    <li>Fornecer dados exatos, completos e atualizados.</li>
    <li>Não utilizar dados de terceiros nem o serviço para fins fraudulentos.</li>
</ul>

<h2>8. Responsabilidade</h2>
<p>O {!! $legal->brand() !!} analisa cada pedido com base em critérios de crédito responsável e solvabilidade. A concessão do empréstimo fica sujeita à aprovação e à veracidade dos dados comunicados. O site pode estar temporariamente indisponível por manutenção.</p>

<h2>9. Direito de livre revogação</h2>
<p>O utilizador tem o direito de revogar o contrato de crédito celebrado com o {!! $legal->brand() !!} no prazo de 14 dias de calendário, sem necessidade de justificação, restituindo o capital financiado e os juros devidos.</p>

<h2>10. Propriedade intelectual</h2>
<p>Os textos, o design, os logótipos e o código do site pertencem a {!! $legal->company_name !!} ou aos seus licenciadores. É proibida a sua reprodução sem autorização.</p>

<h2>11. Alterações</h2>
<p>Estas condições podem ser alteradas. Aplica-se a versão publicada no momento do envio do pedido.</p>

<h2>12. Lei aplicável e foro</h2>
<p>Aplica-se a lei portuguesa. É competente o foro de {!! $legal->jurisdiction !!}, sem prejuízo das regras imperativas de proteção do consumidor. Em caso de litígio, pode recorrer a uma entidade de resolução alternativa de litígios de consumo.</p>
