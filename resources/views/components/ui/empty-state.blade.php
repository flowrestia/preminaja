@props([
    'title' => 'Tidak Ada Data',
    'description' => null,
    'actionLabel' => null,
    'actionHref' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center p-8 sm:p-12 text-center rounded-2xl bg-slate-900/60 border border-slate-800/80']) }}>
    <div class="w-16 h-16 rounded-2xl bg-slate-800/80 border border-slate-700/60 flex items-center justify-center text-slate-400 mb-4 shadow-inner">
        {{ $icon ?? '' }}
    </div>

    <h3 class="text-base sm:text-lg font-bold text-white">{{ $title }}</h3>

    @if($description)
        <p class="text-xs sm:text-sm text-slate-400 max-w-sm mt-1.5 leading-relaxed">{{ $description }}</p>
    @endif

    @if($actionLabel && $actionHref)
        <div class="mt-6">
            <x-ui.button :href="$actionHref" variant="primary" size="sm">
                {{ $actionLabel }}
            </x-ui.button>
        </div>
    @elseif(isset($action))
        <div class="mt-6">
            {{ $action }}
        </div>
    @endif
</div>
