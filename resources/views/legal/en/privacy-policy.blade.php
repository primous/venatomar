{{-- TEMPLATE: must be reviewed by a lawyer before publication. --}}
<h2>1. Data controller</h2>
@include('legal.partials.identity', ['l' => ['company' => 'Controller', 'tax' => 'Company / VAT number', 'register' => 'Company register', 'address' => 'Address', 'email' => 'Privacy contact', 'credit' => 'Credit broker registration no.']])
<p>Data protection officer (if appointed): {!! $legal->dpo_email !!}.</p>

<h2>2. Data we process</h2>
<ul>
    <li><strong>Income:</strong> amount and term requested, net income, employment status, adverse credit (CCJs / defaults).</li>
    <li><strong>Personal details:</strong> name, National Insurance number (optional), date of birth, email and phone.</li>
    <li><strong>Address:</strong> postcode, county, town, street, house number and flat.</li>
    <li><strong>Bank account:</strong> IBAN or sort code and account number.</li>
    <li><strong>Additional information:</strong> sex, loan purpose, marital status, sector, education, housing, outgoings, debts and vehicle.</li>
    <li><strong>Technical data:</strong> IP address, browser, language, campaign of origin (UTM), date and version of consent.</li>
</ul>
<p>Identity number, bank details and date of birth are stored encrypted.</p>

<h2>3. Purposes and lawful bases</h2>
<ul>
    <li><strong>Handling your application</strong> and passing it to lenders: steps taken at your request before entering into a contract (Art. 6(1)(b) UK GDPR).</li>
    <li><strong>Marketing communications</strong>: only with your consent, which you can withdraw at any time (Art. 6(1)(a)).</li>
    <li><strong>Fraud prevention and security</strong> (submission limits, technical logs): legitimate interests (Art. 6(1)(f)).</li>
    <li><strong>Legal obligations</strong>: Art. 6(1)(c).</li>
</ul>

<h2>4. Recipients</h2>
<ul>
    <li>Partner lenders your application is sent to: {!! $legal->partners !!}.</li>
    <li>Hosting and email provider: {!! $legal->hosting_provider !!}, as processor.</li>
    <li>Public authorities, where required by law.</li>
</ul>
<p>We never sell your data. Form fields are never shared with analytics tools.</p>

<h2>5. International transfers</h2>
<p>Data is hosted in the European Economic Area, which benefits from UK adequacy regulations. If you accept statistics or marketing cookies, Google or Meta may process browsing data outside the UK with appropriate safeguards (adequacy regulations or the International Data Transfer Addendum).</p>

<h2>6. How long we keep data</h2>
<p>We keep application data for {!! $legal->retention !!}, then restricted for the statutory limitation periods. Technical security data is kept for the minimum time necessary.</p>

<h2>7. Your rights</h2>
<p>You have the right to access, rectify, erase, object to, restrict and port your data. Email <a href="mailto:{!! $legal->contact_email !!}">{!! $legal->contact_email !!}</a> or use {!! $legal->accountLink('My account') !!}, where you can request the deletion of your data.</p>
<p>You can complain to the {!! $legal->authority() !!}.</p>

<h2>8. Automated decisions</h2>
<p>{!! $legal->brand() !!} does not make decisions based solely on automated processing that have legal effects on you. The lending decision belongs to each lender, which will inform you about its own processing.</p>

<h2>9. Cookies</h2>
<p>See our {!! $legal->link('cookie-policy', 'cookie policy') !!}.</p>
