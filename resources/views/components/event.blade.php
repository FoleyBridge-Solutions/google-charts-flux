{{-- Event sub-component: renders a hidden template with event binding config --}}
{{-- The parent <x-google-chart> Alpine component reads this during init --}}
<template data-gcf-event="{{ json_encode($toArray()) }}"></template>
