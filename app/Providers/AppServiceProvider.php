<?php

namespace App\Providers;

use NumberFormatter;
use Illuminate\Support\Facades\Blade;
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
        Blade::directive('money', function ($amount) {
            $amount = floatval($amount);

            $formatter = new NumberFormatter('pt_BR', NumberFormatter::CURRENCY);
            $formatted = $formatter->format($amount, 'BRL');

            return "<?php echo $formatted; ?>";
        });
    }
}
