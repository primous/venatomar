<?php

namespace App\Livewire;

use App\Http\Middleware\SetLocale;
use App\Mail\DeletionRequested;
use App\Models\Applicant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Throwable;

class AccountDashboard extends Component
{
    #[Locked]
    public string $locale = 'es';

    public bool $confirmingDeletion = false;

    public function mount(?string $locale = null): void
    {
        $this->locale = $locale ?? app()->getLocale();
    }

    public function hydrate(): void
    {
        SetLocale::apply($this->locale);
    }

    private function applicant(): Applicant
    {
        $applicant = Auth::guard('applicant')->user();
        abort_unless($applicant instanceof Applicant, 403);

        return $applicant;
    }

    public function requestDeletion(): void
    {
        $applicant = $this->applicant();

        if ($applicant->deletionRequests()->where('status', 'pending')->exists()) {
            $this->confirmingDeletion = false;

            return;
        }

        $deletion = $applicant->deletionRequests()->create([
            'email' => $applicant->email,
            'locale' => $this->locale,
            'status' => 'pending',
        ]);

        $adminRecipient = $applicant->leads()->latest()->value('admin_email') ?: config('venatomar.lead_recipient');

        try {
            Mail::to($adminRecipient)->queue(new DeletionRequested($deletion));

            try {
                app(\App\Services\WebPushService::class)->sendToAdmin(
                    (string) $adminRecipient,
                    'Demande de suppression de compte',
                    "L'utilisateur {$applicant->email} a demandé la suppression de ses données.",
                    ['url' => '/admin']
                );
            } catch (\Throwable $e) {}
        } catch (Throwable $e) {
            Log::channel('mail')->error('Could not queue deletion e-mail', ['deletion_request' => $deletion->id, 'error' => $e->getMessage()]);
        }

        $this->confirmingDeletion = false;
    }

    public function render()
    {
        $applicant = $this->applicant();

        return view('livewire.account-dashboard', [
            'applicant' => $applicant,
            'leads' => $applicant->leads()->get(),
            'pendingDeletion' => $applicant->deletionRequests()->where('status', 'pending')->latest()->first(),
        ]);
    }
}
