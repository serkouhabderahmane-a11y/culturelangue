<?php

namespace App\Providers;

use App\Models\NavigationItem;
use App\Models\ServiceCategory;
use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer(['layouts.public', 'layouts.inner'], function ($view) {
            $view->with('navigationItems', NavigationItem::where('is_active', true)
                ->with('children')
                ->orderBy('order')
                ->get());
            $view->with('settings', Setting::pluck('value', 'key')->toArray());
        });

        View::composer('*', function ($view) {
            $view->with('siteName', config('app.name'));
        });
    }
}
