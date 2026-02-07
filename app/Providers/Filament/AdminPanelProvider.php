<?php

namespace App\Providers\Filament;

use Aldids\FilamentDbSync\FilamentDbSync;
use App\Filament\Resources\GrainAmorts\Widgets\GrainApproval;
use App\Filament\Resources\Loans\Widgets\LoanApproval;
use App\Filament\Resources\Members\Widgets\MemberSavingChart;
use App\Filament\Resources\Members\Widgets\MemberStatus;
use App\Filament\Resources\SavingReports\Widgets\MemberReportStats;
use App\Filament\Resources\Savings\Widgets\SavingChart;
use App\Models\Grain;
use App\Models\Loan;
use App\Observers\GrainObserver;
use App\Observers\LoanObserver;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Enums\UserMenuPosition;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Rupadana\ApiService\ApiServicePlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('/')
            ->viteTheme('resources/css/filament/admin/theme.css')
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
                'primary' => Color::Blue,
            ])
//            ->strictAuthorization()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
                MemberReportStats::class,
                MemberStatus::class,
                LoanApproval::class,
                GrainApproval::class,
                SavingChart::class,
                MemberSavingChart::class
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
            ->registerErrorNotification(
                title: 'An error occurred',
                body: 'Please try again later.',
            )
            ->registerErrorNotification(
                title: 'Record not found',
                body: 'A record you are looking for does not exist.',
                statusCode: 404,
            )->registerErrorNotification(
                title: 'Permission denied',
                body: 'You not have permission to access this page.',
                statusCode: 403,
            )
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
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
                    ]),
                FilamentDbSync::make(),
                ApiServicePlugin::make(),
                BreezyCore::make()
                    ->enableBrowserSessions(condition: true)
                    ->myProfile(
                        // Sets the 'account' link in the panel User Menu (default = true)
                        shouldRegisterNavigation: true, // Customizes the 'account' link label in the panel User Menu (default = null)
                        hasAvatars: false, // Adds a main navigation item for the My Profile page (default = false)
                        slug: 'my-profile', // Sets the navigation group for the My Profile page (default = null)
                        navigationGroup: 'Settings', // Enables the avatar upload form component (default = false)
                        userMenuLabel: 'My Profile' // Sets the slug for the profile page (default = 'my-profile')
                    ),
//                    ->simpleResourcePermissionView()
            ])
            ->bootUsing(function (Panel $panel) {
                Loan::observe(LoanObserver::class);
                Grain::observe(GrainObserver::class);
            })->viteTheme('resources/css/filament/admin/theme.css');
    }
}
