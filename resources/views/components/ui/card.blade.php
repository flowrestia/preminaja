@props([
    'padding' => 'p-6',
    'hover' => false,
])

<div {{ $attributes->merge([
    'class' => 'bg-white border border-slate-200/90 rounded-2xl overflow-hidden shadow-sm ' .
        ($hover ? 'hover:border-indigo-300 hover:shadow-md transition-all duration-300 ' : '') .
        $padding
]) }}>
    {{ $slot }}
</div>
