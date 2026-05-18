@php
    $tag = $tag ?? 'p';
    $labelClass = $labelClass ?? ($tag === 'dt' ? 'sim-rail-perf__term' : 'sim-kpiLabel');
@endphp
<{{ $tag }} class="{{ $labelClass }} sim-rail-kpi__labelRow" style="display:flex;align-items:center;flex-wrap:nowrap;gap:4px;max-width:100%;margin:0">
    <span style="flex:0 1 auto;min-width:0">{{ $text }}</span>
    @include('simulations.partials.section-help', [
        'tooltip' => $tooltip,
        'label' => $helpLabel,
    ])
</{{ $tag }}>
