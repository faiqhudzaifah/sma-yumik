@props(['assignment'])

@php
    $schedule = $assignment->schedule;
    $subject = $schedule->subject ?? null;
    $dueDate = $assignment->due_date;
    $isUrgent = $dueDate && $dueDate->diffInDays(now()) <= 2 && $dueDate->isFuture();
@endphp

<a href="{{ route('siswa.assignment.show', $assignment->id) }}" class="block group">
    <div class="flex items-start gap-4 p-4 rounded-2xl hover:bg-slate-50 dark:hover:bg-white/5 transition-colors border border-transparent hover:border-slate-100 dark:hover:border-white/10">
        <div class="w-11 h-11 rounded-xl {{ $isUrgent ? 'bg-red-100 dark:bg-red-500/20 text-red-500' : 'bg-amber-100 dark:bg-amber-500/20 text-amber-500' }} flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
            <i class="ph-fill ph-clipboard-text text-xl"></i>
        </div>

        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-2 mb-1">
                <h4 class="text-sm font-bold text-slate-800 dark:text-white truncate group-hover:text-yumik-primary transition-colors">
                    {{ $assignment->title }}
                </h4>
                @if($isUrgent)
                    <span class="text-[9px] font-black uppercase tracking-widest text-red-500 bg-red-50 dark:bg-red-500/10 px-2 py-0.5 rounded-full shrink-0">
                        Segera
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500 font-medium truncate">
                {{ $subject->name ?? '-' }} · {{ $assignment->type }}
            </p>
            @if($dueDate)
                <p class="text-[11px] font-bold mt-1.5 flex items-center gap-1 {{ $isUrgent ? 'text-red-500' : 'text-slate-400' }}">
                    <i class="ph-bold ph-clock"></i>
                    Tenggat: {{ $dueDate->translatedFormat('d M Y, H:i') }}
                </p>
            @endif
        </div>

        <i class="ph-bold ph-caret-right text-slate-300 dark:text-slate-600 group-hover:text-yumik-primary group-hover:translate-x-1 transition-all self-center"></i>
    </div>
</a>