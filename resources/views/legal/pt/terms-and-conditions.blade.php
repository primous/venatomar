{{-- MODELO: deve ser validado por um jurista antes da publicação. --}}
<h2>1. Identificação do titular</h2>
<p>O site {!! $legal->brand() !!} ({!! $legal->url() !!}) é explorado por:</p>
@include('legal.partials.identity', ['l' => ['company' => 'Denominação social', 'tax' => 'NIPC', 'register' => 'Registo comercial', 'address' => 'Sede', 'email' => 'E-mail de contacto', 'credit' => 'N.º de registo de intermediário de crédito']])

<h2>2. Objeto do serviço</h2>
<p>O {!! $legal->brand() !!} é um serviço gratuito de comparação e intermediação de crédito pessoal. Recolhe o pedido do utilizador e transmite-o a entidades parceiras. <strong>O {!! $legal->brand() !!} não concede crédito</strong>, não decide sobre a aprovação e não é parte do contrato de crédito.</p>

<h2>3. Gratuitidade para o utilizador</h2>
<p>A utilização do comparador é gratuita. O {!! $legal->brand() !!} nunca pede ao utilizador pagamentos, adiantamentos ou comissões de análise.</p>

<h2>4. Remuneração</h2>
<p>O {!! $legal->brand() !!} pode receber uma remuneração das entidades parceiras quando lhes é transmitido um pedido ou celebrado um contrato. Essa remuneração não aumenta o custo do crédito para o utilizador. Entidades parceiras: {!! $legal->partners !!}.</p>

<h2>5. Condições do crédito</h2>
<p>O montante, o prazo, a taxa de juro, a TAEG e as demais condições são fixados exclusivamente por cada entidade após a análise do pedido. Antes da assinatura, a entidade deve entregar a Ficha de Informação Normalizada Europeia (FIN) e o contrato.</p>

<h2>6. Exemplo representativo</h2>
<x-legal-example />

<h2>7. Obrigações do utilizador</h2>
<ul>
    <li>Ser maior de idade e agir em nome próprio.</li>
    <li>Fornecer dados exatos, completos e atualizados.</li>
    <li>Não utilizar dados de terceiros nem o serviço para fins fraudulentos.</li>
</ul>

<h2>8. Responsabilidade</h2>
<p>O {!! $legal->brand() !!} não garante a aceitação do pedido nem as condições propostas. Não responde pelas decisões, propostas ou contratos das entidades, nem por danos decorrentes de dados inexatos fornecidos pelo utilizador. O site pode estar temporariamente indisponível por manutenção.</p>

<h2>9. Direito de livre revogação</h2>
<p>O utilizador pode revogar o contrato de crédito celebrado com a entidade no prazo de 14 dias de calendário, sem indicar motivo, nos termos do regime do crédito aos consumidores. Deve comunicá-lo à entidade e restituir o capital e os juros vencidos.</p>

<h2>10. Propriedade intelectual</h2>
<p>Os textos, o design, os logótipos e o código do site pertencem a {!! $legal->company_name !!} ou aos seus licenciadores. É proibida a sua reprodução sem autorização.</p>

<h2>11. Alterações</h2>
<p>Estas condições podem ser alteradas. Aplica-se a versão publicada no momento do envio do pedido.</p>

<h2>12. Lei aplicável e foro</h2>
<p>Aplica-se a lei portuguesa. É competente o foro de {!! $legal->jurisdiction !!}, sem prejuízo das regras imperativas de proteção do consumidor. Em caso de litígio, pode recorrer a uma entidade de resolução alternativa de litígios de consumo.</p>
