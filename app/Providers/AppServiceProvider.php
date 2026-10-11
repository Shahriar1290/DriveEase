<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\View\Composers\NotificationComposer;
use App\View\Composers\WishlistComposer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('pagination::bootstrap-5');
        View::composer(['layouts.*'], NotificationComposer::class);
        View::composer(['home.index', 'home.vehicles', 'home.vehicle-detail'], WishlistComposer::class);
    }
}
