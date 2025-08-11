<?php

namespace App\Providers;

use App\Models\BorrowRequest;
use App\Models\VisitRequest;
use App\Models\TestingRequest;
use App\Observers\BorrowRequestObserver;
use App\Observers\VisitRequestObserver;
use App\Observers\TestingRequestObserver;
use Illuminate\Support\ServiceProvider;

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
        // Register model observers for automatic notifications
        BorrowRequest::observe(BorrowRequestObserver::class);
        VisitRequest::observe(VisitRequestObserver::class);
        TestingRequest::observe(TestingRequestObserver::class);
    }
}
