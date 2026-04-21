@php
    $trendDirection = $trendDirection ?? 'neutral';
    $trendColor = [
        'up' => 'text-emerald-600 bg-emerald-50 border-emerald-100',
        'down' => 'text-rose-600 bg-rose-50 border-rose-100',
        'neutral' => 'text-slate-500 bg-slate-100 border-slate-200',
    ][$trendDirection] ?? 'text-slate-500 bg-slate-100 border-slate-200';
@endphp

<div class="card-soft rounded-3xl p-5">
    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">{{ $label }}</p>
    <p class="mt-3 font-display text-3xl font-semibold text-slate-900">{{ $value }}</p>

    @if (!empty($caption))
        <p class="mt-1 text-sm text-slate-500">{{ $caption }}</p>
    @endif

    @if (!empty($trend))
        <span class="mt-3 inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $trendColor }}">
            {{ $trend }}
        </span>
    @endif
</div>
