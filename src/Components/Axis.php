<?php

declare(strict_types=1);

namespace FoleyBridgeSolutions\GoogleChartsFlux\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Chart axis sub-component.
 *
 * Configures the horizontal or vertical axis of a chart.
 * Maps to Google Charts hAxis / vAxis options.
 *
 * Usage:
 *   <x-google-chart type="line" :data="$data">
 *       <x-google-chart.axis which="h" title="Year" format="####" />
 *       <x-google-chart.axis which="v" title="Revenue ($)" :minValue="0" />
 *   </x-google-chart>
 *
 * @see https://developers.google.com/chart/interactive/docs/gallery/linechart#axis-options
 */
class Axis extends Component
{
    /**
     * The axis configuration as an array.
     *
     * @var array<string, mixed>
     */
    public array $axisConfig;

    /**
     * Create a new axis component instance.
     *
     * @param string $which          Which axis: 'h' (horizontal) or 'v' (vertical)
     * @param string|null $title     Axis title text
     * @param string|null $format    Number/date format string
     * @param float|null $minValue   Minimum axis value
     * @param float|null $maxValue   Maximum axis value
     * @param int|null $gridlines    Number of gridlines (including baseline)
     * @param string|null $gridlineColor Gridline color
     * @param int|null $minorGridlines Number of minor gridlines
     * @param string|null $baseline  Baseline value
     * @param string|null $baselineColor Baseline color
     * @param string|null $textPosition Text position: 'out', 'in', 'none'
     * @param array|null $textStyle  Text style config
     * @param array|null $titleTextStyle Title text style config
     * @param bool|null $logScale    Use logarithmic scale
     * @param string|null $direction Axis direction: 1 (normal) or -1 (reversed)
     * @param array|null $viewWindow View window config {min, max}
     * @param array|null $ticks      Explicit tick values
     * @param bool|null $slantedText Slant text on horizontal axis
     * @param int|null $slantedTextAngle Angle for slanted text
     * @param int|null $maxTextLines Max text lines for axis labels
     * @param array|null $extra      Additional axis options
     */
    public function __construct(
        public string $which = 'h',
        public ?string $title = null,
        public ?string $format = null,
        public ?float $minValue = null,
        public ?float $maxValue = null,
        public ?int $gridlines = null,
        public ?string $gridlineColor = null,
        public ?int $minorGridlines = null,
        public ?string $baseline = null,
        public ?string $baselineColor = null,
        public ?string $textPosition = null,
        public ?array $textStyle = null,
        public ?array $titleTextStyle = null,
        public ?bool $logScale = null,
        public ?string $direction = null,
        public ?array $viewWindow = null,
        public ?array $ticks = null,
        public ?bool $slantedText = null,
        public ?int $slantedTextAngle = null,
        public ?int $maxTextLines = null,
        public ?array $extra = null,
    ) {
        $this->axisConfig = $this->buildConfig();
    }

    /**
     * Build the axis configuration array from non-null props.
     *
     * @return array<string, mixed>
     */
    protected function buildConfig(): array
    {
        $config = ['which' => $this->which];

        if ($this->title !== null) {
            $config['title'] = $this->title;
        }
        if ($this->format !== null) {
            $config['format'] = $this->format;
        }
        if ($this->minValue !== null) {
            $config['minValue'] = $this->minValue;
        }
        if ($this->maxValue !== null) {
            $config['maxValue'] = $this->maxValue;
        }
        if ($this->gridlines !== null) {
            $config['gridlines'] = ['count' => $this->gridlines];
        }
        if ($this->gridlineColor !== null) {
            $config['gridlines'] = array_merge($config['gridlines'] ?? [], ['color' => $this->gridlineColor]);
        }
        if ($this->minorGridlines !== null) {
            $config['minorGridlines'] = ['count' => $this->minorGridlines];
        }
        if ($this->baseline !== null) {
            $config['baseline'] = $this->baseline;
        }
        if ($this->baselineColor !== null) {
            $config['baselineColor'] = $this->baselineColor;
        }
        if ($this->textPosition !== null) {
            $config['textPosition'] = $this->textPosition;
        }
        if ($this->textStyle !== null) {
            $config['textStyle'] = $this->textStyle;
        }
        if ($this->titleTextStyle !== null) {
            $config['titleTextStyle'] = $this->titleTextStyle;
        }
        if ($this->logScale !== null) {
            $config['logScale'] = $this->logScale;
        }
        if ($this->direction !== null) {
            $config['direction'] = (int) $this->direction;
        }
        if ($this->viewWindow !== null) {
            $config['viewWindow'] = $this->viewWindow;
        }
        if ($this->ticks !== null) {
            $config['ticks'] = $this->ticks;
        }
        if ($this->slantedText !== null) {
            $config['slantedText'] = $this->slantedText;
        }
        if ($this->slantedTextAngle !== null) {
            $config['slantedTextAngle'] = $this->slantedTextAngle;
        }
        if ($this->maxTextLines !== null) {
            $config['maxTextLines'] = $this->maxTextLines;
        }

        if ($this->extra !== null) {
            $config = array_merge($config, $this->extra);
        }

        return $config;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('google-chart::components.axis');
    }
}
