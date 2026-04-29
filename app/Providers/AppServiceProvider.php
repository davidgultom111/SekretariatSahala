<?php

namespace App\Providers;

use App\Models\Member;
use App\Observers\MemberObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Middleware\HandleCors;

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
        // Register MemberObserver to auto-generate id_jemaat
        Member::observe(MemberObserver::class);
    }
}


