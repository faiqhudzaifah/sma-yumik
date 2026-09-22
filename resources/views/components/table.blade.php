<div class="w-full overflow-hidden rounded-[2rem] bg-white/60 dark:bg-[#1A1F29]/60 backdrop-blur-2xl border-2 border-white/90 dark:border-white/5 shadow-[0_15px_40px_-15px_rgba(0,0,0,0.05)] dark:shadow-none">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 dark:bg-black/20 border-b border-slate-200 dark:border-white/5">
                    {{ $head }}
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>