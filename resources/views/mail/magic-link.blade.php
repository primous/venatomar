<x-mail-layout :message="$message ?? null" :title="__('mail.magic.subject', ['brand' => config('venatomar.brand')])">
    <h1 style="margin:0 0 12px;font-family:Manrope,Segoe UI,Helvetica,Arial,sans-serif;font-size:24px;line-height:1.2;color:#0B1F3A;">{{ __('mail.magic.title') }}</h1>
    <p style="margin:0 0 24px;font-size:16px;line-height:1.6;">{{ __('mail.magic.intro', ['minutes' => $minutes]) }}</p>
    <table role="presentation" cellpadding="0" cellspacing="0"><tr><td style="border-radius:12px;background:#0E7C66;">
        <a href="{{ $url }}" style="display:inline-block;padding:14px 24px;font-weight:600;color:#FFFFFF;text-decoration:none;">{{ __('mail.magic.button') }}</a>
    </td></tr></table>
    <p style="margin:24px 0 0;font-size:13px;line-height:1.5;color:#5B6B82;">{{ __('mail.magic.ignore') }}</p>
    <x-slot:footer>
        <p style="margin:0;"><a href="{{ $privacyUrl }}">{{ __('footer.privacy') }}</a></p>
    </x-slot:footer>
</x-mail-layout>
