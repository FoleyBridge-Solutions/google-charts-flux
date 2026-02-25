{{-- Series sub-component: renders a hidden template with series configuration --}}
{{-- The parent <x-google-chart> Alpine component reads this during init --}}
<template data-gcf-series="{{ json_encode($seriesConfig) }}"></template>
