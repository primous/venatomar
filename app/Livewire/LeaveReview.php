<?php

namespace App\Livewire;

use App\Http\Middleware\SetLocale;
use App\Models\Applicant;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Public "leave a review" form (home page and "Mi cuenta").
 * Every review is saved UNPUBLISHED and only appears once approved in /admin.
 * The "Verificado" badge is set only when the author is a logged-in applicant.
 */
class LeaveReview extends Component
{
    #[Locked]
    public string $locale = 'es';

    #[Locked]
    public int $startedAt = 0;

    public int $rating = 0;
    public string $author_name = '';
    public string $title = '';
    public string $body = '';
    public bool $consent = false;
    public string $website = ''; // honeypot
    public bool $sent = false;

    public function mount(?string $locale = null): void
    {
        $this->locale = $locale ?? app()->getLocale();
        $this->startedAt = now()->getTimestamp();
    }

    public function hydrate(): void
    {
        SetLocale::apply($this->locale);
    }

    private function applicant(): ?Applicant
    {
        $user = Auth::guard('applicant')->user();

        return $user instanceof Applicant ? $user : null;
    }

    protected function validationAttributes(): array
    {
        return [
            'rating' => __('reviews.form.rating'),
            'author_name' => __('reviews.form.name'),
            'title' => __('reviews.form.review_title'),
            'body' => __('reviews.form.body'),
            'consent' => __('reviews.form.consent_attr'),
        ];
    }

    protected function messages(): array
    {
        return [
            'body.not_regex' => __('reviews.form.no_links'),
            'title.not_regex' => __('reviews.form.no_links'),
            'rating.min' => __('reviews.form.pick_rating'),
        ];
    }

    public function setRating(int $value): void
    {
        $this->rating = max(1, min(5, $value));
        $this->resetErrorBag('rating');
    }

    public function submit(): void
    {
        if ($this->website !== '' || (now()->getTimestamp() - $this->startedAt) < (int) config('venatomar.antispam.min_seconds')) {
            $this->addError('form', __('form.error_generic'));

            return;
        }

        $noLinks = 'not_regex:/(https?:\/\/|www\.|\.(com|net|org|es|info)\b)/i';
        $data = $this->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'author_name' => ['required', 'string', 'min:2', 'max:60'],
            'title' => ['nullable', 'string', 'max:120', $noLinks],
            'body' => ['required', 'string', 'min:20', 'max:1500', $noLinks],
            'consent' => ['accepted'],
        ]);

        $key = 'review:ip:'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, (int) config('venatomar.antispam.reviews_per_ip_per_day', 3))) {
            $this->addError('form', __('form.error_rate_limited'));

            return;
        }
        RateLimiter::hit($key, 86400);

        $applicant = $this->applicant();

        Review::create([
            'applicant_id' => $applicant?->id,
            'locale' => $this->locale,
            'author_name' => trim(strip_tags($data['author_name'])),
            'rating' => $data['rating'],
            'title' => $data['title'] !== '' ? trim(strip_tags($data['title'])) : null,
            'body' => trim(strip_tags($data['body'])),
            'reviewed_at' => today(),
            'source' => $applicant ? 'account' : 'site',
            'is_verified' => $applicant !== null && $applicant->leads()->exists(),
            'is_published' => false,
        ]);

        $this->reset('rating', 'author_name', 'title', 'body', 'consent');
        $this->sent = true;
    }

    public function again(): void
    {
        $this->sent = false;
        $this->startedAt = now()->getTimestamp();
    }

    public function render()
    {
        return view('livewire.leave-review', [
            'verified' => ($a = $this->applicant()) !== null && $a->leads()->exists(),
        ]);
    }
}
