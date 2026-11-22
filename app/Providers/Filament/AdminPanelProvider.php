<?php

namespace App\Providers\Filament;

use App\Http\Middleware\SetAdminLocale;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('Venatomar · Admin')
            ->brandLogo(fn () => view('filament.brand'))
            ->brandLogoHeight('2rem')
            ->favicon(asset('favicon.svg'))
            ->colors([
                'primary' => Color::hex('#0FA57E'),
                'warning' => Color::hex('#F26B1D'),
            ])
            ->databaseTransactions()
            ->sidebarCollapsibleOnDesktop()
            // The public site boots Livewire from its Vite bundle (inject_assets = false),
            // so the admin panel loads Livewire's own script explicitly.
            ->renderHook(PanelsRenderHook::HEAD_END, fn () => Blade::render('<script src="{{ asset("js/venatomar-push.js") }}" defer></script>@livewireStyles'))
            ->renderHook(PanelsRenderHook::BODY_END, fn () => Blade::render('@livewireScripts'))
            // Push notification toggle and language switcher (French by default): top bar and login page.
            ->renderHook(PanelsRenderHook::USER_MENU_BEFORE, fn () => view('filament.push-toggle'))
            ->renderHook(PanelsRenderHook::USER_MENU_BEFORE, fn () => view('filament.locale-switcher'))
            ->renderHook(PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, fn () => view('filament.locale-switcher', ['center' => true]))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            // Persistent: also applied to the Livewire requests of the panel.
            ->middleware([SetAdminLocale::class], isPersistent: true)
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
