@props([
    'padding' => 'p-6',
    'hover' => false,
])

<div {{ $attributes->merge([
    'class' => 'bg-slate-900/80 backdrop-blur-sm border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl shadow-black/20 ' .
        ($hover ? 'hover:border-slate-700/80 hover:shadow-indigo-500/5 transition-all duration-300 ' : '') .
        $padding
]) }}>
    {{ $slot }}
</div>
