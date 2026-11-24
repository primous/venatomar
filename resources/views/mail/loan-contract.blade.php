<x-mail-layout :message="$message ?? null" :title="__('mail.contract.subject', ['reference' => $reference])" :preheader="__('mail.contract.preheader', ['brand' => config('venatomar.brand')])">
    <div style="margin-bottom:20px;">
        <span style="display:inline-block;padding:6px 14px;background:#E6F5F1;border:1px solid rgba(15,165,126,0.3);border-radius:20px;font-family:Manrope,Segoe UI,Helvetica,Arial,sans-serif;font-size:12px;font-weight:700;color:#0A5E4E;text-transform:uppercase;letter-spacing:0.5px;">
            ✓ {{ __('mail.contract.badge') }}
        </span>
    </div>

    <h1 style="margin:0 0 16px;font-family:Manrope,Segoe UI,Helvetica,Arial,sans-serif;font-size:22px;line-height:1.3;color:#0B1F3A;font-weight:700;">
        {{ __('mail.contract.greeting', ['name' => $name]) }}
    </h1>

    <p style="margin:0 0 18px;font-size:15px;line-height:1.6;color:#24324A;">
        {{ __('mail.contract.intro') }}
    </p>

    <!-- Contract details recap -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F5F7FA;border-radius:12px;font-size:15px;line-height:1.6;margin:20px 0 24px;">
        <tr>
            <td style="padding:14px 20px 6px;color:#5B6B82;width:45%;">- {{ __('mail.confirmation.reference') }} :</td>
            <td style="padding:14px 20px 6px;text-align:right;font-weight:700;color:#0B1F3A;font-family:monospace;">{{ $reference }}</td>
        </tr>
        <tr>
            <td style="padding:6px 20px;color:#5B6B82;">- {{ __('mail.confirmation.amount') }} :</td>
            <td style="padding:6px 20px;text-align:right;font-weight:700;color:#0B1F3A;">{{ $amount }}</td>
        </tr>
        @if(!empty($monthlyPayment))
        <tr>
            <td style="padding:6px 20px;color:#5B6B82;">- {{ __('mail.contract.monthly_payment') }} :</td>
            <td style="padding:6px 20px;text-align:right;font-weight:700;color:#0B1F3A;">{{ $monthlyPayment }}</td>
        </tr>
        @endif
        <tr>
            <td style="padding:6px 20px;color:#5B6B82;">- {{ __('mail.confirmation.term') }} :</td>
            <td style="padding:6px 20px;text-align:right;font-weight:700;color:#0B1F3A;">{{ trans_choice('form.months', $term, ['count' => $term]) }}</td>
        </tr>
        @if(!empty($interestRate))
        <tr>
            <td style="padding:6px 20px;color:#5B6B82;">- {{ __('mail.contract.interest_rate') }} :</td>
            <td style="padding:6px 20px;text-align:right;font-weight:700;color:#0B1F3A;">{{ number_format((float) $interestRate, 2, ',', ' ') }} %</td>
        </tr>
        @endif
        @if(!empty($documentFee))
        <tr>
            <td style="padding:6px 20px;color:#5B6B82;">- {{ __('mail.contract.document_fee') }} :</td>
            <td style="padding:6px 20px;text-align:right;font-weight:700;color:#0B1F3A;">{{ $documentFee }}</td>
        </tr>
        @endif
        <tr>
            <td style="padding:6px 20px 14px;color:#5B6B82;">- {{ __('mail.contract.attached_document') }} :</td>
            <td style="padding:6px 20px 14px;text-align:right;font-weight:700;color:#0A5E4E;font-family:monospace;">{{ $attachmentName ?? __('mail.contract.attachment_filename', ['reference' => $reference]) }}</td>
        </tr>
    </table>

    <h2 style="margin:26px 0 12px;font-family:Manrope,Segoe UI,Helvetica,Arial,sans-serif;font-size:16px;font-weight:700;color:#0B1F3A;">
        {{ __('mail.contract.instructions_title') }}
    </h2>
    <ol style="margin:0 0 24px;padding-left:20px;font-size:15px;line-height:1.6;color:#24324A;">
        <li style="margin-bottom:8px;">{{ __('mail.contract.step1') }}</li>
        <li>{{ __('mail.contract.step2') }}</li>
    </ol>

    <div style="margin:26px 0 24px;padding:16px 20px;background:#F5F7FA;border-left:4px solid #0E7C66;border-radius:4px;font-size:14px;line-height:1.6;color:#24324A;">
        {!! __('mail.confirmation.questions', ['email' => '<a href="mailto:'.$contactEmail.'" style="color:#0A5E4E;font-weight:700;text-decoration:underline;">'.$contactEmail.'</a>']) !!}
    </div>

    <div style="margin:24px 0 0;font-size:15px;line-height:1.6;color:#24324A;">
        <p style="margin:0;">{{ __('mail.contract.closing') }}</p>
        <p style="margin:4px 0 0;font-weight:700;color:#0B1F3A;">{{ __('mail.contract.service') }}</p>
    </div>

    <x-slot:footer>
        <p style="margin:0;">{{ __('mail.footer.why', ['brand' => config('venatomar.brand')]) }} <a href="{{ $privacyUrl }}">{{ __('footer.privacy') }}</a></p>
    </x-slot:footer>
</x-mail-layout>
