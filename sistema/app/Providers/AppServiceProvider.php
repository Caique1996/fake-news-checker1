<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('path.public', function() {
            return realpath(base_path().'/../public_html');
        });

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('lng', function ($key) {
            return "<?php echo trans($key); ?>";
        });
        Blade::directive('html_select', function ($data) {
            $name = $data['name'];
            $label = $data['label'];
            $options = $data['options'];
            return "<?php echo htmlSelectInput($name,$label,$options); ?>";
        });

    }
}
