@php use App\Support\Venatomar as PF; @endphp
{{-- Applicant confirmation: NO IBAN, NO identity number, NO income. --}}
<x-mail-layout :message="$message ?? null" :title="__('mail.confirmation.subject', ['reference' => $reference])" :preheader="__('mail.confirmation.preheader', ['brand' => config('venatomar.brand')])">
    <h1 style="margin:0 0 16px;font-family:Manrope,Segoe UI,Helvetica,Arial,sans-serif;font-size:22px;line-height:1.3;color:#0B1F3A;font-weight:700;">
        {{ __('mail.confirmation.title', ['name' => $firstName]) }}
    </h1>

    <p style="margin:0 0 20px;font-size:15px;line-height:1.6;color:#24324A;">
        {{ __('mail.confirmation.intro', ['brand' => config('venatomar.brand')]) }}
    </p>

    <h2 style="margin:24px 0 12px;font-family:Manrope,Segoe UI,Helvetica,Arial,sans-serif;font-size:16px;font-weight:700;color:#0B1F3A;">
        {{ __('mail.confirmation.summary_title') }}
    </h2>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F5F7FA;border-radius:12px;font-size:15px;line-height:1.6;margin-bottom:24px;">
        <tr>
            <td style="padding:14px 20px 6px;color:#5B6B82;width:45%;">- {{ __('mail.confirmation.reference') }} :</td>
            <td style="padding:14px 20px 6px;text-align:right;font-weight:700;color:#0B1F3A;font-family:monospace;">{{ $reference }}</td>
        </tr>
        <tr>
            <td style="padding:6px 20px;color:#5B6B82;">- {{ __('mail.confirmation.amount') }} :</td>
            <td style="padding:6px 20px;text-align:right;font-weight:700;color:#0B1F3A;">{{ PF::money($amount, $country) }}</td>
        </tr>
        <tr>
            <td style="padding:6px 20px;color:#5B6B82;">- {{ __('mail.confirmation.term') }} :</td>
            <td style="padding:6px 20px;text-align:right;font-weight:700;color:#0B1F3A;">{{ trans_choice('form.months', $term, ['count' => $term]) }}</td>
        </tr>
        <tr>
            <td style="padding:6px 20px 14px;color:#5B6B82;">- {{ __('mail.confirmation.date') }} :</td>
            <td style="padding:6px 20px 14px;text-align:right;font-weight:700;color:#0B1F3A;">{{ $date }}</td>
        </tr>
    </table>

    <h2 style="margin:28px 0 12px;font-family:Manrope,Segoe UI,Helvetica,Arial,sans-serif;font-size:16px;font-weight:700;color:#0B1F3A;">
        {{ __('mail.confirmation.next_title') }}
    </h2>
    <ol style="margin:0 0 24px;padding-left:20px;font-size:15px;line-height:1.6;color:#24324A;">
        <li style="margin-bottom:8px;">{{ __('mail.confirmation.next1') }}</li>
        <li>{{ __('mail.confirmation.next2') }}</li>
    </ol>

    <div style="margin:28px 0 24px;padding:16px 20px;background:#F5F7FA;border-left:4px solid #0E7C66;border-radius:4px;font-size:14px;line-height:1.6;color:#24324A;">
        {!! __('mail.confirmation.questions', ['email' => '<a href="mailto:'.$contactEmail.'" style="color:#0A5E4E;font-weight:700;text-decoration:underline;">'.$contactEmail.'</a>']) !!}
    </div>

    <div style="margin:24px 0 0;font-size:15px;line-height:1.6;color:#24324A;">
        <p style="margin:0;">{{ __('mail.confirmation.closing') }}</p>
        <p style="margin:4px 0 0;font-weight:700;color:#0B1F3A;">{{ __('mail.confirmation.team', ['brand' => config('venatomar.brand')]) }}</p>
    </div>

    <x-slot:footer>
        <p style="margin:0;">{{ __('mail.footer.why', ['brand' => config('venatomar.brand')]) }} <a href="{{ $privacyUrl }}">{{ __('footer.privacy') }}</a></p>
    </x-slot:footer>
</x-mail-layout>
