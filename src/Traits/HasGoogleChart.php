<?php

declare(strict_types=1);

namespace FoleyBridgeSolutions\GoogleChartsFlux\Traits;

use FoleyBridgeSolutions\GoogleChartsFlux\Data\ChartData;

/**
 * Trait for Livewire components that use Google Charts.
 *
 * Provides convenience methods for building chart data and handling
 * chart events within Livewire components.
 *
 * Usage:
 *   class Dashboard extends Component
 *   {
 *       use HasGoogleChart;
 *
 *       public array $revenueData = [];
 *
 *       public function mount(): void
 *       {
 *           $this->revenueData = $this->buildChartData(
 *               headers: ['Month', 'Revenue', 'Expenses'],
 *               rows: [
 *                   ['Jan', 1000, 400],
 *                   ['Feb', 1170, 460],
 *               ],
 *           );
 *       }
 *   }
 */
trait HasGoogleChart
{
    /**
     * Build a chart data array from headers and rows.
     *
     * @param array<string> $headers Column header labels
     * @param array<array<mixed>> $rows Data rows
     * @return array<array<mixed>> Google Charts array-of-arrays format
     */
    protected function buildChartData(array $headers, array $rows): array
    {
        return array_merge([$headers], $rows);
    }

    /**
     * Build chart data from key-value pairs (useful for pie/donut charts).
     *
     * @param array<string, int|float> $data Associative array of label => value
     * @param string $labelHeader Header for the label column
     * @param string $valueHeader Header for the value column
     * @return array<array<mixed>> Google Charts array-of-arrays format
     */
    protected function buildChartDataFromKeyValue(
        array $data,
        string $labelHeader = 'Label',
        string $valueHeader = 'Value',
    ): array {
        return ChartData::fromKeyValue($data, $labelHeader, $valueHeader)->toArray();
    }

    /**
     * Build chart data from an iterable collection.
     *
     * @param iterable<mixed> $collection The source data
     * @param array<string> $headers Column header labels
     * @param callable $mapper Function that maps each item to a row array
     * @return array<array<mixed>> Google Charts array-of-arrays format
     */
    protected function buildChartDataFromCollection(
        iterable $collection,
        array $headers,
        callable $mapper,
    ): array {
        return ChartData::fromCollection($collection, $headers, $mapper)->toArray();
    }

    /**
     * Create a new ChartData builder instance.
     *
     * @return ChartData
     */
    protected function chartData(): ChartData
    {
        return ChartData::make();
    }

    /**
     * Dispatch a browser event to update a specific chart's data.
     *
     * This allows updating a chart from any Livewire method without
     * needing wire:model. The chart must have an id matching $chartId.
     *
     * @param string $chartId The chart element's ID
     * @param array<array<mixed>> $data New chart data
     * @return void
     */
    protected function updateChart(string $chartId, array $data): void
    {
        $this->dispatch('google-chart-update', chartId: $chartId, data: $data);
    }
}
