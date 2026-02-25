<?php

declare(strict_types=1);

namespace FoleyBridgeSolutions\GoogleChartsFlux\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Chart series sub-component.
 *
 * Configures individual data series appearance and behavior.
 * Maps to the Google Charts `series` option.
 *
 * Usage:
 *   <x-google-chart type="combo" :data="$data">
 *       <x-google-chart.series :index="0" type="bars" color="#4285F4" />
 *       <x-google-chart.series :index="1" type="line" color="#EA4335" :lineWidth="3" />
 *       <x-google-chart.series :index="2" type="area" color="#FBBC05" :areaOpacity="0.3" />
 *   </x-google-chart>
 *
 * @see https://developers.google.com/chart/interactive/docs/gallery/combochart#series-option
 */
class Series extends Component
{
    /**
     * The series configuration as an array.
     *
     * @var array<string, mixed>
     */
    public array $seriesConfig;

    /**
     * Create a new series component instance.
     *
     * @param int $index            The zero-based series index
     * @param string|null $type     Series chart type ('line', 'bars', 'area', 'steppedArea')
     * @param string|null $color    Series color (hex, rgb, or named color)
     * @param int|null $lineWidth   Line width in pixels
     * @param string|null $lineDashStyle Dash pattern (e.g. '4,4' for dashed)
     * @param string|null $pointShape Point marker shape ('circle', 'triangle', 'square', 'diamond', 'star', 'polygon')
     * @param int|null $pointSize   Point marker size in pixels
     * @param float|null $areaOpacity Area fill opacity (0-1)
     * @param string|null $curveType Curve type ('function' for smooth)
     * @param int|null $targetAxisIndex Target axis (0=left, 1=right)
     * @param bool|null $visibleInLegend Show in legend
     * @param array|null $extra     Additional series options
     */
    public function __construct(
        public int $index = 0,
        public ?string $type = null,
        public ?string $color = null,
        public ?int $lineWidth = null,
        public ?string $lineDashStyle = null,
        public ?string $pointShape = null,
        public ?int $pointSize = null,
        public ?float $areaOpacity = null,
        public ?string $curveType = null,
        public ?int $targetAxisIndex = null,
        public ?bool $visibleInLegend = null,
        public ?array $extra = null,
    ) {
        $this->seriesConfig = $this->buildConfig();
    }

    /**
     * Build the series configuration array from non-null props.
     *
     * @return array<string, mixed>
     */
    protected function buildConfig(): array
    {
        $config = ['index' => $this->index];

        if ($this->type !== null) {
            $config['type'] = $this->type;
        }
        if ($this->color !== null) {
            $config['color'] = $this->color;
        }
        if ($this->lineWidth !== null) {
            $config['lineWidth'] = $this->lineWidth;
        }
        if ($this->lineDashStyle !== null) {
            $config['lineDashStyle'] = array_map('intval', explode(',', $this->lineDashStyle));
        }
        if ($this->pointShape !== null) {
            $config['pointShape'] = $this->pointShape;
        }
        if ($this->pointSize !== null) {
            $config['pointSize'] = $this->pointSize;
        }
        if ($this->areaOpacity !== null) {
            $config['areaOpacity'] = $this->areaOpacity;
        }
        if ($this->curveType !== null) {
            $config['curveType'] = $this->curveType;
        }
        if ($this->targetAxisIndex !== null) {
            $config['targetAxisIndex'] = $this->targetAxisIndex;
        }
        if ($this->visibleInLegend !== null) {
            $config['visibleInLegend'] = $this->visibleInLegend;
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
        return view('google-chart::components.series');
    }
}
