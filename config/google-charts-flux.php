<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Google Maps API Key
    |--------------------------------------------------------------------------
    |
    | Required for GeoChart and Map chart types. Other chart types do not
    | need an API key. Get one from the Google Cloud Console:
    | https://console.cloud.google.com/apis/credentials
    |
    */

    'api_key' => env('GOOGLE_CHARTS_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Google Charts Version
    |--------------------------------------------------------------------------
    |
    | The version of Google Charts to load. Use 'current' for the latest
    | stable release, or pin a specific version number.
    |
    */

    'version' => env('GOOGLE_CHARTS_VERSION', 'current'),

    /*
    |--------------------------------------------------------------------------
    | Chart Packages
    |--------------------------------------------------------------------------
    |
    | The Google Charts packages to load. By default, all packages are loaded
    | so any chart type works out of the box. For better performance, you can
    | limit this to only the packages you actually use.
    |
    | Available packages: corechart, geochart, gauge, sankey, table, treemap,
    | orgchart, timeline, calendar, wordtree, annotationchart, gantt, map
    |
    */

    'packages' => [
        'corechart',
        'geochart',
        'gauge',
        'sankey',
        'table',
        'treemap',
        'orgchart',
        'timeline',
        'calendar',
        'wordtree',
        'annotationchart',
        'gantt',
        'map',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Chart Options
    |--------------------------------------------------------------------------
    |
    | These options are merged into every chart's options as defaults.
    | Per-chart options override these values.
    |
    */

    'defaults' => [
        'fontName' => 'Inter, system-ui, sans-serif',
        'backgroundColor' => 'transparent',
        'chartArea' => [
            'width' => '80%',
            'height' => '75%',
        ],
        'legend' => [
            'textStyle' => [
                'fontSize' => 13,
            ],
        ],
        'titleTextStyle' => [
            'fontSize' => 14,
            'bold' => true,
        ],
        'animation' => [
            'startup' => true,
            'duration' => 300,
            'easing' => 'out',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Dark Mode Options
    |--------------------------------------------------------------------------
    |
    | These options are merged on top of the defaults and per-chart options
    | when dark mode is active. This integrates with Flux UI's dark mode
    | system automatically.
    |
    */

    'dark' => [
        'backgroundColor' => 'transparent',
        'legend' => [
            'textStyle' => [
                'color' => '#d4d4d8',
            ],
        ],
        'titleTextStyle' => [
            'color' => '#fafafa',
        ],
        'hAxis' => [
            'textStyle' => [
                'color' => '#a1a1aa',
            ],
            'titleTextStyle' => [
                'color' => '#d4d4d8',
            ],
            'gridlines' => [
                'color' => '#3f3f46',
            ],
            'baselineColor' => '#52525b',
        ],
        'vAxis' => [
            'textStyle' => [
                'color' => '#a1a1aa',
            ],
            'titleTextStyle' => [
                'color' => '#d4d4d8',
            ],
            'gridlines' => [
                'color' => '#3f3f46',
            ],
            'baselineColor' => '#52525b',
        ],
        'tooltip' => [
            'textStyle' => [
                'color' => '#18181b',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Loading Placeholder
    |--------------------------------------------------------------------------
    |
    | Controls the loading state shown while Google Charts initializes.
    | Set to 'skeleton' to show a Flux UI skeleton, 'spinner' for a simple
    | CSS spinner, or 'none' to show nothing.
    |
    */

    'loading' => 'skeleton',

];
