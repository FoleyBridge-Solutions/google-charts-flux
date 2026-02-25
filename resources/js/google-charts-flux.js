/**
 * Google Charts Flux — Alpine.js component for rendering Google Charts
 * with Livewire reactivity, event bridging, and Flux UI dark mode support.
 *
 * @package foleybridgesolutions/google-charts-flux
 */
(function () {
    'use strict';

    // =========================================================================
    // Google Charts Loader Manager (singleton)
    // =========================================================================

    const GoogleChartsLoader = {
        _promise: null,
        _scriptLoaded: false,

        /**
         * Load the Google Charts library and requested packages.
         * Returns a promise that resolves when charts are ready to use.
         *
         * @param {Object} config - { version, packages, apiKey, language }
         * @returns {Promise<void>}
         */
        load(config) {
            if (this._promise) {
                return this._promise;
            }

            this._promise = new Promise((resolve, reject) => {
                const onScriptReady = () => {
                    const loadOptions = {
                        packages: config.packages || ['corechart'],
                    };

                    if (config.apiKey) {
                        loadOptions.mapsApiKey = config.apiKey;
                    }

                    if (config.language) {
                        loadOptions.language = config.language;
                    }

                    try {
                        google.charts.load(config.version || 'current', loadOptions);
                        google.charts.setOnLoadCallback(() => resolve());
                    } catch (e) {
                        reject(e);
                    }
                };

                // If google.charts already exists (script already loaded)
                if (typeof google !== 'undefined' && google.charts) {
                    onScriptReady();
                    return;
                }

                // If the script tag is already in the DOM but hasn't loaded yet
                if (this._scriptLoaded) {
                    const interval = setInterval(() => {
                        if (typeof google !== 'undefined' && google.charts) {
                            clearInterval(interval);
                            onScriptReady();
                        }
                    }, 50);
                    return;
                }

                // Load the script
                this._scriptLoaded = true;
                const script = document.createElement('script');
                script.src = 'https://www.gstatic.com/charts/loader.js';
                script.async = true;
                script.onload = onScriptReady;
                script.onerror = () => reject(new Error('Failed to load Google Charts library'));
                document.head.appendChild(script);
            });

            return this._promise;
        },
    };

    // =========================================================================
    // Chart Type → Google Visualization Class Mapping
    // =========================================================================

    const CHART_CLASS_MAP = {
        'annotation':    'AnnotationChart',
        'area':          'AreaChart',
        'bar':           'BarChart',
        'bubble':        'BubbleChart',
        'calendar':      'Calendar',
        'candlestick':   'CandlestickChart',
        'column':        'ColumnChart',
        'combo':         'ComboChart',
        'donut':         'PieChart',
        'gantt':         'Gantt',
        'gauge':         'Gauge',
        'geo':           'GeoChart',
        'histogram':     'Histogram',
        'line':          'LineChart',
        'map':           'Map',
        'org':           'OrgChart',
        'pie':           'PieChart',
        'sankey':        'Sankey',
        'scatter':       'ScatterChart',
        'stepped-area':  'SteppedAreaChart',
        'table':         'Table',
        'timeline':      'Timeline',
        'treemap':       'TreeMap',
        'wordtree':      'WordTree',
    };

    // =========================================================================
    // Chart Types Supporting HTML Tooltips
    // =========================================================================

    /**
     * Chart types that support HTML tooltips via { tooltip: { isHtml: true } }.
     *
     * @type {Set<string>}
     */
    const HTML_TOOLTIP_TYPES = new Set([
        'area', 'bar', 'calendar', 'candlestick', 'column', 'combo',
        'line', 'pie', 'donut', 'sankey', 'scatter', 'timeline',
    ]);

    // =========================================================================
    // Utility Functions
    // =========================================================================

    /**
     * Deep merge two objects. Source values override target values.
     * Arrays are replaced, not concatenated.
     *
     * @param {Object} target
     * @param {Object} source
     * @returns {Object}
     */
    function deepMerge(target, source) {
        const output = { ...target };
        for (const key of Object.keys(source)) {
            if (
                source[key] &&
                typeof source[key] === 'object' &&
                !Array.isArray(source[key]) &&
                target[key] &&
                typeof target[key] === 'object' &&
                !Array.isArray(target[key])
            ) {
                output[key] = deepMerge(target[key], source[key]);
            } else {
                output[key] = source[key];
            }
        }
        return output;
    }

    /**
     * Detect if dark mode is currently active.
     * Checks Flux UI's system first, then falls back to DOM class check.
     *
     * @returns {boolean}
     */
    function isDarkMode() {
        // Flux UI JS API
        if (typeof Flux !== 'undefined' && typeof Flux.dark === 'boolean') {
            return Flux.dark;
        }
        // Fallback: check for .dark class on <html> or <body>
        return document.documentElement.classList.contains('dark') ||
               document.body.classList.contains('dark');
    }

    /**
     * Build a Google DataTable from an array-of-arrays format.
     *
     * @param {Array[]} data - First row is headers, rest are data rows
     * @returns {google.visualization.DataTable}
     */
    function arrayToDataTable(data) {
        return google.visualization.arrayToDataTable(data);
    }

    /**
     * Build a Google DataTable from declarative columns and rows.
     *
     * @param {Array<{type: string, label: string, role?: string}>} columns
     * @param {Array<Array>} rows
     * @returns {google.visualization.DataTable}
     */
    function columnsRowsToDataTable(columns, rows) {
        const dataTable = new google.visualization.DataTable();
        for (const col of columns) {
            if (col.role) {
                dataTable.addColumn({ type: col.type, role: col.role, label: col.label || '' });
            } else {
                dataTable.addColumn(col.type, col.label || '');
            }
        }
        if (rows.length > 0) {
            dataTable.addRows(rows);
        }
        return dataTable;
    }

    /**
     * Escape a string for safe insertion into HTML.
     *
     * @param {string} str - Raw string to escape
     * @returns {string} HTML-escaped string
     */
    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // =========================================================================
    // Alpine.js Component: googleChart
    // =========================================================================

    /**
     * Alpine.js data component for rendering and managing a Google Chart.
     *
     * @param {Object} config
     * @param {string} config.type          - Chart type (e.g. 'pie', 'bar', 'line')
     * @param {Array|null} config.data      - Chart data as array-of-arrays
     * @param {Object} config.options       - Chart-specific options
     * @param {Object} config.defaults      - Default options from config
     * @param {Object} config.darkOptions   - Dark mode option overrides
     * @param {Array} config.events         - Event listeners [{on, emit}]
     * @param {Array} config.columns        - Declarative column definitions
     * @param {Array} config.rows           - Declarative row data
     * @param {Array} config.seriesConfig   - Series configuration [{index, ...props}]
     * @param {Array} config.axisConfig     - Axis configuration [{which, ...props}]
     * @param {Object} config.loaderConfig  - Google Charts loader config
     * @param {string} config.wireModelProp - Livewire property name for wire:model
     * @param {string} config.loading       - Loading display type: 'skeleton', 'spinner', 'none'
     * @param {Object} [config.otherBreakdown]         - "Other" slice breakdown data for tooltip mini-chart
     * @param {string} config.otherBreakdown.label      - Label of the "Other" row (must match a DataTable row label)
     * @param {Array<[string, number]>} config.otherBreakdown.items - Breakdown items as [label, value] pairs
     * @returns {Object} Alpine.js component data
     */
    function googleChartComponent(config) {
        return {
            chart: null,
            dataTable: null,
            ready: false,
            error: null,
            _resizeObserver: null,
            _darkModeObserver: null,
            _drawTimeout: null,

            /**
             * Initialize the chart: parse sub-components, load Google Charts,
             * build data, and render.
             *
             * Called automatically by Alpine (x-data auto-init). Do NOT call
             * from x-init — Alpine handles this.
             */
            async init() {
                try {
                    // Parse declarative sub-component <template> elements
                    // before loading the chart library or building data.
                    this.parseSubComponents();

                    await GoogleChartsLoader.load(config.loaderConfig);
                    this.buildDataTable();
                    await this.injectOtherTooltip();
                    this.createChart();
                    this.draw();
                    this.setupResize();
                    this.setupDarkMode();
                    this.setupWireModel();
                    this.ready = true;
                } catch (e) {
                    this.error = e.message;
                    console.error('[GoogleChartsFlux] Initialization error:', e);
                }
            },

            /**
             * Parse child <template> elements rendered by sub-components
             * (options, column, row, event, series, axis) and merge their
             * configuration into the config closure.
             */
            parseSubComponents() {
                const el = this.$el;

                el.querySelectorAll('template[data-gcf-options]').forEach(t => {
                    const parsed = JSON.parse(t.dataset.gcfOptions);
                    config.options = { ...config.options, ...parsed };
                });

                el.querySelectorAll('template[data-gcf-column]').forEach(t => {
                    config.columns.push(JSON.parse(t.dataset.gcfColumn));
                });

                el.querySelectorAll('template[data-gcf-row]').forEach(t => {
                    config.rows.push(JSON.parse(t.dataset.gcfRow));
                });

                el.querySelectorAll('template[data-gcf-event]').forEach(t => {
                    config.events.push(JSON.parse(t.dataset.gcfEvent));
                });

                el.querySelectorAll('template[data-gcf-series]').forEach(t => {
                    config.seriesConfig.push(JSON.parse(t.dataset.gcfSeries));
                });

                el.querySelectorAll('template[data-gcf-axis]').forEach(t => {
                    config.axisConfig.push(JSON.parse(t.dataset.gcfAxis));
                });
            },

            /**
             * Build the DataTable from either array data or declarative columns/rows.
             */
            buildDataTable() {
                const data = this.resolveData();
                if (data && Array.isArray(data) && data.length > 0) {
                    this.dataTable = arrayToDataTable(data);
                } else if (config.columns && config.columns.length > 0) {
                    this.dataTable = columnsRowsToDataTable(config.columns, config.rows || []);
                } else {
                    throw new Error('No chart data provided. Use :data, wire:model, or declarative <x-google-chart.column> / <x-google-chart.row> components.');
                }
            },

            /**
             * Inject an HTML tooltip with a mini PieChart breakdown for the
             * "Other" slice. Renders a hidden chart, captures it as PNG via
             * getImageURI(), and adds a tooltip role column to the DataTable.
             *
             * Requires config.otherBreakdown to be set with shape:
             *   { label: string, items: Array<[string, number]> }
             *
             * No-op if otherBreakdown is absent, has no items, or the chart
             * type does not support HTML tooltips.
             */
            async injectOtherTooltip() {
                const breakdown = config.otherBreakdown;
                if (!breakdown?.items?.length || !HTML_TOOLTIP_TYPES.has(config.type)) {
                    return;
                }

                // Create an off-screen container for the mini chart render
                const hiddenDiv = document.createElement('div');
                hiddenDiv.style.cssText = 'position:fixed;left:-9999px;top:-9999px;width:300px;height:200px;';
                document.body.appendChild(hiddenDiv);

                try {
                    // Build a DataTable for the mini pie chart
                    const miniData = new google.visualization.DataTable();
                    miniData.addColumn('string', 'Label');
                    miniData.addColumn('number', 'Value');
                    miniData.addRows(breakdown.items);

                    // Render the mini chart and capture it as a PNG data URI
                    const miniChart = new google.visualization.PieChart(hiddenDiv);
                    const pngUri = await new Promise((resolve, reject) => {
                        google.visualization.events.addOneTimeListener(miniChart, 'ready', () => {
                            resolve(miniChart.getImageURI());
                        });
                        google.visualization.events.addOneTimeListener(miniChart, 'error', (err) => {
                            reject(new Error(err.message || 'Mini chart render failed'));
                        });
                        miniChart.draw(miniData, {
                            title: '',
                            legend: { position: 'labeled' },
                            pieSliceText: 'percentage',
                            chartArea: { width: '90%', height: '90%' },
                            backgroundColor: 'transparent',
                            enableInteractivity: false,
                            pieSliceTextStyle: { fontSize: 10 },
                            width: 300,
                            height: 200,
                        });
                    });

                    miniChart.clearChart();

                    // Build the HTML tooltip content with the PNG and item list
                    let tooltipHtml = '<div style="padding:8px;min-width:280px;">';
                    tooltipHtml += '<div style="font-weight:bold;margin-bottom:6px;">'
                        + escapeHtml(breakdown.label) + ' Breakdown</div>';
                    tooltipHtml += '<img src="' + pngUri
                        + '" style="width:280px;height:auto;display:block;margin-bottom:6px;" />';
                    tooltipHtml += '<div style="font-size:12px;line-height:1.4;">';

                    const breakdownTotal = breakdown.items.reduce((sum, item) => sum + item[1], 0);
                    for (const item of breakdown.items) {
                        const pct = breakdownTotal > 0
                            ? ((item[1] / breakdownTotal) * 100).toFixed(1)
                            : '0.0';
                        tooltipHtml += '<div>' + escapeHtml(String(item[0]))
                            + ': ' + escapeHtml(String(item[1]))
                            + ' (' + pct + '%)</div>';
                    }

                    tooltipHtml += '</div></div>';

                    // Add a tooltip role column and populate only the "Other" row
                    this.dataTable.addColumn({ type: 'string', role: 'tooltip', p: { html: true } });
                    const tooltipColIndex = this.dataTable.getNumberOfColumns() - 1;
                    const numRows = this.dataTable.getNumberOfRows();

                    for (let r = 0; r < numRows; r++) {
                        const label = this.dataTable.getValue(r, 0);
                        if (label === breakdown.label) {
                            this.dataTable.setCell(r, tooltipColIndex, tooltipHtml);
                        }
                        // null cells → Google Charts auto-generates default tooltip
                    }

                    // Enable HTML tooltips in chart options
                    config.options = config.options || {};
                    config.options.tooltip = Object.assign(config.options.tooltip || {}, { isHtml: true });

                } finally {
                    document.body.removeChild(hiddenDiv);
                }
            },

            /**
             * Resolve the current data source — from wire:model or static data prop.
             *
             * @returns {Array|null}
             */
            resolveData() {
                // If we have a wire:model binding, read from the Livewire component
                if (config.wireModelProp && this.$wire) {
                    const value = this.$wire.get(config.wireModelProp);
                    if (value) return value;
                }
                return config.data;
            },

            /**
             * Create the Google Charts visualization instance.
             */
            createChart() {
                const className = CHART_CLASS_MAP[config.type];
                if (!className) {
                    throw new Error(`Unknown chart type: "${config.type}". Valid types: ${Object.keys(CHART_CLASS_MAP).join(', ')}`);
                }

                const ChartClass = google.visualization[className];
                if (!ChartClass) {
                    throw new Error(`Google Charts class "${className}" not found. Ensure the required package is loaded in your google-charts-flux config.`);
                }

                this.chart = new ChartClass(this.$refs.canvas);
                this.registerEvents();
            },

            /**
             * Draw (or redraw) the chart with current data and merged options.
             */
            draw() {
                if (!this.chart || !this.dataTable) return;

                const mergedOptions = this.buildOptions();

                // Debounce rapid redraws (e.g., during resize)
                clearTimeout(this._drawTimeout);
                this._drawTimeout = setTimeout(() => {
                    try {
                        this.chart.draw(this.dataTable, mergedOptions);
                    } catch (e) {
                        this.error = e.message;
                        console.error('[GoogleChartsFlux] Draw error:', e);
                    }
                }, 10);
            },

            /**
             * Build the final options object by merging defaults, per-chart
             * options, series/axis config, and dark mode overrides.
             *
             * @returns {Object}
             */
            buildOptions() {
                let opts = deepMerge(config.defaults || {}, config.options || {});

                // Apply donut hole for 'donut' type
                if (config.type === 'donut' && opts.pieHole === undefined) {
                    opts.pieHole = 0.4;
                }

                // Merge series configuration
                if (config.seriesConfig && config.seriesConfig.length > 0) {
                    const series = {};
                    for (const s of config.seriesConfig) {
                        const idx = s.index;
                        const props = { ...s };
                        delete props.index;
                        series[idx] = props;
                    }
                    opts.series = deepMerge(opts.series || {}, series);
                }

                // Merge axis configuration
                if (config.axisConfig && config.axisConfig.length > 0) {
                    for (const a of config.axisConfig) {
                        const props = { ...a };
                        const which = props.which;
                        delete props.which;

                        const axisKey = which === 'h' ? 'hAxis' : 'vAxis';
                        opts[axisKey] = deepMerge(opts[axisKey] || {}, props);
                    }
                }

                // Apply dark mode overrides
                if (isDarkMode() && config.darkOptions) {
                    opts = deepMerge(opts, config.darkOptions);
                }

                return opts;
            },

            /**
             * Register Google Charts event listeners and bridge them
             * to Livewire dispatch calls.
             */
            registerEvents() {
                if (!config.events || config.events.length === 0) return;

                for (const evt of config.events) {
                    google.visualization.events.addListener(this.chart, evt.on, (e) => {
                        const payload = this.buildEventPayload(evt.on, e);

                        // Dispatch as Livewire event
                        if (this.$wire) {
                            this.$wire.dispatch(evt.emit, payload);
                        }

                        // Also dispatch as a DOM custom event for Alpine listeners
                        this.$el.dispatchEvent(new CustomEvent(evt.emit, {
                            detail: payload,
                            bubbles: true,
                        }));
                    });
                }
            },

            /**
             * Build a serializable payload from a Google Charts event.
             *
             * @param {string} eventName - The Google Charts event name
             * @param {*} eventData - The raw event data from Google Charts
             * @returns {Object}
             */
            buildEventPayload(eventName, eventData) {
                const payload = {
                    chartType: config.type,
                    event: eventName,
                };

                // For 'select' events, include the selected rows/columns/values
                if (eventName === 'select' && this.chart.getSelection) {
                    const selection = this.chart.getSelection();
                    payload.selection = selection;
                    payload.selectedData = selection.map((sel) => {
                        const row = {};
                        if (sel.row !== null && sel.row !== undefined && this.dataTable) {
                            const numCols = this.dataTable.getNumberOfColumns();
                            for (let c = 0; c < numCols; c++) {
                                const label = this.dataTable.getColumnLabel(c) || `col_${c}`;
                                row[label] = this.dataTable.getValue(sel.row, c);
                            }
                        }
                        row._row = sel.row;
                        row._column = sel.column;
                        return row;
                    });
                }

                // For 'ready' events
                if (eventName === 'ready') {
                    payload.ready = true;
                }

                // Pass through any extra event data
                if (eventData && typeof eventData === 'object') {
                    payload.raw = eventData;
                }

                return payload;
            },

            /**
             * Set up a ResizeObserver to redraw the chart when the container resizes.
             */
            setupResize() {
                this._resizeObserver = new ResizeObserver(() => {
                    this.draw();
                });
                this._resizeObserver.observe(this.$el);
            },

            /**
             * Watch for dark mode changes and redraw the chart.
             * Integrates with Flux UI's dark mode system.
             */
            setupDarkMode() {
                // Watch for Flux dark mode changes via MutationObserver on <html>
                this._darkModeObserver = new MutationObserver(() => {
                    this.draw();
                });
                this._darkModeObserver.observe(document.documentElement, {
                    attributes: true,
                    attributeFilter: ['class'],
                });

                // Also listen for Flux's custom appearance events if available
                document.addEventListener('flux:appearance', () => {
                    // Small delay to let Flux update the DOM class
                    setTimeout(() => this.draw(), 50);
                });
            },

            /**
             * Set up Livewire wire:model reactivity.
             * When the bound property changes, rebuild data, re-inject
             * tooltips, and redraw.
             */
            setupWireModel() {
                if (!config.wireModelProp || !this.$wire) return;

                this.$wire.$watch(config.wireModelProp, async (newValue) => {
                    if (newValue && Array.isArray(newValue) && newValue.length > 0) {
                        try {
                            this.dataTable = arrayToDataTable(newValue);
                            await this.injectOtherTooltip();
                            this.draw();
                        } catch (e) {
                            console.error('[GoogleChartsFlux] Data update error:', e);
                        }
                    }
                });
            },

            /**
             * Update the chart data programmatically (callable from Alpine/JS).
             * Re-injects "Other" tooltip if configured.
             *
             * @param {Array} newData - New data as array-of-arrays
             */
            async updateData(newData) {
                if (newData && Array.isArray(newData) && newData.length > 0) {
                    this.dataTable = arrayToDataTable(newData);
                    await this.injectOtherTooltip();
                    this.draw();
                }
            },

            /**
             * Update chart options and redraw.
             *
             * @param {Object} newOptions - Options to merge
             */
            updateOptions(newOptions) {
                config.options = deepMerge(config.options || {}, newOptions);
                this.draw();
            },

            /**
             * Get the chart as a PNG data URI (for charts that support it).
             *
             * @returns {string|null}
             */
            getImageURI() {
                if (this.chart && typeof this.chart.getImageURI === 'function') {
                    return this.chart.getImageURI();
                }
                return null;
            },

            /**
             * Clean up observers and chart instance.
             */
            destroy() {
                clearTimeout(this._drawTimeout);
                if (this._resizeObserver) {
                    this._resizeObserver.disconnect();
                }
                if (this._darkModeObserver) {
                    this._darkModeObserver.disconnect();
                }
                if (this.chart) {
                    google.visualization.events.removeAllListeners(this.chart);
                    this.chart.clearChart?.();
                }
            },
        };
    }

    // =========================================================================
    // Register Alpine Component
    // =========================================================================

    // Register when Alpine is available
    function registerComponent() {
        if (typeof Alpine === 'undefined') {
            // Alpine not loaded yet — wait for it
            document.addEventListener('alpine:init', () => {
                Alpine.data('googleChart', googleChartComponent);
            });
        } else {
            Alpine.data('googleChart', googleChartComponent);
        }
    }

    // Handle Livewire navigation (SPA mode with wire:navigate)
    // Charts need to be re-initialized when navigating between pages
    document.addEventListener('livewire:navigated', () => {
        // Alpine will automatically re-initialize x-data components
        // but we need to ensure the Google Charts loader is still valid
        GoogleChartsLoader._promise = null;
    });

    registerComponent();

})();
