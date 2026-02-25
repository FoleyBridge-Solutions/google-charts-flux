<?php

declare(strict_types=1);

namespace FoleyBridgeSolutions\GoogleChartsFlux\Components;

use FoleyBridgeSolutions\GoogleChartsFlux\Enums\ChartType;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Main Google Chart component.
 *
 * Renders a Google Charts visualization with optional Livewire reactivity,
 * event bridging, and Flux UI dark mode support.
 *
 * Usage:
 *   <x-google-chart type="pie" :data="$data" class="h-80">
 *       <x-google-chart.options title="Tasks" :pieHole="0.4" />
 *       <x-google-chart.event on="select" emit="chartSelected" />
 *   </x-google-chart>
 *
 * @property string $type       The chart type (e.g. 'pie', 'bar', 'line')
 * @property array|null $data   Chart data as array-of-arrays
 * @property string|null $wireModel Livewire model property name for reactivity
 * @property string $loading    Loading state display ('skeleton', 'spinner', 'none')
 */
class Chart extends Component
{
    /**
     * The resolved chart type enum.
     */
    public ChartType $chartType;

    /**
     * Chart data as array-of-arrays (first row = headers).
     *
     * Note: "data" is a reserved keyword in Laravel Blade components
     * and cannot be a public property name. We accept it as a constructor
     * parameter (which maps to the :data HTML attribute) and store it
     * in this non-reserved public property for use in the Blade view.
     *
     * @var array<int, array<mixed>>|null
     */
    public ?array $chartData = null;

    /**
     * Create a new chart component instance.
     *
     * @param string $type         The chart type ('pie', 'bar', 'line', etc.)
     * @param array|null $data     Chart data as array-of-arrays (first row = headers)
     * @param string|null $loading Loading placeholder type (null = use config default)
     */
    public function __construct(
        public string $type,
        ?array $data = null,
        public ?string $loading = null,
    ) {
        $this->chartType = ChartType::resolve($type);
        $this->chartData = $data;
        $this->loading = $loading ?? config('google-charts-flux.loading', 'skeleton');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('google-chart::components.chart');
    }

    /**
     * Get the loader configuration for the Google Charts JS library.
     *
     * @return array{version: string, packages: string[], apiKey: string}
     */
    public function loaderConfig(): array
    {
        return [
            'version' => config('google-charts-flux.version', 'current'),
            'packages' => config('google-charts-flux.packages', ['corechart']),
            'apiKey' => config('google-charts-flux.api_key', ''),
        ];
    }

    /**
     * Get the default chart options from config.
     *
     * @return array<string, mixed>
     */
    public function defaultOptions(): array
    {
        return config('google-charts-flux.defaults', []);
    }

    /**
     * Get the dark mode option overrides from config.
     *
     * @return array<string, mixed>
     */
    public function darkOptions(): array
    {
        return config('google-charts-flux.dark', []);
    }
}
