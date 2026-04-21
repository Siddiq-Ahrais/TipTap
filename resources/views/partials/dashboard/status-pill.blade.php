@php
    $tone = $tone ?? 'slate';

    $pillClasses = [
        'emerald' => 'border-teal-primary/30 bg-teal-primary/10 text-teal-primary',
        'rose' => 'border-coral-accent/30 bg-coral-accent/10 text-coral-accent',
        'slate' => 'border-dark-slate/25 bg-dark-slate/10 text-dark-slate',
        'amber' => 'border-mustard-caution/35 bg-mustard-caution/10 text-mustard-caution',
    ][$tone] ?? 'border-dark-slate/25 bg-dark-slate/10 text-dark-slate';

    $dotClasses = [
        'emerald' => 'bg-teal-primary',
        'rose' => 'bg-coral-accent',
        'slate' => 'bg-dark-slate',
        'amber' => 'bg-mustard-caution',
    ][$tone] ?? 'bg-dark-slate';
@endphp

<span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em] {{ $pillClasses }}">
    <span class="h-2 w-2 rounded-full {{ $dotClasses }}"></span>
    {{ $label }}
</span>
