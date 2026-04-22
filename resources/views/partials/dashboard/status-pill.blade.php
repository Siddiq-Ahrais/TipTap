@php
    $tone = $tone ?? 'slate';

    $pillClasses = [
        'emerald' => 'border-[#0B4A85]/30 bg-[#E7EFF6] text-[#063157]',
        'rose' => 'border-rose-300 bg-rose-50 text-rose-700',
        'slate' => 'border-dark-slate/25 bg-dark-slate/10 text-dark-slate',
        'amber' => 'border-[#0B4A85]/30 bg-[#E7EFF6] text-[#0B4A85]',
    ][$tone] ?? 'border-dark-slate/25 bg-dark-slate/10 text-dark-slate';

    $dotClasses = [
        'emerald' => 'bg-[#0B4A85]',
        'rose' => 'bg-rose-600',
        'slate' => 'bg-dark-slate',
        'amber' => 'bg-[#0B4A85]',
    ][$tone] ?? 'bg-dark-slate';
@endphp

<span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em] {{ $pillClasses }}">
    <span class="h-2 w-2 rounded-full {{ $dotClasses }}"></span>
    {{ $label }}
</span>
