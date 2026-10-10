@props([
    'label' => null,
    'id' => null,
    'error' => null,
    'hint' => null,
    'type' => 'text',
])

@php
    $id = $id ?? ($attributes->whereStartsWith('wire:model')->first() ?? $attributes->get('name') ?? uniqid('input_'));
@endphp

<div class="w-full space-y-1.5">
    @if($label)
        <label for="{{ $id }}" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
            {{ $label }}
        </label>
    @endif

    <div class="relative">
        <input
            id="{{ $id }}"
            type="{{ $type }}"
            {{ $attributes->merge([
                'class' => 'w-full px-4 py-2.5 bg-slate-900/90 border ' .
                    ($error ? 'border-rose-500/80 focus:ring-rose-500/50 focus:border-rose-500 ' : 'border-slate-800 focus:border-indigo-500 focus:ring-indigo-500/40 ') .
                    'rounded-xl text-sm text-white placeholder-slate-500 transition duration-150 focus:outline-none focus:ring-2 shadow-inner disabled:opacity-50 disabled:bg-slate-950'
            ]) }}
        />
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
