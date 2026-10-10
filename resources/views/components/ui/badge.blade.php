@props([
    'variant' => 'neutral', // neutral, success, warning, danger, brand
    'size' => 'sm', // xs, sm, md
])

@php
    $baseClasses = 'inline-flex items-center font-semibold rounded-full border tracking-wide uppercase';

    $sizeClasses = [
        'xs' => 'text-[9px] px-2 py-0.5',
        'sm' => 'text-[11px] px-2.5 py-0.5',
        'md' => 'text-xs px-3 py-1',
    ][$size] ?? 'text-[11px] px-2.5 py-0.5';

    $variantClasses = [
        'neutral' => 'bg-slate-100 text-slate-700 border-slate-200',
        'success' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'warning' => 'bg-amber-50 text-amber-700 border-amber-200',
        'danger' => 'bg-rose-50 text-rose-700 border-rose-200',
        'brand' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
    ][$variant] ?? 'bg-slate-100 text-slate-700 border-slate-200';

    $classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
