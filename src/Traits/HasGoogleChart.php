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
     * Breakdown of items grouped into the "Other" slice by groupSmallSlices().
     *
     * Populated automatically when groupSmallSlices() absorbs small slices.
     * Structure: ['label' => string, 'items' => array<int, array{0: string, 1: int|float}>]
     * Empty array when no grouping occurred.
     *
     * Public because Livewire must pass it to the Blade view for JS tooltip rendering.
     */
    public array $otherBreakdown = [];

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
     * Group small slices below a percentage threshold into a single "Other" row.
     *
     * Useful for pie/donut charts where many tiny slices clutter the display.
     * Only groups when there are 2+ small rows — a single small row is kept as-is.
     *
     * @param array<int, array<mixed>> $data Chart data in array-of-arrays format (first row = headers)
     * @param float $threshold Percentage threshold (0–100). Rows below this % of total are grouped.
     * @param string $label Label for the grouped row
     * @return array<int, array<mixed>> Chart data with small rows merged into one
     */
    protected function groupSmallSlices(
        array $data,
        float $threshold = 5.0,
        string $label = 'Other',
    ): array {
        $this->otherBreakdown = [];

        // Need at least a header row + 1 data row
        if (count($data) < 2) {
            return $data;
        }

        $header = $data[0];
        $rows = array_slice($data, 1);

        // Sum all values (column index 1)
        $total = array_sum(array_column($rows, 1));

        if ($total <= 0) {
            return $data;
        }

        // "Other budget" algorithm: threshold controls the max % the Other
        // group can occupy. Greedily absorb the smallest slices first until
        // adding the next one would exceed the budget.
        $budget = ($threshold / 100) * $total;

        // Sort by value ascending (smallest first), preserving original keys
        $indexed = $rows;
        usort($indexed, fn (array $a, array $b) => $a[1] <=> $b[1]);

        $otherSum = 0;
        $absorbedLabels = [];
        $absorbedItems = [];

        foreach ($indexed as $row) {
            if ($otherSum + $row[1] > $budget) {
                break;
            }
            $otherSum += $row[1];
            $absorbedLabels[] = $row[0];
            $absorbedItems[] = [$row[0], $row[1]];
        }

        // Don't group if fewer than 2 rows absorbed — nothing meaningful to merge
        if (count($absorbedLabels) < 2) {
            return $data;
        }

        $this->otherBreakdown = [
            'label' => $label,
            'items' => $absorbedItems,
        ];

        // Build result: keep non-absorbed rows in their original order, append Other
        $absorbedSet = array_flip($absorbedLabels);
        $keep = [];

        foreach ($rows as $row) {
            if (! isset($absorbedSet[$row[0]])) {
                $keep[] = $row;
            }
        }

        $keep[] = [$label, $otherSum];

        return array_merge([$header], $keep);
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
