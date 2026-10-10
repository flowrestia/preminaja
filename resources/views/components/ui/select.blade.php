@props([
    'label' => null,
    'id' => null,
    'error' => null,
    'hint' => null,
    'options' => [],
])

@php
    $id = $id ?? ($attributes->whereStartsWith('wire:model')->first() ?? $attributes->get('name') ?? uniqid('select_'));
@endphp

<div class="w-full space-y-1.5">
    @if($label)
        <label for="{{ $id }}" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
            {{ $label }}
        </label>
    @endif

    <div class="relative">
        <select
            id="{{ $id }}"
            {{ $attributes->merge([
                'class' => 'w-full px-4 py-2.5 bg-slate-900/90 border ' .
                    ($error ? 'border-rose-500/80 focus:ring-rose-500/50 ' : 'border-slate-800 focus:border-indigo-500 focus:ring-indigo-500/40 ') .
                    'rounded-xl text-sm text-white transition duration-150 focus:outline-none focus:ring-2 shadow-inner disabled:opacity-50 appearance-none pr-10'
            ]) }}
        >
            {{ $slot }}
        </select>
        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </div>

    @if($error)
        <p class="text-xs text-rose-400 font-medium flex items-center gap-1 mt-1">
            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            {{ $error }}
        </p>
    @elseif($hint)
        <p class="text-xs text-slate-400 mt-1">{{ $hint }}</p>
    @endif
</div>
