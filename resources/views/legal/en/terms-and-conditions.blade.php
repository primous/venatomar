{{-- TEMPLATE: must be reviewed by a lawyer before publication. --}}
<h2>1. Who we are</h2>
<p>The website {!! $legal->brand() !!} ({!! $legal->url() !!}) is operated by:</p>
@include('legal.partials.identity', ['l' => ['company' => 'Company name', 'tax' => 'Company / VAT number', 'register' => 'Company register', 'address' => 'Registered address', 'email' => 'Contact email', 'credit' => 'Credit broker registration no.']])

<h2>2. What the service does</h2>
<p>{!! $legal->brand() !!} is an online personal credit and lending provider. It directly processes personal loan applications and grants loans to consumers in accordance with the terms agreed in the contract signed with the borrower.</p>

<h2>3. Free application review</h2>
<p>Application submission and credit evaluation are completely free. {!! $legal->brand() !!} will never ask you for an upfront management fee, study charge or pre-payment.</p>

<h2>4. Direct lending</h2>
<p>{!! $legal->brand() !!} assesses and directly grants financing upon successful verification of borrower documentation and execution of the loan contract.</p>

<h2>5. Loan terms</h2>
<p>The loan amount, term, interest rate, APR and all other terms are set directly in accordance with responsible lending criteria. Before you sign, {!! $legal->brand() !!} will provide you with the pre-contractual credit information (SECCI) and the agreement.</p>

<h2>6. Representative example</h2>
<x-legal-example />

<h2>7. Your obligations</h2>
<ul>
    <li>Be 18 or over and apply in your own name.</li>
    <li>Give accurate, complete and up-to-date information.</li>
    <li>Do not use another person's details or use the service for fraud.</li>
</ul>

<h2>8. Liability</h2>
<p>{!! $legal->brand() !!} assesses every application based on creditworthiness and responsible risk criteria. Loan approval is subject to application review and accuracy of borrower data. The website may be temporarily unavailable for maintenance.</p>

<h2>9. Right to withdraw</h2>
<p>You can withdraw from the credit agreement concluded with {!! $legal->brand() !!} within 14 calendar days, without giving a reason, in accordance with consumer credit regulations, repaying the principal plus accrued interest.</p>

<h2>10. Intellectual property</h2>
<p>The texts, designs, logos and code of the website belong to {!! $legal->company_name !!} or its licensors. They may not be reproduced without permission.</p>

<h2>11. Changes to these terms</h2>
<p>These terms may change. The version that applies is the one published when you send your application.</p>

<h2>12. Governing law</h2>
<p>These terms are governed by the law of England and Wales. The courts of {!! $legal->jurisdiction !!} have jurisdiction, without prejudice to your rights as a consumer to bring proceedings in the part of the UK where you live.</p>
