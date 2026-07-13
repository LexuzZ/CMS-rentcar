<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\AvailableCarsOverview;
use App\Filament\Widgets\DashboardActionsWidget;
use App\Filament\Widgets\DashboardMonthlySummary;
use App\Filament\Widgets\LoginActivityWidget;
use App\Filament\Widgets\RecentTransactions;
use App\Filament\Widgets\TransactionChart;
use App\Filament\Widgets\UserActivityFeedWidget;
use App\Filament\Widgets\UserActivityWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
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
            ->profile()

            // ── Branding ──────────────────────────────────────────
            ->favicon(asset('semetonpesiar.png'))
            ->brandLogo(asset('spt.png'))
            ->brandLogoHeight('4rem')
            ->brandName('Semeton Pesiar')

            // ── Theme ─────────────────────────────────────────────
            ->darkMode(true)               // biarkan user pilih sendiri
            ->font('Poppins')

            ->colors([
                'primary' => Color::Sky,
                'success' => Color::Teal,
                'danger'  => Color::Rose,
                'warning' => Color::Amber,
                'info'    => Color::Indigo,
                'gray'    => Color::Zinc,
            ])

            // ── Layout ────────────────────────────────────────────
            ->sidebarCollapsibleOnDesktop()
            ->sidebarFullyCollapsibleOnDesktop()
            ->sidebarWidth('18rem')
            ->maxContentWidth('full')      // ✅ konten lebih lebar, tidak sempit di tengah
            ->breadcrumbs(false)           // ✅ lebih bersih tanpa breadcrumb di dashboard

            // ── Features ──────────────────────────────────────────
            ->spa()                        // navigasi cepat tanpa full reload
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s') // ✅ polling notifikasi tiap 30 detik

            // ── Global Search ─────────────────────────────────────
            ->globalSearch(true)           // ✅ aktifkan global search (Ctrl+K)
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])

            // ── Collapsible sidebar groups ─────────────────────────
            ->collapsibleNavigationGroups(true) // ✅ grup navigasi bisa di-collapse

            // ── Resources & Pages ─────────────────────────────────
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])

            // ── Widgets ───────────────────────────────────────────
            ->widgets([
                DashboardMonthlySummary::class,
                AvailableCarsOverview::class,
                RecentTransactions::class,
                TransactionChart::class,
                LoginActivityWidget::class,
                UserActivityFeedWidget::class,
                DashboardActionsWidget::class,
            ])

            // ── Middleware ────────────────────────────────────────
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

    public static function canAccess(): bool
    {
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }
}
