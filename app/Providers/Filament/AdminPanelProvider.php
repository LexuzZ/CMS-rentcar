<?php

namespace App\Providers\Filament;

use App\Filament\Resources\CarResource\Widgets\AvailableCarsOverview;
use App\Filament\Resources\CarResource\Widgets\MobilReadyStats;
use App\Filament\Widgets\AnnouncementWidget;
use App\Filament\Widgets\AvailableCarsOverview as WidgetsAvailableCarsOverview;
use App\Filament\Widgets\DaftarTungguTable;
use App\Filament\Widgets\DashboardActionsWidget;
use App\Filament\Widgets\DashboardMonthlySummary;
use App\Filament\Widgets\InvoiceTable;
use App\Filament\Widgets\MobilKeluar;
use App\Filament\Widgets\MobilKembali;
use App\Filament\Widgets\MonthlyStaffRankingWidget;
use App\Filament\Widgets\OverdueTasksWidget;
use App\Filament\Widgets\RecentTransactions;
use App\Filament\Widgets\StaffRankingWidget;
use App\Filament\Widgets\TempoDueToday;
use App\Filament\Widgets\TransactionChart;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Assets\Css;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Filament\Widgets\ChartWidget;
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
            // ->defaultThemeMode(ThemeMode::Light)
            ->darkMode(true)
            ->favicon(asset('semetonpesiar.png'))
            ->brandLogo(asset('spt.png'))
            ->brandLogoHeight('4rem')
            // ->domain('');
            ->colors([
                'primary' => Color::Sky,       // tombol, highlights
                'success' => Color::Teal,      // income
                'danger' => Color::Rose,       // expense
                'warning' => Color::Amber,     // overdue
                'info' => Color::Indigo,       // notice
                'gray' => Color::Zinc,         // teks netral
            ])



            ->sidebarCollapsibleOnDesktop()
            ->sidebarFullyCollapsibleOnDesktop()
            ->sidebarWidth('18rem')
            ->font('0.2rem')
            ->brandName('Semeton Pesiar')
            // ->brandLogo(asset('public/spt.png'))
            ->font('Poppins')
            ->databaseNotifications()
            // ->spa()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            // ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                    // WidgetsAvailableCarsOverview::class,
                DashboardMonthlySummary::class,
                RecentTransactions::class,
                TransactionChart::class,

                    // OverdueTasksWidget::class,
                    // MobilKeluar::class,
                    // MobilKembali::class,
                    // TempoDueToday::class,
                DashboardActionsWidget::class,
                \App\Filament\Widgets\UserActivityStatus::class,
                \App\Filament\Widgets\UserActivityLog::class,

                // StaffRankingWidget::class,
                // MonthlyStaffRankingWidget::class,
                // DaftarTungguTable::class,
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
            ]);
    }

    public static function canAccess(): bool
    {
        // Hanya pengguna dengan peran 'admin' yang bisa melihat halaman ini
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }
}
