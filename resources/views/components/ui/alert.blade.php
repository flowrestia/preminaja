@props([
    'variant' => 'info', // info, success, warning, danger
    'title' => null,
])

@php
    $styles = [
        'info' => [
            'box' => 'bg-indigo-950/40 border-indigo-800/50 text-indigo-200',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
            'title' => 'text-indigo-300'
        ],
        'success' => [
            'box' => 'bg-emerald-950/40 border-emerald-800/50 text-emerald-200',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
            'title' => 'text-emerald-300'
        ],
        'warning' => [
            'box' => 'bg-amber-950/40 border-amber-800/50 text-amber-200',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />',
            'title' => 'text-amber-300'
        ],
        'danger' => [
            'box' => 'bg-rose-950/40 border-rose-800/50 text-rose-200',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
            'title' => 'text-rose-300'
        ],
    ][$variant] ?? [
        'box' => 'bg-slate-900 border-slate-800 text-slate-300',
        'icon' => '',
        'title' => 'text-white'
    ];
@endphp

<div {{ $attributes->merge(['class' => 'flex items-start gap-3.5 p-4 rounded-xl border ' . $styles['box']]) }} role="alert">
    <div class="shrink-0 mt-0.5">
        @if($variant === 'success')
            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        @elseif($variant === 'warning')
            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
        @elseif($variant === 'danger')
            <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        @else
            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        @endif
    </div>
    <div class="flex-1 text-sm leading-relaxed">
        @if($title)
            <h4 class="font-semibold {{ $styles['title'] }} mb-0.5">{{ $title }}</h4>
        @endif
        {{ $slot }}
    </div>
</div>
