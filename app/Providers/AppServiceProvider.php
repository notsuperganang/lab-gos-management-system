<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\View;
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
        // Share site settings with all views
        View::composer('*', function ($view) {
            $siteSettings = cache()->remember('site_settings', 3600, function () {
                return SiteSetting::active()->get()->pluck('parsed_content', 'key')->toArray();
            });

            $view->with('siteSettings', $siteSettings);
        });

        // Register Blade directive for rich text parsing
        \Illuminate\Support\Facades\Blade::directive('richtext', function ($expression) {
            return "<?php echo app(App\Services\RichTextParser::class)->parse($expression); ?>";
        });

        // Register Blade directive for plain text extraction with limit
        \Illuminate\Support\Facades\Blade::directive('plaintext', function ($expression) {
            $parts = explode(',', $expression, 2);
            $content = trim($parts[0]);
            $limit = isset($parts[1]) ? trim($parts[1]) : 'null';
            return "<?php echo app(App\Services\RichTextParser::class)->toPlainText($content, $limit); ?>";
        });
    }
}
