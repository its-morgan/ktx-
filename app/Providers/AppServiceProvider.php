<?php

namespace App\Providers;

use App\Models\Sinhvien;
use App\Observers\SinhvienObserver;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\View\Components\Badge;

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
        Sinhvien::observe(SinhvienObserver::class);

        Blade::directive('badge', function ($expression) {
            return "<?php echo \\App\\View\\Components\\Badge::renderDirect($expression); ?>";
        });
    }
}
