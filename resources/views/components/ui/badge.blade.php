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
        'neutral' => 'bg-slate-800/80 text-slate-300 border-slate-700',
        'success' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/25',
        'warning' => 'bg-amber-500/10 text-amber-400 border-amber-500/25',
        'danger' => 'bg-rose-500/10 text-rose-400 border-rose-500/25',
        'brand' => 'bg-indigo-500/10 text-indigo-400 border-indigo-500/25',
    ][$variant] ?? 'bg-slate-800/80 text-slate-300 border-slate-700';

    $classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
