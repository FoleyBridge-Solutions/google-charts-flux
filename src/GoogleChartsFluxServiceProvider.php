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
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

/**
 * Service provider for the Google Charts Flux package.
 *
 * Registers Blade components, directives, config, views, and
 * auto-injects the Google Charts script into HTML responses.
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
        $this->registerAutoInjection();
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
     * and the Alpine.js component registration. Can be used manually
     * if auto-injection is disabled. Otherwise, scripts are injected
     * automatically before </body> on all HTML responses.
     */
    protected function registerBladeDirectives(): void
    {
        Blade::directive('googleChartsFluxScripts', function () {
            return "<?php echo view('google-chart::scripts')->render(); ?>";
        });
    }

    /**
     * Auto-inject the Google Charts script into HTML responses.
     *
     * Hooks into Laravel's RequestHandled event (same pattern as
     * Livewire's SupportAutoInjectedAssets) to inject the script
     * tag before </body> on all successful HTML responses.
     *
     * Can be disabled by setting 'google-charts-flux.inject_assets'
     * to false in config.
     */
    protected function registerAutoInjection(): void
    {
        $this->app['events']->listen(RequestHandled::class, function (RequestHandled $handled) {
            if (config('google-charts-flux.inject_assets', true) === false) {
                return;
            }

            // Only inject into successful HTML responses
            $contentType = $handled->response->headers->get('content-type', '');
            if (! str_contains($contentType, 'text/html')) {
                return;
            }
            if (! method_exists($handled->response, 'status') || $handled->response->status() !== 200) {
                return;
            }

            $html = $handled->response->getContent();
            if ($html === false || ! str_contains($html, '</body>')) {
                return;
            }

            // Don't double-inject if @googleChartsFluxScripts was used manually
            if (str_contains($html, '/* google-charts-flux */')) {
                return;
            }

            $scriptTag = $this->buildScriptTag();

            $originalContent = $handled->response->original;
            $handled->response->setContent(
                preg_replace('/(<\s*\/\s*body\s*>)/i', $scriptTag . '$1', $html, 1)
            );
            $handled->response->original = $originalContent;
        });
    }

    /**
     * Build the inline <script> tag containing the Google Charts Flux JS.
     *
     * @return string
     */
    protected function buildScriptTag(): string
    {
        $jsPath = __DIR__ . '/../resources/js/google-charts-flux.js';
        $js = file_get_contents($jsPath);

        return "\n<script>/* google-charts-flux */" . $js . "</script>\n";
    }
}
