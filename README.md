# Google Charts Flux

Google Charts integration for Laravel Livewire and Flux UI. Composable Blade components with full Livewire reactivity, event bridging, and automatic dark mode support.

## Requirements

- PHP 8.1+
- Laravel 10, 11, or 12
- Livewire 3+ (for reactivity features)
- Alpine.js (included with Livewire)

## Installation

```bash
composer require foleybridgesolutions/google-charts-flux
```

The service provider is auto-discovered. To publish the config file:

```bash
php artisan vendor:publish --tag=google-charts-flux-config
```

## Setup

Add `@googleChartsFluxScripts` to your layout, after `@livewireScripts` or `@fluxScripts`:

```blade
<html>
<body>
    {{ $slot }}

    @fluxScripts {{-- or @livewireScripts --}}
    @googleChartsFluxScripts
</body>
</html>
```

No build step is required. The JavaScript is inlined automatically.

## Quick Start

### Simple chart with inline data

```blade
<x-google-chart
    type="pie"
    :data="[
        ['Task', 'Hours per Day'],
        ['Work', 11],
        ['Eat', 2],
        ['Commute', 2],
        ['Watch TV', 2],
        ['Sleep', 7],
    ]"
    class="h-80"
>
    <x-google-chart.options title="My Daily Activities" :pieHole="0.4" />
</x-google-chart>
```

### Livewire component with wire:model

```php
use Livewire\Component;
use FoleyBridgeSolutions\GoogleChartsFlux\Traits\HasGoogleChart;

class Dashboard extends Component
{
    use HasGoogleChart;

    public array $revenueData = [];

    public function mount(): void
    {
        $this->revenueData = $this->buildChartData(
            headers: ['Month', 'Revenue', 'Expenses'],
            rows: [
                ['Jan', 1000, 400],
                ['Feb', 1170, 460],
                ['Mar', 860, 580],
                ['Apr', 1030, 540],
            ],
        );
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
```

```blade
{{-- livewire/dashboard.blade.php --}}
<div>
    <x-google-chart type="line" wire:model="revenueData" class="h-80">
        <x-google-chart.options title="Revenue vs Expenses" curveType="function" />
        <x-google-chart.axis which="v" title="Amount ($)" :minValue="0" />
    </x-google-chart>
</div>
```

When `$revenueData` changes in the Livewire component, the chart re-renders automatically.

## Chart Types

All 24 Google Charts types are supported via the `type` prop:

| Type | Value | Description |
|------|-------|-------------|
| Annotation Chart | `annotation` | Interactive time-based annotations |
| Area Chart | `area` | Area chart with filled regions |
| Bar Chart | `bar` | Horizontal bar chart |
| Bubble Chart | `bubble` | Bubble chart (x, y, size) |
| Calendar Chart | `calendar` | Calendar heatmap |
| Candlestick Chart | `candlestick` | Financial candlestick chart |
| Column Chart | `column` | Vertical bar chart |
| Combo Chart | `combo` | Mixed chart types in one |
| Donut Chart | `donut` | Pie chart with hole (pieHole=0.4) |
| Gantt Chart | `gantt` | Project timeline / Gantt chart |
| Gauge Chart | `gauge` | Speedometer gauge |
| Geo Chart | `geo` | Geographic map visualization |
| Histogram | `histogram` | Frequency distribution |
| Line Chart | `line` | Line chart |
| Map | `map` | Google Maps markers |
| Org Chart | `org` | Organizational chart / tree |
| Pie Chart | `pie` | Pie chart |
| Sankey Diagram | `sankey` | Flow diagram |
| Scatter Chart | `scatter` | Scatter plot |
| Stepped Area Chart | `stepped-area` | Stepped area chart |
| Table Chart | `table` | Interactive data table |
| Timeline | `timeline` | Timeline of events |
| TreeMap | `treemap` | Hierarchical treemap |
| Word Tree | `wordtree` | Word tree visualization |

## Components

### `<x-google-chart>` — Main Chart

The root component that renders the chart.

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `type` | string | (required) | Chart type (see table above) |
| `:data` | array | null | Data as array-of-arrays (first row = headers) |
| `wire:model` | string | — | Livewire property to bind for reactivity |
| `loading` | string | `'skeleton'` | Loading state: `'skeleton'`, `'spinner'`, or `'none'` |

```blade
<x-google-chart type="bar" :data="$data" class="h-96 w-full" />
```

### `<x-google-chart.options>` — Chart Options

Configures Google Charts options. All props map to Google Charts option keys.

