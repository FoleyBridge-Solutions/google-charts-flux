{{-- Axis sub-component: renders a hidden template with axis configuration --}}
{{-- The parent <x-google-chart> Alpine component reads this during init --}}
<template data-gcf-axis="{{ json_encode($axisConfig) }}"></template>
