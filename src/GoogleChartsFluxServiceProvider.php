<?php

declare(strict_types=1);

namespace FoleyBridgeSolutions\GoogleChartsFlux;

use FoleyBridgeSolutions\GoogleChartsFlux\Components\Axis;
use FoleyBridgeSolutions\GoogleChartsFlux\Components\Chart;
use FoleyBridgeSolutions\GoogleChartsFlux\Components\Column;
use FoleyBridgeSolutions\GoogleChartsFlux\Components\Data;
use FoleyBridgeSolutions\GoogleChartsFlux\Components\Event;
use FoleyBridgeSolutions\GoogleChartsFlux\Components\Options;
use FoleyBridgeSolutions\GoogleChartsFlux\Components\Row;
use FoleyBridgeSolutions\GoogleChartsFlux\Components\Series;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

/**
 * Service provider for the Google Charts Flux package.
 *
 * Registers Blade components, directives, config, and views.
 */
class GoogleChartsFluxServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/google-charts-flux.php',
            'google-charts-flux'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPublishing();
        $this->registerViews();
        $this->registerComponents();
        $this->registerBladeDirectives();
    }

    /**
     * Register the package's publishable resources.
     */
    protected function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/google-charts-flux.php' => config_path('google-charts-flux.php'),
            ], 'google-charts-flux-config');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/google-chart'),
            ], 'google-charts-flux-views');
        }
    }

    /**
     * Register the package's views.
     */
    protected function registerViews(): void
    {
        $this->loadViewsFrom(
            __DIR__ . '/../resources/views',
            'google-chart'
        );
    }

    /**
     * Register the package's Blade components.
     *
     * Components are registered with the 'google-chart' prefix:
     * - <x-google-chart>           → Chart (main wrapper)
     * - <x-google-chart.options>   → Options
     * - <x-google-chart.column>    → Column
     * - <x-google-chart.row>       → Row
     * - <x-google-chart.data>      → Data
     * - <x-google-chart.event>     → Event
     * - <x-google-chart.series>    → Series
     * - <x-google-chart.axis>      → Axis
     */
    protected function registerComponents(): void
    {
        Blade::component('google-chart', Chart::class);
        Blade::component('google-chart.options', Options::class);
        Blade::component('google-chart.column', Column::class);
        Blade::component('google-chart.row', Row::class);
        Blade::component('google-chart.data', Data::class);
        Blade::component('google-chart.event', Event::class);
        Blade::component('google-chart.series', Series::class);
        Blade::component('google-chart.axis', Axis::class);
    }

    /**
     * Register Blade directives for the package.
     *
     * @googleChartsFluxScripts — Outputs the Google Charts loader script
     * and the Alpine.js component registration. Place this in your layout
     * before the closing </body> tag, after @fluxScripts / @livewireScripts.
     */
    protected function registerBladeDirectives(): void
    {
        Blade::directive('googleChartsFluxScripts', function () {
            return "<?php echo view('google-chart::scripts')->render(); ?>";
        });
    }
}
