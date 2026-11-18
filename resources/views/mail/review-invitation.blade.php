{{-- Review invitation: sent once, no personal data besides the first name and reference. --}}
<x-mail-layout :message="$message ?? null" :title="__('mail.review.subject', ['brand' => config('venatomar.brand')])" :preheader="__('mail.review.preheader')">
    <h1 style="margin:0 0 12px;font-family:Manrope,Segoe UI,Helvetica,Arial,sans-serif;font-size:24px;line-height:1.2;color:#0B1F3A;">{{ __('mail.review.title', ['name' => $firstName]) }}</h1>
    <p style="margin:0 0 20px;font-size:16px;line-height:1.6;">{{ __('mail.review.intro', ['reference' => $reference, 'brand' => config('venatomar.brand')]) }}</p>

    <table role="presentation" cellpadding="0" cellspacing="0"><tr><td style="border-radius:12px;background:#0E7C66;">
        <a href="{{ $reviewUrl }}" style="display:inline-block;padding:14px 24px;font-weight:600;color:#FFFFFF;text-decoration:none;">{{ __('mail.review.button') }}</a>
    </td></tr></table>

    <p style="margin:24px 0 0;font-size:13px;line-height:1.5;color:#5B6B82;">{{ __('mail.review.once') }}</p>

    <x-slot:footer>
        <p style="margin:0;">{{ __('mail.footer.why', ['brand' => config('venatomar.brand')]) }} <a href="{{ $privacyUrl }}">{{ __('footer.privacy') }}</a></p>
    </x-slot:footer>
</x-mail-layout>
