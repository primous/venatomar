{{-- Inline styles: the Filament theme does not compile the site's Tailwind classes. --}}
@php
    $currentEmail = auth()->user()?->email;
    $isContact = $currentEmail === 'contact@venatomar.com';
@endphp
<span style="display:inline-flex;align-items:center;gap:.5rem;white-space:nowrap;">
    <img src="{{ asset('images/logo.svg') }}" alt="" width="32" height="32" style="width:2rem;height:2rem;flex:none;">
    <span style="font-size:1.125rem;font-weight:800;letter-spacing:-.01em;">{{ config('venatomar.brand') }}</span>
    <span style="font-size:.7rem;font-weight:700;padding:.15rem .5rem;border-radius:.375rem;background:{{ $isContact ? 'rgba(14,165,233,.2);color:#0284c7' : 'rgba(15,165,126,.2);color:#0FA57E' }};">
        {{ $isContact ? 'Admin · contact@venatomar.com' : 'Admin · info@venatomar.com' }}
    </span>
</span>
