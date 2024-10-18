<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Blade::directive('money', function ($amount): string {
            return "<?php echo (new \NumberFormatter('pt_BR', \NumberFormatter::CURRENCY))->format(floatval($amount)) ; ?>";
        });
    }
}
