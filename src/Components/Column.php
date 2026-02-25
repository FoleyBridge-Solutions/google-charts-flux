<?php

declare(strict_types=1);

namespace FoleyBridgeSolutions\GoogleChartsFlux\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Chart column sub-component.
 *
 * Declares a column in the chart's DataTable when using the
 * declarative (composable) data definition approach.
 *
 * Usage:
 *   <x-google-chart.data>
 *       <x-google-chart.column type="string" label="Task" />
 *       <x-google-chart.column type="number" label="Hours" />
 *       <x-google-chart.column type="number" role="tooltip" />
 *   </x-google-chart.data>
 *
 * @see https://developers.google.com/chart/interactive/docs/reference#DataTable
 */
class Column extends Component
{
    /**
     * Create a new column component instance.
     *
     * @param string $type  The data type: 'string', 'number', 'boolean', 'date', 'datetime', 'timeofday'
     * @param string $label The column header label
     * @param string|null $role Optional column role: 'tooltip', 'annotation', 'style', 'certainty', 'emphasis', 'interval', 'scope'
     * @param string|null $id Optional column ID
     * @param string|null $pattern Optional format pattern
     */
    public function __construct(
        public string $type = 'string',
        public string $label = '',
        public ?string $role = null,
        public ?string $id = null,
        public ?string $pattern = null,
    ) {}

    /**
     * Get the column definition as an array for JSON serialization.
     *
     * @return array{type: string, label: string, role?: string, id?: string, pattern?: string}
     */
    public function toArray(): array
    {
        $col = [
            'type' => $this->type,
            'label' => $this->label,
        ];

        if ($this->role !== null) {
            $col['role'] = $this->role;
        }
        if ($this->id !== null) {
            $col['id'] = $this->id;
        }
        if ($this->pattern !== null) {
            $col['pattern'] = $this->pattern;
        }

        return $col;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('google-chart::components.column');
    }
}
