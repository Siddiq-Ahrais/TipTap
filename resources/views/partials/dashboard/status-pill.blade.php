@php
    $tone = $tone ?? 'slate';

    $pillClasses = [
        'emerald' => 'border-emerald-200 bg-emerald-100 text-emerald-700',
        'rose' => 'border-rose-200 bg-rose-100 text-rose-700',
        'slate' => 'border-slate-200 bg-slate-100 text-slate-700',
        'amber' => 'border-amber-200 bg-amber-100 text-amber-700',
    ][$tone] ?? 'border-slate-200 bg-slate-100 text-slate-700';

    $dotClasses = [
        'emerald' => 'bg-emerald-500',
        'rose' => 'bg-rose-500',
        'slate' => 'bg-slate-500',
        'amber' => 'bg-amber-500',
    ][$tone] ?? 'bg-slate-500';
@endphp

<span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em] {{ $pillClasses }}">
    <span class="h-2 w-2 rounded-full {{ $dotClasses }}"></span>
    {{ $label }}
</span>
