<?php

declare(strict_types=1);

namespace FoleyBridgeSolutions\GoogleChartsFlux\Data;

/**
 * Fluent builder for Google Charts DataTable arrays.
 *
 * Provides a PHP-side API for building chart data arrays in the format
 * expected by Google Charts (array-of-arrays where the first row is headers).
 *
 * Usage:
 *   $data = ChartData::make()
 *       ->addColumn('string', 'Task')
 *       ->addColumn('number', 'Hours per Day')
 *       ->addRow(['Work', 11])
 *       ->addRow(['Eat', 2])
 *       ->addRow(['Sleep', 7])
 *       ->toArray();
 *
 * Or build from a collection:
 *   $data = ChartData::fromCollection(
 *       $users,
 *       headers: ['Name', 'Age', 'Score'],
 *       mapper: fn($user) => [$user->name, $user->age, $user->score],
 *   )->toArray();
 */
class ChartData
{
    /**
     * Column headers.
     *
     * @var array<array{type: string, label: string, role?: string}>
     */
    protected array $columns = [];

    /**
     * Data rows.
     *
     * @var array<array<mixed>>
     */
    protected array $rows = [];

    /**
     * Create a new ChartData instance.
     */
    public function __construct() {}

    /**
     * Static factory method.
     *
     * @return static
     */
    public static function make(): static
    {
        return new static();
    }

    /**
     * Add a column definition.
     *
     * @param string $type  The data type ('string', 'number', 'boolean', 'date', 'datetime', 'timeofday')
     * @param string $label The column header label
     * @param string|null $role Optional column role ('tooltip', 'annotation', 'style', etc.)
     * @return static
     */
    public function addColumn(string $type, string $label, ?string $role = null): static
    {
        $col = ['type' => $type, 'label' => $label];
        if ($role !== null) {
            $col['role'] = $role;
        }
        $this->columns[] = $col;

        return $this;
    }

    /**
     * Add a string column.
     *
     * @param string $label The column header label
     * @return static
     */
    public function addStringColumn(string $label): static
    {
        return $this->addColumn('string', $label);
    }

    /**
     * Add a number column.
     *
     * @param string $label The column header label
     * @return static
     */
    public function addNumberColumn(string $label): static
    {
        return $this->addColumn('number', $label);
    }

    /**
     * Add a date column.
     *
     * @param string $label The column header label
     * @return static
     */
    public function addDateColumn(string $label): static
    {
        return $this->addColumn('date', $label);
    }

    /**
     * Add a boolean column.
     *
     * @param string $label The column header label
     * @return static
     */
    public function addBooleanColumn(string $label): static
    {
        return $this->addColumn('boolean', $label);
    }

    /**
     * Add a tooltip role column.
     *
     * @param string $label The tooltip column label
     * @return static
     */
    public function addTooltipColumn(string $label = ''): static
    {
        return $this->addColumn('string', $label, 'tooltip');
    }

    /**
     * Add a style role column.
     *
     * @param string $label The style column label
     * @return static
     */
    public function addStyleColumn(string $label = ''): static
    {
        return $this->addColumn('string', $label, 'style');
    }

    /**
     * Add a annotation role column.
     *
     * @param string $label The annotation column label
     * @return static
     */
    public function addAnnotationColumn(string $label = ''): static
    {
        return $this->addColumn('string', $label, 'annotation');
    }

    /**
     * Add a single data row.
     *
     * @param array<mixed> $values Row values in the same order as columns
     * @return static
     */
    public function addRow(array $values): static
    {
        $this->rows[] = $values;

        return $this;
    }

    /**
     * Add multiple data rows at once.
     *
     * @param array<array<mixed>> $rows Array of row arrays
     * @return static
     */
    public function addRows(array $rows): static
    {
        foreach ($rows as $row) {
            $this->rows[] = $row;
        }

        return $this;
    }

    /**
     * Build data from an iterable collection using a mapper function.
     *
     * @param iterable<mixed> $collection The source data
     * @param array<string> $headers Simple string headers (column labels)
     * @param callable $mapper Function that maps each item to a row array
     * @return static
     */
    public static function fromCollection(iterable $collection, array $headers, callable $mapper): static
    {
        $instance = new static();

        // Add columns from simple headers (assumes all are strings/numbers — use addColumn for typed)
        foreach ($headers as $header) {
            $instance->columns[] = ['type' => 'string', 'label' => $header];
        }

        foreach ($collection as $item) {
            $instance->rows[] = $mapper($item);
        }

        return $instance;
    }

    /**
     * Build data from key-value pairs (for pie charts, gauges, etc.).
     *
     * @param array<string, int|float> $data Associative array of label => value
     * @param string $labelHeader Header for the label column
     * @param string $valueHeader Header for the value column
     * @return static
     */
    public static function fromKeyValue(array $data, string $labelHeader = 'Label', string $valueHeader = 'Value'): static
    {
        $instance = new static();
        $instance->addStringColumn($labelHeader);
        $instance->addNumberColumn($valueHeader);

        foreach ($data as $label => $value) {
            $instance->addRow([$label, $value]);
        }

        return $instance;
    }

    /**
     * Convert to Google Charts array-of-arrays format.
     *
     * The first row contains column labels, subsequent rows contain data.
     * This is the format expected by the <x-google-chart :data="..."> prop.
     *
     * @return array<array<mixed>>
     */
    public function toArray(): array
    {
        $headers = array_map(fn (array $col) => $col['label'], $this->columns);

        return array_merge([$headers], $this->rows);
    }

    /**
     * Get the raw columns definitions (for advanced use).
     *
     * @return array<array{type: string, label: string, role?: string}>
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Get the raw rows data.
     *
     * @return array<array<mixed>>
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    /**
     * Get the total number of data rows (excluding header).
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->rows);
    }

    /**
     * Check if the data set is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }
}
