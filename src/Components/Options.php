<?php

declare(strict_types=1);

namespace FoleyBridgeSolutions\GoogleChartsFlux\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Chart options sub-component.
 *
 * Passes configuration options to the parent <x-google-chart> component.
 * All props are forwarded as Google Charts options.
 *
 * Usage:
 *   <x-google-chart.options
 *       title="Revenue"
 *       curveType="function"
 *       :pieHole="0.4"
 *       :colors="['#4285F4', '#EA4335', '#FBBC05']"
 *       :legend="['position' => 'bottom']"
 *   />
 *
 * Any prop not explicitly consumed by this component is passed through
 * as a Google Charts option key-value pair.
 */
class Options extends Component
{
    /**
     * The options array built from component props.
     *
     * @var array<string, mixed>
     */
    public array $chartOptions;

    /**
     * Create a new options component instance.
     *
     * All named parameters are treated as Google Charts options.
     * Use camelCase prop names (e.g. pieHole, curveType, fontName).
     *
     * @param string|null $title         Chart title
     * @param string|null $curveType     Line curve type ('function' for smooth)
     * @param float|null $pieHole        Donut hole size (0-1)
     * @param array|null $colors         Array of color hex strings
     * @param array|string|null $legend  Legend config array or position string
     * @param bool|null $is3D            Enable 3D rendering
     * @param array|null $chartArea      Chart area dimensions
     * @param string|null $fontName      Font family name
     * @param int|null $fontSize         Base font size
     * @param array|null $animation      Animation config
     * @param array|null $tooltip        Tooltip config
     * @param string|null $backgroundColor Background color
     * @param array|null $extra          Additional options to merge (catch-all)
     */
    public function __construct(
        public ?string $title = null,
        public ?string $curveType = null,
        public ?float $pieHole = null,
        public ?array $colors = null,
        public array|string|null $legend = null,
        public ?bool $is3D = null,
        public ?array $chartArea = null,
        public ?string $fontName = null,
        public ?int $fontSize = null,
        public ?array $animation = null,
        public ?array $tooltip = null,
        public ?string $backgroundColor = null,
        public ?array $extra = null,
    ) {
        $this->chartOptions = $this->buildOptions();
    }

    /**
     * Build the options array from non-null props.
     *
     * @return array<string, mixed>
     */
    protected function buildOptions(): array
    {
        $options = [];

        if ($this->title !== null) {
            $options['title'] = $this->title;
        }
        if ($this->curveType !== null) {
            $options['curveType'] = $this->curveType;
        }
        if ($this->pieHole !== null) {
            $options['pieHole'] = $this->pieHole;
        }
        if ($this->colors !== null) {
            $options['colors'] = $this->colors;
        }
        if ($this->legend !== null) {
            $options['legend'] = is_string($this->legend)
                ? ['position' => $this->legend]
                : $this->legend;
        }
        if ($this->is3D !== null) {
            $options['is3D'] = $this->is3D;
        }
        if ($this->chartArea !== null) {
            $options['chartArea'] = $this->chartArea;
        }
        if ($this->fontName !== null) {
            $options['fontName'] = $this->fontName;
        }
        if ($this->fontSize !== null) {
            $options['fontSize'] = $this->fontSize;
        }
        if ($this->animation !== null) {
            $options['animation'] = $this->animation;
        }
        if ($this->tooltip !== null) {
            $options['tooltip'] = $this->tooltip;
        }
        if ($this->backgroundColor !== null) {
            $options['backgroundColor'] = $this->backgroundColor;
        }

        // Merge any extra options
        if ($this->extra !== null) {
            $options = array_merge($options, $this->extra);
        }

        return $options;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('google-chart::components.options');
    }
}
