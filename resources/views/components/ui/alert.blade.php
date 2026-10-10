@props([
    'variant' => 'info', // info, success, warning, danger
    'title' => null,
])

@php
    $styles = [
        'info' => [
            'box' => 'bg-indigo-50 border-indigo-200 text-indigo-900',
            'title' => 'text-indigo-950 font-bold'
        ],
        'success' => [
            'box' => 'bg-emerald-50 border-emerald-200 text-emerald-900',
            'title' => 'text-emerald-950 font-bold'
        ],
        'warning' => [
            'box' => 'bg-amber-50 border-amber-200 text-amber-900',
            'title' => 'text-amber-950 font-bold'
        ],
        'danger' => [
            'box' => 'bg-rose-50 border-rose-200 text-rose-900',
            'title' => 'text-rose-950 font-bold'
        ],
    ][$variant] ?? [
        'box' => 'bg-slate-50 border-slate-200 text-slate-800',
        'title' => 'text-slate-900 font-bold'
    ];
@endphp

<div {{ $attributes->merge(['class' => 'flex items-start gap-3.5 p-4 rounded-xl border ' . $styles['box']]) }} role="alert">
    <div class="shrink-0 mt-0.5">
        @if($variant === 'success')
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        @elseif($variant === 'warning')
            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
        @elseif($variant === 'danger')
            <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        @else
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        @endif
    </div>
    <div class="flex-1 text-sm leading-relaxed">
        @if($title)
            <h4 class="font-semibold {{ $styles['title'] }} mb-0.5">{{ $title }}</h4>
        @endif
        {{ $slot }}
    </div>
</div>
