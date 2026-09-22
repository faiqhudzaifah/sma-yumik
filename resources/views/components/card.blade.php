<div {{ $attributes->merge(['class' => 'relative overflow-hidden rounded-[2rem] bg-white/60 dark:bg-[#1A1F29]/60 backdrop-blur-2xl border-2 border-white/90 dark:border-white/5 shadow-[0_15px_40px_-15px_rgba(24,201,93,0.15)] dark:shadow-none p-7 transition-all duration-300 hover:shadow-[0_20px_50px_-15px_rgba(24,201,93,0.25)]']) }}>
    
    <!-- Gradien kilau internal -->
    <div class="absolute inset-0 bg-gradient-to-br from-white/40 via-transparent to-white/10 dark:from-white/5 dark:via-transparent dark:to-transparent pointer-events-none"></div>
    
    <div class="relative z-10 h-full flex flex-col">
        {{ $slot }}
    </div>
</div>