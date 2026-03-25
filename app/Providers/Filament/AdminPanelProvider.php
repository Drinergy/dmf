<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Assets\Css;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\View\TablesRenderHook;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
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
            ->login(\App\Filament\Pages\Auth\CustomLogin::class)
            ->brandName('DMF Dental Training Center')
            ->brandLogo(fn () => view('filament.brand'))
            ->favicon(asset('favicon.ico'))
            ->assets([
                Css::make('dmf-filament-admin')
                    ->html(function (): string {
                        $path = public_path('css/filament-admin.css');
                        $version = is_file($path) ? (string) filemtime($path) : (string) time();

                        return "<link href=\"/css/filament-admin.css?v={$version}\" rel=\"stylesheet\" data-navigate-track />";
                    }),
            ])
            ->navigation(false)
            ->topNavigation()
            ->maxContentWidth('6xl')
            ->colors([
                // Primary = brand gold (accent color of the main site)
                'primary' => Color::hex('#FAB21B'),
                // Gray is mapped to the dark navy used on the main site
                'gray'    => Color::Slate,
            ])
            ->font('Inter')
            ->darkMode(false)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                // Removed generic Dashboard 
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                // Removed FilamentInfoWidget — not needed for client-facing admin
            ])
            ->renderHook(
                PanelsRenderHook::FOOTER,
                fn () => view('filament.footer'),
            )
            ->renderHook(
                TablesRenderHook::TOOLBAR_SEARCH_AFTER,
                fn () => view('filament.tables.enrollments-refresh'),
                scopes: \App\Filament\Resources\EnrollmentResource\Pages\ListEnrollments::class,
            )
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
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}