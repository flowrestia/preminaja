@props([
    'type' => 'card', // card, text, row
    'count' => 1,
])

@if($type === 'card')
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 animate-pulse">
        @for($i = 0; $i < $count; $i++)
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden p-4 space-y-4 shadow-sm">
                <div class="aspect-video bg-slate-100 rounded-xl"></div>
                <div class="space-y-2">
                    <div class="h-4 bg-slate-100 rounded w-3/4"></div>
                    <div class="h-3 bg-slate-100 rounded w-1/2"></div>
                </div>
                <div class="pt-3 border-t border-slate-100 flex justify-between items-center">
                    <div class="h-5 bg-slate-100 rounded w-1/3"></div>
                    <div class="h-7 bg-slate-100 rounded-lg w-16"></div>
                </div>
            </div>
        @endfor
    </div>
@elseif($type === 'row')
    <div class="space-y-3 animate-pulse">
        @for($i = 0; $i < $count; $i++)
            <div class="h-14 bg-white border border-slate-200 rounded-xl shadow-sm"></div>
        @endfor
    </div>
@else
    <div class="space-y-2 animate-pulse">
        @for($i = 0; $i < $count; $i++)
            <div class="h-4 bg-slate-100 rounded w-full"></div>
        @endfor
    </div>
@endif
