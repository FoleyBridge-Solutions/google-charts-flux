<?php

declare(strict_types=1);

namespace FoleyBridgeSolutions\GoogleChartsFlux\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Chart data wrapper sub-component.
 *
 * Groups column and row sub-components for the declarative data
 * definition approach. This is optional — you can also pass data
 * directly to the parent chart via :data or wire:model.
 *
 * Usage:
 *   <x-google-chart type="bar" class="h-96">
 *       <x-google-chart.data>
 *           <x-google-chart.column type="string" label="Year" />
 *           <x-google-chart.column type="number" label="Revenue" />
 *           <x-google-chart.row :values="['2023', 1000]" />
 *           <x-google-chart.row :values="['2024', 1170]" />
 *       </x-google-chart.data>
 *   </x-google-chart>
 */
class Data extends Component
{
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('google-chart::components.data');
    }
}
