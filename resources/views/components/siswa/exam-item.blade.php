@props(['assessment'])

@php
    $schedule = $assessment->schedule;
    $subject = $schedule->subject ?? null;
    $endAt = $assessment->end_at;
    $hoursLeft = $endAt ? now()->diffInHours($endAt, false) : null;
    $isUrgent = $hoursLeft !== null && $hoursLeft <= 24 && $hoursLeft >= 0;
@endphp

<a href="{{ route('siswa.cbt.show', $assessment->id) }}" class="block group">
    <div class="flex items-start gap-4 p-4 rounded-2xl hover:bg-slate-50 dark:hover:bg-white/5 transition-colors border border-transparent hover:border-slate-100 dark:hover:border-white/10">
        <div class="w-11 h-11 rounded-xl {{ $isUrgent ? 'bg-red-100 dark:bg-red-500/20 text-red-500' : 'bg-purple-100 dark:bg-purple-500/20 text-purple-500' }} flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
            <i class="ph-fill ph-exam text-xl"></i>
        </div>

        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-2 mb-1">
                <h4 class="text-sm font-bold text-slate-800 dark:text-white truncate group-hover:text-yumik-primary transition-colors">
                    {{ $assessment->title }}
                </h4>
                <span class="text-[9px] font-black uppercase tracking-widest {{ $assessment->type === 'EXAM' ? 'text-purple-500 bg-purple-50 dark:bg-purple-500/10' : 'text-blue-500 bg-blue-50 dark:bg-blue-500/10' }} px-2 py-0.5 rounded-full shrink-0">
                    {{ $assessment->type }}
                </span>
            </div>
            <p class="text-xs text-slate-500 font-medium truncate">
                {{ $subject->name ?? '-' }} · {{ $assessment->duration }} menit
            </p>
            @if($endAt)
                <p class="text-[11px] font-bold mt-1.5 flex items-center gap-1 {{ $isUrgent ? 'text-red-500' : 'text-slate-400' }}">
                    <i class="ph-bold ph-hourglass"></i>
                    Berakhir: {{ $endAt->translatedFormat('d M Y, H:i') }}
                </p>
            @endif
        </div>

        <i class="ph-bold ph-caret-right text-slate-300 dark:text-slate-600 group-hover:text-yumik-primary group-hover:translate-x-1 transition-all self-center"></i>
    </div>
</a>