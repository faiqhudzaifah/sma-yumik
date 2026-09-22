@props(['type' => 'card'])

@if($type === 'card')
    <div class="w-full rounded-[2rem] bg-white/40 dark:bg-white/5 border border-white/60 dark:border-white/5 p-7 animate-pulse">
        <div class="w-14 h-14 rounded-2xl bg-slate-200 dark:bg-slate-700/50 mb-6"></div>
        <div class="h-6 bg-slate-200 dark:bg-slate-700/50 rounded-md w-3/4 mb-3"></div>
        <div class="h-4 bg-slate-200 dark:bg-slate-700/50 rounded-md w-1/2"></div>
    </div>
@elseif($type === 'text')
    <div class="animate-pulse flex flex-col gap-3">
        <div class="h-4 bg-slate-200 dark:bg-slate-700/50 rounded-md w-full"></div>
        <div class="h-4 bg-slate-200 dark:bg-slate-700/50 rounded-md w-5/6"></div>
        <div class="h-4 bg-slate-200 dark:bg-slate-700/50 rounded-md w-4/6"></div>
    </div>
@endif