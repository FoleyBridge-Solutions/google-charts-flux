<?php

declare(strict_types=1);

namespace FoleyBridgeSolutions\GoogleChartsFlux\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Chart event sub-component.
 *
 * Registers a Google Charts event listener and bridges it to a
 * Livewire event dispatch. When the Google Charts event fires,
 * the specified Livewire event is dispatched with the event payload.
 *
 * Usage:
 *   <x-google-chart type="pie" :data="$data">
 *       <x-google-chart.event on="select" emit="chartItemSelected" />
 *       <x-google-chart.event on="ready" emit="chartReady" />
 *   </x-google-chart>
 *
 * In your Livewire component:
 *   #[On('chartItemSelected')]
 *   public function onChartSelect(array $selection, array $selectedData): void
 *   {
 *       // Handle the selection...
 *   }
 *
 * Available events depend on chart type. Common ones:
 * - 'select'       — User clicks a data point (most chart types)
 * - 'ready'        — Chart finished rendering
 * - 'error'        — Chart encountered an error
 * - 'onmouseover'  — Mouse enters a data point
 * - 'onmouseout'   — Mouse leaves a data point
 * - 'regionClick'  — GeoChart region clicked
 *
 * @see https://developers.google.com/chart/interactive/docs/events
 */
class Event extends Component
{
    /**
     * Create a new event component instance.
     *
     * @param string $on   The Google Charts event name to listen for
     * @param string $emit The Livewire event name to dispatch
     */
    public function __construct(
        public string $on,
        public string $emit,
    ) {}

    /**
     * Get the event definition as an array for JSON serialization.
     *
     * @return array{on: string, emit: string}
     */
    public function toArray(): array
    {
        return [
            'on' => $this->on,
            'emit' => $this->emit,
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('google-chart::components.event');
    }
}
