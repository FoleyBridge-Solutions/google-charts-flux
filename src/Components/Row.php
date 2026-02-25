<?php

declare(strict_types=1);

namespace FoleyBridgeSolutions\GoogleChartsFlux\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Chart row sub-component.
 *
 * Declares a data row in the chart's DataTable when using the
 * declarative (composable) data definition approach.
 *
 * Usage:
 *   <x-google-chart.data>
 *       <x-google-chart.column type="string" label="Task" />
 *       <x-google-chart.column type="number" label="Hours" />
 *       <x-google-chart.row :values="['Work', 11]" />
 *       <x-google-chart.row :values="['Sleep', 7]" />
 *   </x-google-chart.data>
 */
class Row extends Component
{
    /**
     * Create a new row component instance.
     *
     * @param array $values The row values, in the same order as declared columns
     */
    public function __construct(
        public array $values = [],
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('google-chart::components.row');
    }
}
