<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Member;
use App\Models\Grain;
use App\Models\Loan;
use App\Observers\GrainObserver;
use App\Observers\LoanObserver;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Croustibat\FilamentJobsMonitor\FilamentJobsMonitorPlugin;
use Filament\Enums\UserMenuPosition;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Str;
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
            ->profile(isSimple: false)
            ->passwordReset()
            ->spa()
            ->sidebarWidth('15rem')
            ->databaseNotifications()
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth(Width::Full)
            ->simplePageMaxContentWidth(Width::Small)
            ->brandLogo('/GECHAAN.jpg')
            ->userMenu(position: UserMenuPosition::Sidebar)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->strictAuthorization()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
//                FilamentInfoWidget::class,
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
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
//                FilamentJobsMonitorPlugin::make()
//                    ->enableNavigation(),
                FilamentShieldPlugin::make()
                    ->navigationLabel('Roles')                  // string|Closure|null
                    ->navigationIcon('heroicon-o-home')         // string|Closure|null
                    ->activeNavigationIcon('heroicon-s-home')   // string|Closure|null
                    ->navigationGroup('Settings')                  // string|Closure|null
                    ->navigationSort(10)                        // int|Closure|null
                    ->registerNavigation(true)
                    ->gridColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 4,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ])
//                    ->simpleResourcePermissionView()
            ])
            ->bootUsing(function (Panel $panel) {
                Loan::observe(LoanObserver::class);
                Grain::observe(GrainObserver::class);
            });
    }
}
