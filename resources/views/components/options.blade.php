{{-- Options sub-component: renders a hidden template with JSON options --}}
{{-- The parent <x-google-chart> Alpine component reads this during init --}}
<template data-gcf-options="{{ json_encode($chartOptions) }}"></template>
