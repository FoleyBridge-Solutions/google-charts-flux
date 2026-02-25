@php
    // Extract wire:model from attributes if present
    $wireModelProp = null;
    $wireAttributes = $attributes->whereStartsWith('wire:model')->getAttributes();
    foreach ($wireAttributes as $key => $value) {
        $wireModelProp = $value ?: $key;
        break;
    }

    $chartId = 'gcf-' . uniqid();
@endphp

<div
    id="{{ $chartId }}"
    x-data="googleChart({
        type: @js($type),
        data: @js($chartData),
        options: {},
        defaults: @js($defaultOptions()),
        darkOptions: @js($darkOptions()),
        events: [],
        columns: [],
        rows: [],
        seriesConfig: [],
        axisConfig: [],
        loaderConfig: @js($loaderConfig()),
        wireModelProp: @js($wireModelProp),
        loading: @js($loading),
    })"
    x-on:google-chart-update.window="
        if ($event.detail.chartId === '{{ $chartId }}') {
            updateData($event.detail.data);
        }
    "
    wire:ignore.self
    {{ $attributes->except(array_keys($wireAttributes))->merge(['class' => 'relative']) }}
>
    {{-- Sub-component slot (renders hidden <template> elements) --}}
    {{ $slot }}

    {{-- Loading placeholder --}}
    <div x-show="!ready && !error" x-cloak class="absolute inset-0 flex items-center justify-center">
        @if ($loading === 'skeleton')
            <div class="w-full h-full animate-pulse">
                <div class="w-full h-full rounded bg-zinc-200 dark:bg-zinc-700"></div>
            </div>
        @elseif ($loading === 'spinner')
            <div class="flex items-center justify-center">
                <svg class="h-8 w-8 animate-spin text-zinc-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        @endif
    </div>

    {{-- Error display --}}
    <div x-show="error" x-cloak class="absolute inset-0 flex items-center justify-center">
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-800 dark:bg-red-950 dark:text-red-400">
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <span x-text="error"></span>
            </div>
        </div>
    </div>

    {{-- Chart canvas --}}
    <div x-ref="canvas" class="h-full w-full" x-show="ready" x-transition.opacity.duration.300ms></div>
</div>
