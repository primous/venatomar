{{-- TEMPLATE: must be reviewed by a lawyer before publication. --}}
<h2>1. Who we are</h2>
<p>The website {!! $legal->brand() !!} ({!! $legal->url() !!}) is operated by:</p>
@include('legal.partials.identity', ['l' => ['company' => 'Company name', 'tax' => 'Company / VAT number', 'register' => 'Company register', 'address' => 'Registered address', 'email' => 'Contact email', 'credit' => 'Credit broker registration no.']])

<h2>2. What the service does</h2>
<p>{!! $legal->brand() !!} is a free personal loan comparison and broking service. It collects your application and passes it to partner lenders. <strong>{!! $legal->brand() !!} is not a lender</strong>: it does not decide whether a loan is approved and is not a party to the loan agreement.</p>

<h2>3. Free for you</h2>
<p>Using the comparison service is free. {!! $legal->brand() !!} will never ask you for a fee, an upfront payment or an arrangement charge.</p>

<h2>4. How we are paid</h2>
<p>{!! $legal->brand() !!} may receive a commission from partner lenders when an application is passed to them or a loan is taken out. This commission does not increase the cost of the loan for you. Partner lenders: {!! $legal->partners !!}.</p>

<h2>5. Loan terms</h2>
<p>The amount, term, interest rate, APR and all other terms are set solely by each lender after assessing your application. Before you sign, the lender must give you the pre-contract credit information and the agreement.</p>

<h2>6. Representative example</h2>
<x-legal-example />

<h2>7. Your obligations</h2>
<ul>
    <li>Be 18 or over and apply in your own name.</li>
    <li>Give accurate, complete and up-to-date information.</li>
    <li>Do not use another person's details or use the service for fraud.</li>
</ul>

<h2>8. Liability</h2>
<p>{!! $legal->brand() !!} does not guarantee that a lender will accept your application or the terms it may offer. We are not responsible for lenders' decisions, offers or agreements, nor for loss caused by inaccurate information you provide. The website may be temporarily unavailable for maintenance.</p>

<h2>9. Right to withdraw</h2>
<p>You can withdraw from a regulated credit agreement with the lender within 14 days, without giving a reason. You must tell the lender and repay the capital plus interest accrued.</p>

<h2>10. Intellectual property</h2>
<p>The texts, designs, logos and code of the website belong to {!! $legal->company_name !!} or its licensors. They may not be reproduced without permission.</p>

<h2>11. Changes to these terms</h2>
<p>These terms may change. The version that applies is the one published when you send your application.</p>

<h2>12. Governing law</h2>
<p>These terms are governed by the law of England and Wales. The courts of {!! $legal->jurisdiction !!} have jurisdiction, without prejudice to your rights as a consumer to bring proceedings in the part of the UK where you live.</p>
