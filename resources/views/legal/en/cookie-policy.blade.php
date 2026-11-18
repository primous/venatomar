{{-- TEMPLATE: must be reviewed by a lawyer before publication. --}}
<h2>1. What are cookies?</h2>
<p>Cookies are small files the website stores in your browser. {!! $legal->brand() !!} only uses the cookies listed below. Statistics and marketing cookies are only set if you accept them.</p>

<h2>2. Cookies we use</h2>
@include('legal.partials.cookies-table', ['t' => [
    'h' => ['Cookie', 'Purpose', 'Duration', 'Owner', 'Category'],
    'own' => 'First party',
    'session' => 'Keeps your session and the form draft.',
    'xsrf' => 'Protects forms against cross-site request forgery (CSRF).',
    'consent' => 'Remembers your cookie choice and the version of this policy.',
    'utm' => 'Remembers the campaign your visit came from (only after consent).',
    'ga' => 'Google Analytics: aggregated audience measurement.',
    'ads' => 'Google Ads: conversion measurement.',
    'meta' => 'Meta Pixel: campaign measurement.',
    'minutes' => ':n minutes of inactivity',
    'days' => ':n days',
    'provider' => 'Set by the provider',
    'necessary' => 'Necessary', 'statistics' => 'Statistics', 'marketing' => 'Marketing',
    'none' => 'No third-party cookie is currently set.',
]])

<h2>3. Managing your consent</h2>
<p>You can change your choice at any time with the “Cookie settings” button in the footer. You can also delete cookies in your browser settings.</p>
<p>More information in our {!! $legal->link('privacy-policy', 'privacy policy') !!}.</p>
