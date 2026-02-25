{{-- Row sub-component: renders a hidden template with row values --}}
{{-- The parent <x-google-chart> Alpine component reads this during init --}}
<template data-gcf-row="{{ json_encode($values) }}"></template>