| Prop | Type | Description |
|------|------|-------------|
| `title` | string | Chart title |
| `curveType` | string | `'function'` for smooth lines |
| `:pieHole` | float | Donut hole size (0-1) |
| `:colors` | array | Color palette |
| `legend` | string/array | Legend position or config |
| `:is3D` | bool | Enable 3D rendering |
| `:chartArea` | array | Chart area dimensions |
| `fontName` | string | Font family |
| `:fontSize` | int | Base font size |
| `:animation` | array | Animation config |
| `:tooltip` | array | Tooltip config |
| `backgroundColor` | string | Background color |
| `:extra` | array | Any additional options |

```blade
<x-google-chart.options
    title="Sales Report"
    curveType="function"
    :colors="['#4285F4', '#EA4335', '#FBBC05']"
    :legend="['position' => 'bottom']"
    :extra="['pointSize' => 5]"
/>
```

### `<x-google-chart.data>` — Data Wrapper

Optional wrapper for declarative column/row definitions.

### `<x-google-chart.column>` — Column Definition

Declares a DataTable column.

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `type` | string | `'string'` | Data type: `string`, `number`, `boolean`, `date`, `datetime`, `timeofday` |
| `label` | string | `''` | Column header label |
| `role` | string | null | Column role: `tooltip`, `annotation`, `style`, `certainty`, `emphasis` |
| `id` | string | null | Column ID |

### `<x-google-chart.row>` — Data Row

Declares a data row.

| Prop | Type | Description |
|------|------|-------------|
| `:values` | array | Row values matching column order |

```blade
<x-google-chart type="bar" class="h-96">
    <x-google-chart.data>
        <x-google-chart.column type="string" label="Year" />
        <x-google-chart.column type="number" label="Revenue" />
        <x-google-chart.column type="number" label="Expenses" />
        <x-google-chart.row :values="['2022', 1000, 400]" />
        <x-google-chart.row :values="['2023', 1170, 460]" />
        <x-google-chart.row :values="['2024', 860, 580]" />
    </x-google-chart.data>
</x-google-chart>
```

### `<x-google-chart.event>` — Event Listener

Bridges Google Charts events to Livewire dispatches.

| Prop | Type | Description |
|------|------|-------------|
| `on` | string | Google Charts event name (`select`, `ready`, `onmouseover`, etc.) |
| `emit` | string | Livewire event name to dispatch |

```blade
<x-google-chart type="pie" :data="$data">
    <x-google-chart.event on="select" emit="chartItemSelected" />
</x-google-chart>
```

Handle the event in your Livewire component:

```php
use Livewire\Attributes\On;

#[On('chartItemSelected')]
public function onChartSelect(array $payload): void
{
    // $payload contains: chartType, event, selection, selectedData
    $selectedRow = $payload['selectedData'][0] ?? null;
}
```

The `select` event payload includes:
- `chartType` — The chart type string
- `event` — The event name
- `selection` — Raw Google Charts selection array
- `selectedData` — Array of row data with column labels as keys

### `<x-google-chart.series>` — Series Configuration

Configures individual data series (useful for combo charts and multi-series charts).

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `:index` | int | `0` | Zero-based series index |
| `type` | string | null | Series chart type: `line`, `bars`, `area`, `steppedArea` |
| `color` | string | null | Series color |
| `:lineWidth` | int | null | Line width in pixels |
| `lineDashStyle` | string | null | Dash pattern (e.g. `'4,4'`) |
| `pointShape` | string | null | Point marker shape |
| `:pointSize` | int | null | Point marker size |
| `:areaOpacity` | float | null | Area fill opacity (0-1) |
| `curveType` | string | null | Curve type |
| `:targetAxisIndex` | int | null | Target axis (0=left, 1=right) |
| `:visibleInLegend` | bool | null | Show in legend |
| `:extra` | array | null | Additional series options |

```blade
<x-google-chart type="combo" :data="$data" class="h-96">
    <x-google-chart.series :index="0" type="bars" color="#4285F4" />
    <x-google-chart.series :index="1" type="line" color="#EA4335" :lineWidth="3" />
</x-google-chart>
```

### `<x-google-chart.axis>` — Axis Configuration

Configures horizontal or vertical axis.

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `which` | string | `'h'` | Axis: `'h'` (horizontal) or `'v'` (vertical) |
| `title` | string | null | Axis title |
| `format` | string | null | Number/date format string |
| `:minValue` | float | null | Minimum value |
| `:maxValue` | float | null | Maximum value |
| `:gridlines` | int | null | Number of gridlines |
| `gridlineColor` | string | null | Gridline color |
| `:logScale` | bool | null | Logarithmic scale |
| `textPosition` | string | null | `'out'`, `'in'`, or `'none'` |
| `:slantedText` | bool | null | Slant axis labels |
| `:slantedTextAngle` | int | null | Slant angle in degrees |
| `:extra` | array | null | Additional axis options |

