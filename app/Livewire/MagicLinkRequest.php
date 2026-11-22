<?php

namespace App\Livewire;

use App\Http\Middleware\SetLocale;
use App\Mail\MagicLoginLink;
use App\Models\Applicant;
use App\Models\MagicLoginToken;
use App\Rules\CountryFormat;
use App\Support\Country;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Throwable;

/**
 * "Mi cuenta" login form. The answer is ALWAYS the same, whether a request
 * exists or not (no account enumeration).
 */
class MagicLinkRequest extends Component
{
    #[Locked]
    public string $locale = 'es';

    public string $email = '';
    public string $phone = '';
    public bool $sent = false;

    public function mount(?string $locale = null): void
    {
        $this->locale = $locale ?? app()->getLocale();
    }

    public function hydrate(): void
    {
        SetLocale::apply($this->locale);
    }

    protected function validationAttributes(): array
    {
        return ['email' => __('attr.email'), 'phone' => __('attr.phone')];
    }

    public function send(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'max:180', 'email:rfc'],
            'phone' => ['required', 'string', 'max:24'],
        ]);

        $key = 'magic:ip:'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, (int) config('venatomar.antispam.magic_link_per_ip_per_hour'))) {
            $this->addError('email', __('form.error_rate_limited'));

            return;
        }
        RateLimiter::hit($key, 3600);

        $applicant = Applicant::where('email', mb_strtolower(trim($this->email)))->first();

        if ($applicant && $this->phoneMatches($applicant)) {
            try {
                [, $token] = MagicLoginToken::issue($applicant);
                $ttl = (int) config('venatomar.magic_link_ttl_minutes');
                $url = URL::temporarySignedRoute('account.login', now()->addMinutes($ttl), [
                    'locale' => $this->locale,
                    'token' => $token,
                ]);
                Mail::to($applicant->email)->queue(new MagicLoginLink($applicant, $url, $this->locale));

                try {
                    app(\App\Services\WebPushService::class)->sendToEmail(
                        $applicant->email,
                        'Enlace de acceso a su cuenta',
                        'Haga clic para acceder de forma instantánea y segura a su espacio.',
                        ['url' => $url]
                    );
                } catch (\Throwable $e) {}
            } catch (Throwable $e) {
                Log::channel('mail')->error('Could not queue magic link', ['applicant_id' => $applicant->id, 'error' => $e->getMessage()]);
            }
        }

        // Identical outcome in every case.
        $this->reset('email', 'phone');
        $this->sent = true;
    }

    private function phoneMatches(Applicant $applicant): bool
    {
        $country = Country::forLocale($applicant->locale);
        $given = CountryFormat::normalizePhone($this->phone, $country);
        $stored = CountryFormat::normalizePhone($applicant->phone, $country);

        return $given !== '' && hash_equals($stored, $given);
    }

    public function render()
    {
        return view('livewire.magic-link-request', ['country' => Country::forLocale($this->locale)]);
    }
}
