<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Portal\PortalAuthController;
use App\Http\Controllers\Portal\PortalDashboardController;
use App\Http\Controllers\Portal\PortalDocumentController;
use App\Http\Controllers\Portal\PortalLoanController;
use App\Http\Controllers\Portal\PortalProfileController;
use App\Http\Controllers\Portal\PortalSupportController;
use App\Http\Controllers\Portal\PortalTransferController;
use App\Http\Controllers\SitemapController;
use App\Http\Middleware\CaptureUtm;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/'.config('venatomar.default_locale'), 302)->name('root');

// Back-office language (French by default, see SetAdminLocale).
Route::get('/admin/lang/{lang}', function (string $lang) {
    session([\App\Http\Middleware\SetAdminLocale::SESSION_KEY => $lang]);

    return redirect()->back(fallback: '/admin');
})->whereIn('lang', array_keys(\App\Support\AdminText::LOCALES))->name('admin.locale');

Route::get('/sitemap.xml', [SitemapController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

if (app()->environment('local')) {
    Route::get('/_styleguide', [PageController::class, 'styleguide'])->name('styleguide');
}

Route::prefix('portal')->group(function () {
    Route::get('/login', [PortalAuthController::class, 'showLogin'])->name('portal.login');
    Route::post('/login', [PortalAuthController::class, 'login'])->name('portal.login.submit');
    Route::post('/logout', [PortalAuthController::class, 'logout'])->name('portal.logout');
    Route::get('/', function () {
        $locale = Auth::guard('client')->user()?->locale ?? config('venatomar.default_locale', 'es');
        return redirect()->route('portal.dashboard', ['locale' => $locale]);
    });
});

Route::prefix('{locale}')
    ->where(['locale' => '[a-z]{2}'])
    ->middleware([SetLocale::class, CaptureUtm::class])
    ->group(function () {
        Route::get('/', [PageController::class, 'home'])->name('home');

        Route::get('/apply-loan', [PageController::class, 'apply'])->name('apply');
        Route::get('/apply-loan/gracias', [PageController::class, 'thanks'])->name('apply.thanks');

        // Spanish duplicate routes (associated with contact@venatomar.com)
        Route::get('/solicitar-prestamo', [PageController::class, 'solicitar'])->name('solicitar');
        Route::get('/solicitar', [PageController::class, 'solicitar'])->name('solicitar.short');
        Route::get('/solicitar-prestamo/gracias', [PageController::class, 'solicitarThanks'])->name('solicitar.thanks');
        Route::get('/solicitar/gracias', [PageController::class, 'solicitarThanks'])->name('solicitar.short.thanks');

        Route::get('/my-account', [AccountController::class, 'show'])->name('account');
        Route::post('/my-account', [PortalAuthController::class, 'login'])->name('account.client.login');
        Route::get('/my-account/login/{token}', [AccountController::class, 'login'])
            ->middleware('signed')->where('token', '[A-Za-z0-9]{64}')->name('account.login');
        Route::get('/my-account/dashboard', [AccountController::class, 'dashboard'])
            ->middleware('auth:applicant')->name('account.dashboard');
        Route::post('/my-account/logout', [AccountController::class, 'logout'])->name('account.logout');

        // Spanish duplicate customer account routes
        Route::get('/mi-cuenta', [AccountController::class, 'show'])->name('solicitar.account');
        Route::post('/mi-cuenta', [PortalAuthController::class, 'login'])->name('solicitar.account.client.login');
        Route::get('/mi-cuenta/login/{token}', [AccountController::class, 'login'])
            ->middleware('signed')->where('token', '[A-Za-z0-9]{64}')->name('solicitar.account.login');
        Route::get('/mi-cuenta/dashboard', [AccountController::class, 'dashboard'])
            ->middleware('auth:applicant')->name('solicitar.account.dashboard');
        Route::post('/mi-cuenta/logout', [AccountController::class, 'logout'])->name('solicitar.account.logout');
        Route::get('/banca-online', function (?string $locale = null) {
            return redirect()->route('portal.dashboard', ['locale' => $locale ?: app()->getLocale()]);
        })->name('solicitar.portal');

        // Banking Portal within the selected language (enforcing admin-selected client locale)
        Route::prefix('portal')->middleware(['auth:client', \App\Http\Middleware\EnsureClientPortalLocale::class])->group(function () {
            Route::get('/', [PortalDashboardController::class, 'index'])->name('portal.dashboard');

            Route::get('/transfers', [PortalTransferController::class, 'index'])->name('portal.transfers');
            Route::post('/transfers', [PortalTransferController::class, 'store'])->name('portal.transfers.store');
            Route::post('/transfers/{transaction}/verify-otp', [PortalTransferController::class, 'verifyOtp'])->name('portal.transfers.verify-otp');
            Route::post('/transfers/{transaction}/cancel', [PortalTransferController::class, 'cancel'])->name('portal.transfers.cancel');

            Route::get('/documents', [PortalDocumentController::class, 'index'])->name('portal.documents');
            Route::get('/documents/rib', [PortalDocumentController::class, 'downloadRib'])->name('portal.documents.rib');
            Route::get('/documents/statement', [PortalDocumentController::class, 'downloadStatement'])->name('portal.documents.statement');
            Route::get('/documents/attestation', [PortalDocumentController::class, 'downloadAttestation'])->name('portal.documents.attestation');

            Route::get('/loans', [PortalLoanController::class, 'index'])->name('portal.loans');
            Route::post('/loans/savings-transfer', [PortalLoanController::class, 'transferSavings'])->name('portal.loans.savings-transfer');
            Route::post('/loans/apply', [PortalLoanController::class, 'applyLoan'])->name('portal.loans.apply');

            Route::get('/support', [PortalSupportController::class, 'index'])->name('portal.support');
            Route::post('/support', [PortalSupportController::class, 'store'])->name('portal.support.store');

            Route::get('/profile', [PortalProfileController::class, 'index'])->name('portal.profile');
            Route::post('/profile', [PortalProfileController::class, 'update'])->name('portal.profile.update');
        });

        Route::get('/legal/{page}', [PageController::class, 'legal'])
            ->whereIn('page', [
                'terms-and-conditions', 'privacy-policy', 'cookie-policy',
                'terminos-y-condiciones', 'politica-de-privacidad', 'politica-de-cookies',
            ])
            ->name('legal');
    });
