@props(['paginator'])

@if ($paginator->hasPages())
<div class="flex items-center justify-center gap-2 mt-8">
    
    <!-- Tombol Previous -->
    @if ($paginator->onFirstPage())
        <button class="w-10 h-10 rounded-[1rem] bg-white/20 dark:bg-white/5 backdrop-blur-md border border-white/60 dark:border-white/10 flex items-center justify-center text-slate-300 dark:text-slate-600 cursor-not-allowed" disabled>
            <i class="ph-bold ph-caret-left text-sm"></i>
        </button>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="w-10 h-10 rounded-[1rem] bg-white/40 dark:bg-white/5 backdrop-blur-md border border-white/60 dark:border-white/10 flex items-center justify-center text-slate-500 hover:bg-white dark:hover:bg-white/10 transition-colors">
            <i class="ph-bold ph-caret-left text-sm"></i>
        </a>
    @endif

    <!-- Loop Nomor Halaman -->
    @foreach ($paginator->elements() as $element)
        <!-- Separator Tiga Titik -->
        @if (is_string($element))
            <span class="w-10 h-10 flex items-center justify-center text-slate-400 font-bold tracking-widest">{{ $element }}</span>
        @endif

        <!-- Array Link Halaman -->
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <button class="w-10 h-10 rounded-[1rem] bg-yumik-primary text-white font-bold shadow-md shadow-yumik-primary/30 flex items-center justify-center transition-transform cursor-default">{{ $page }}</button>
                @else
                    <a href="{{ $url }}" class="w-10 h-10 rounded-[1rem] bg-white/40 dark:bg-white/5 backdrop-blur-md border border-white/60 dark:border-white/10 flex items-center justify-center font-medium text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-white/10 transition-colors">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    <!-- Tombol Next -->
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="w-10 h-10 rounded-[1rem] bg-white/40 dark:bg-white/5 backdrop-blur-md border border-white/60 dark:border-white/10 flex items-center justify-center text-slate-500 hover:bg-white dark:hover:bg-white/10 transition-colors">
            <i class="ph-bold ph-caret-right text-sm"></i>
        </a>
    @else
        <button class="w-10 h-10 rounded-[1rem] bg-white/20 dark:bg-white/5 backdrop-blur-md border border-white/60 dark:border-white/10 flex items-center justify-center text-slate-300 dark:text-slate-600 cursor-not-allowed" disabled>
            <i class="ph-bold ph-caret-right text-sm"></i>
        </button>
    @endif
    
</div>
@endif