@props([
    'variant' => 'primary', // primary, secondary, danger, ghost
    'size' => 'md', // sm, md, lg
    'type' => 'button',
    'href' => null,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-semibold rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white disabled:opacity-50 disabled:pointer-events-none active:scale-[0.98] select-none gap-2';

    $sizeClasses = [
        'sm' => 'text-xs px-3 py-1.5',
        'md' => 'text-sm px-4 py-2.5',
        'lg' => 'text-base px-6 py-3',
    ][$size] ?? 'text-sm px-4 py-2.5';

    $variantClasses = [
        'primary' => 'bg-indigo-600 hover:bg-indigo-500 text-white shadow-md shadow-indigo-600/20 hover:shadow-indigo-600/30 focus:ring-indigo-500 border border-transparent',
        'secondary' => 'bg-white hover:bg-slate-50 text-slate-700 hover:text-slate-900 border border-slate-300 focus:ring-slate-400 shadow-sm',
        'danger' => 'bg-rose-600 hover:bg-rose-500 text-white shadow-md shadow-rose-600/20 focus:ring-rose-500 border border-transparent',
        'ghost' => 'bg-transparent hover:bg-slate-100 text-slate-600 hover:text-slate-900 focus:ring-slate-300',
    ][$variant] ?? 'bg-indigo-600 hover:bg-indigo-500 text-white shadow-md shadow-indigo-600/20 focus:ring-indigo-500';

    $classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