```blade
<x-google-chart type="line" :data="$data">
    <x-google-chart.axis which="h" title="Year" format="####" />
    <x-google-chart.axis which="v" title="Revenue ($)" :minValue="0" />
</x-google-chart>
```

## Data Building

### `HasGoogleChart` Trait

Add the trait to your Livewire components for convenience methods:

```php
use FoleyBridgeSolutions\GoogleChartsFlux\Traits\HasGoogleChart;

class Dashboard extends Component
{
    use HasGoogleChart;

    public array $chartData = [];

    public function mount(): void
    {
        // Simple headers + rows
        $this->chartData = $this->buildChartData(
            headers: ['Month', 'Revenue'],
            rows: [['Jan', 1000], ['Feb', 1170]],
        );

        // Key-value pairs (great for pie charts)
        $this->chartData = $this->buildChartDataFromKeyValue([
            'Work' => 11,
            'Eat' => 2,
            'Sleep' => 7,
        ]);

        // From a collection (e.g. Eloquent results)
        $this->chartData = $this->buildChartDataFromCollection(
            collection: User::all(),
            headers: ['Name', 'Age'],
            mapper: fn($user) => [$user->name, $user->age],
        );
    }
}
```

### `ChartData` Fluent Builder

For more control, use the `ChartData` builder directly:

```php
use FoleyBridgeSolutions\GoogleChartsFlux\Data\ChartData;

$data = ChartData::make()
    ->addStringColumn('Task')
    ->addNumberColumn('Hours per Day')
    ->addTooltipColumn()
    ->addRow(['Work', 11, 'Working hard'])
    ->addRow(['Eat', 2, 'Lunch break'])
    ->addRow(['Sleep', 7, 'Zzz...'])
    ->toArray();
```

Available column methods:
- `addColumn(string $type, string $label, ?string $role)`
- `addStringColumn(string $label)`
- `addNumberColumn(string $label)`
- `addDateColumn(string $label)`
- `addBooleanColumn(string $label)`
- `addTooltipColumn(?string $label)`
- `addStyleColumn(?string $label)`
- `addAnnotationColumn(?string $label)`

### Updating Charts Programmatically

From any Livewire method, you can push new data to a chart without `wire:model`:

```php
// In your Livewire component
$this->updateChart('my-chart-id', $newData);
```

```blade
<x-google-chart id="my-chart-id" type="line" :data="$data" class="h-80" />
```

## Dark Mode

The package integrates with Flux UI's dark mode system automatically. When dark mode is toggled, all charts redraw with dark-friendly colors.

Default dark mode colors (customizable via config):

- Text: `#d4d4d8` (zinc-300)
- Axis labels: `#a1a1aa` (zinc-400)
- Gridlines: `#3f3f46` (zinc-700)
- Baseline: `#52525b` (zinc-600)
- Title: `#fafafa` (zinc-50)

Override dark mode colors in `config/google-charts-flux.php`:

```php
'dark' => [
    'legend' => ['textStyle' => ['color' => '#e5e5e5']],
    'hAxis' => ['textStyle' => ['color' => '#999']],
    'vAxis' => ['textStyle' => ['color' => '#999']],
],
```

## Configuration

Publish the config file to customize defaults:

```bash
php artisan vendor:publish --tag=google-charts-flux-config
```

Key config options:

| Key | Default | Description |
|-----|---------|-------------|
| `api_key` | `''` | Google Maps API key (required for GeoChart and Map types only) |
| `version` | `'current'` | Google Charts version to load |
| `packages` | all 13 packages | Which chart packages to load (reduce for performance) |
| `defaults` | Inter font, transparent bg, animations | Default options merged into every chart |
| `dark` | Zinc color palette | Dark mode option overrides |
| `loading` | `'skeleton'` | Default loading placeholder type |

### Optimizing Package Loading

By default, all 13 Google Charts packages are loaded. For better performance, limit to only what you use:

```php
// config/google-charts-flux.php
'packages' => [
    'corechart',  // Covers: Area, Bar, Bubble, Candlestick, Column, Combo, Histogram, Line, Pie, Scatter, SteppedArea
    'table',      // Table chart
],
```

## Responsive Behavior

Charts automatically resize when their container changes size (via `ResizeObserver`). Use standard CSS/Tailwind to control the chart container dimensions:

```blade
<x-google-chart type="line" :data="$data" class="h-64 sm:h-80 lg:h-96 w-full" />
```

## SPA Navigation

The package handles Livewire SPA navigation (`wire:navigate`) automatically. Charts re-initialize correctly when navigating between pages.

## Publishing Views

To customize the Blade templates:

```bash
php artisan vendor:publish --tag=google-charts-flux-views
```

Views will be published to `resources/views/vendor/google-chart/`.

## License

MIT. See [LICENSE](LICENSE) for details.
