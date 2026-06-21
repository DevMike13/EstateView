<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use App\Http\Responses\LogoutResponse;
use App\Models\BillingPayment;
use App\Models\ClientAppointment;
use App\Models\LotReservation;
use App\Models\PurchaseAccount;
use App\Models\User;
use App\Models\UserInfo;
use App\Observers\AppointmentObserver;
use App\Observers\BillingPaymentObserver;
use App\Observers\LotReservationObserver;
use App\Observers\PurchaseAccountObserver;
use App\Observers\UserInfoObserver;
use App\Observers\UserObserver;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        UserInfo::observe(UserInfoObserver::class);
        LotReservation::observe(LotReservationObserver::class);
        ClientAppointment::observe(AppointmentObserver::class);
        PurchaseAccount::observe(PurchaseAccountObserver::class);
        BillingPayment::observe(BillingPaymentObserver::class);
        
        $this->loadMigrationsFrom(database_path('migrations'));
 
        if (is_dir(database_path('migrations/prpcmblmts'))) {
            $this->loadMigrationsFrom(database_path('migrations/prpcmblmts'));
        }

        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_END,
            fn (): string => Blade::render('<livewire:components.dashboard.custom-notification />')
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_BEFORE,
            fn (): string => Blade::render('<livewire:components.dashboard.notification-bell />')
        );
    }
}
