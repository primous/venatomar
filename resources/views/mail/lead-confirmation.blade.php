@php use App\Support\Venatomar as PF; @endphp
{{-- Applicant confirmation: NO IBAN, NO identity number, NO income. --}}
<x-mail-layout :message="$message ?? null" :title="__('mail.confirmation.subject', ['reference' => $reference])" :preheader="__('mail.confirmation.preheader')">
    <h1 style="margin:0 0 12px;font-family:Manrope,Segoe UI,Helvetica,Arial,sans-serif;font-size:24px;line-height:1.2;color:#0B1F3A;">{{ __('mail.confirmation.title', ['name' => $firstName]) }}</h1>
    <p style="margin:0 0 20px;font-size:16px;line-height:1.6;">{{ __('mail.confirmation.intro') }}</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F5F7FA;border-radius:12px;font-size:15px;">
        <tr><td style="padding:16px 20px 4px;color:#5B6B82;">{{ __('mail.confirmation.reference') }}</td><td style="padding:16px 20px 4px;text-align:right;font-weight:700;color:#0B1F3A;">{{ $reference }}</td></tr>
        <tr><td style="padding:4px 20px;color:#5B6B82;">{{ __('form.amount') }}</td><td style="padding:4px 20px;text-align:right;font-weight:700;color:#0B1F3A;">{{ PF::money($amount, $country) }}</td></tr>
        <tr><td style="padding:4px 20px 16px;color:#5B6B82;">{{ __('form.term') }}</td><td style="padding:4px 20px 16px;text-align:right;font-weight:700;color:#0B1F3A;">{{ trans_choice('form.months', $term, ['count' => $term]) }}</td></tr>
    </table>

    <h2 style="margin:28px 0 8px;font-family:Manrope,Segoe UI,Helvetica,Arial,sans-serif;font-size:18px;color:#0B1F3A;">{{ __('thanks.next_title') }}</h2>
    <ol style="margin:0 0 24px;padding-left:20px;font-size:15px;line-height:1.6;">
        <li>{{ __('thanks.next1') }}</li>
        <li>{{ __('thanks.next2') }}</li>
        <li>{{ __('thanks.next3') }}</li>
    </ol>

    <table role="presentation" cellpadding="0" cellspacing="0"><tr><td style="border-radius:12px;background:#0E7C66;">
        <a href="{{ $accountUrl }}" style="display:inline-block;padding:14px 24px;font-weight:600;color:#FFFFFF;text-decoration:none;">{{ __('nav.account') }}</a>
    </td></tr></table>

    <p style="margin:24px 0 0;font-size:13px;line-height:1.5;color:#5B6B82;">{{ __('mail.confirmation.not_you') }}</p>

    <x-slot:footer>
        <p style="margin:0;">{{ __('mail.footer.why', ['brand' => config('venatomar.brand')]) }} <a href="{{ $privacyUrl }}">{{ __('footer.privacy') }}</a></p>
    </x-slot:footer>
</x-mail-layout>
