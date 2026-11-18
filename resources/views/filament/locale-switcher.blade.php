@php use App\Support\AdminText; $current = app()->getLocale(); @endphp
{{-- Inline styles: the Filament theme does not compile the site's Tailwind classes. --}}
<nav aria-label="Langue / Idioma / Language" style="display:flex;gap:.25rem;align-items:center;{{ ($center ?? false) ? 'justify-content:center;margin-top:1rem;' : 'margin-inline-end:.5rem;' }}">
    @foreach (AdminText::LOCALES as $code => $label)
        <a href="{{ route('admin.locale', $code) }}" title="{{ $label }}" hreflang="{{ $code }}"
           @if ($code === $current) aria-current="true" @endif
           style="font-size:.75rem;font-weight:{{ $code === $current ? '700' : '500' }};padding:.25rem .5rem;border-radius:.375rem;text-decoration:none;{{ $code === $current ? 'background:#0FA57E;color:#fff;' : 'color:inherit;opacity:.75;' }}">{{ strtoupper($code) }}</a>
    @endforeach
</nav>
