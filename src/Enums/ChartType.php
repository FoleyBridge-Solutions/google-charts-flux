<?php

declare(strict_types=1);

namespace FoleyBridgeSolutions\GoogleChartsFlux\Enums;

/**
 * All supported Google Charts visualization types.
 *
 * Each case maps to a Google Charts visualization class name,
 * the required package to load, and the fully-qualified JS class.
 */
enum ChartType: string
{
    case AnnotationChart = 'annotation';
    case AreaChart = 'area';
    case BarChart = 'bar';
    case BubbleChart = 'bubble';
    case CalendarChart = 'calendar';
    case CandlestickChart = 'candlestick';
    case ColumnChart = 'column';
    case ComboChart = 'combo';
    case DonutChart = 'donut';
    case GanttChart = 'gantt';
    case GaugeChart = 'gauge';
    case GeoChart = 'geo';
    case Histogram = 'histogram';
    case LineChart = 'line';
    case Map = 'map';
    case OrgChart = 'org';
    case PieChart = 'pie';
    case SankeyChart = 'sankey';
    case ScatterChart = 'scatter';
    case SteppedAreaChart = 'stepped-area';
    case TableChart = 'table';
    case TimelineChart = 'timeline';
    case TreeMapChart = 'treemap';
    case WordTree = 'wordtree';

    /**
     * Get the Google Charts visualization class name.
     */
    public function className(): string
    {
        return match ($this) {
            self::AnnotationChart => 'AnnotationChart',
            self::AreaChart => 'AreaChart',
            self::BarChart => 'BarChart',
            self::BubbleChart => 'BubbleChart',
            self::CalendarChart => 'Calendar',
            self::CandlestickChart => 'CandlestickChart',
            self::ColumnChart => 'ColumnChart',
            self::ComboChart => 'ComboChart',
            self::DonutChart => 'PieChart',
            self::GanttChart => 'Gantt',
            self::GaugeChart => 'Gauge',
            self::GeoChart => 'GeoChart',
            self::Histogram => 'Histogram',
            self::LineChart => 'LineChart',
            self::Map => 'Map',
            self::OrgChart => 'OrgChart',
            self::PieChart => 'PieChart',
            self::SankeyChart => 'Sankey',
            self::ScatterChart => 'ScatterChart',
            self::SteppedAreaChart => 'SteppedAreaChart',
            self::TableChart => 'Table',
            self::TimelineChart => 'Timeline',
            self::TreeMapChart => 'TreeMap',
            self::WordTree => 'WordTree',
        };
    }

    /**
     * Get the Google Charts package required to load this chart type.
     */
    public function package(): string
    {
        return match ($this) {
            self::AnnotationChart => 'annotationchart',
            self::CalendarChart => 'calendar',
            self::GanttChart => 'gantt',
            self::GaugeChart => 'gauge',
            self::GeoChart => 'geochart',
            self::Map => 'map',
            self::OrgChart => 'orgchart',
            self::SankeyChart => 'sankey',
            self::TableChart => 'table',
            self::TimelineChart => 'timeline',
            self::TreeMapChart => 'treemap',
            self::WordTree => 'wordtree',
            default => 'corechart',
        };
    }

    /**
     * Get the JavaScript namespace for the visualization class.
     *
     * Most charts live under google.visualization, but some (like
     * Gantt, Calendar, Sankey) live under google.charts.
     */
    public function jsNamespace(): string
    {
        return match ($this) {
            self::AnnotationChart,
            self::GanttChart,
            self::CalendarChart,
            self::SankeyChart,
            self::TimelineChart,
            self::TreeMapChart,
            self::WordTree,
            self::Map => 'google.visualization',
            default => 'google.visualization',
        };
    }

    /**
     * Get the fully-qualified JavaScript class path for this chart type.
     */
    public function jsClass(): string
    {
        return $this->jsNamespace() . '.' . $this->className();
    }

    /**
     * Resolve a chart type from a string value.
     *
     * Accepts both the enum value (e.g. 'pie') and the class name
     * (e.g. 'PieChart') for flexibility.
     *
     * @param string $type The chart type string
     * @return self The resolved ChartType enum case
     *
     * @throws \ValueError If the type string doesn't match any chart type
     */
    public static function resolve(string $type): self
    {
        // Try direct enum value match first
        $resolved = self::tryFrom($type);
        if ($resolved !== null) {
            return $resolved;
        }

        // Try matching by class name (case-insensitive)
        $normalized = strtolower($type);
        foreach (self::cases() as $case) {
            if (strtolower($case->className()) === $normalized) {
                return $case;
            }
            if (strtolower($case->name) === $normalized) {
                return $case;
            }
        }

        throw new \ValueError("Unknown chart type: {$type}. Valid types are: " . implode(', ', array_map(fn (self $c) => $c->value, self::cases())));
    }

    /**
     * Get all unique packages needed by a set of chart types.
     *
     * @param self[] $types
     * @return string[]
     */
    public static function packagesFor(array $types): array
    {
        return array_values(array_unique(array_map(fn (self $t) => $t->package(), $types)));
    }

    /**
     * Get all available packages across all chart types.
     *
     * @return string[]
     */
    public static function allPackages(): array
    {
        return array_values(array_unique(array_map(fn (self $t) => $t->package(), self::cases())));
    }
}
