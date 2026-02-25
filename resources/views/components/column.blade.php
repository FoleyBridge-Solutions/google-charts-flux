{{-- Column sub-component: renders a hidden template with column definition --}}
{{-- The parent <x-google-chart> Alpine component reads this during init --}}
<template data-gcf-column="{{ json_encode($toArray()) }}"></template>
