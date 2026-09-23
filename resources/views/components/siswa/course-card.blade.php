@props(['schedule'])

@php
    // /** @var \App\Models\Schedule $schedule */
    $subject = $schedule->subject;
    $guru = $schedule->guru;
@endphp

<a href="{{ route('siswa.course.show', $schedule->id) }}" class="block group h-full">
    <x-card class="!p-6 h-full hover:!shadow-[0_20px_50px_-15px_rgba(24,201,93,0.3)] hover:-translate-y-1 transition-all duration-300 cursor-pointer border-2 border-transparent hover:border-yumik-primary/20">
        <div class="flex items-start justify-between mb-5">
            <div class="w-14 h-14 rounded-[1.25rem] bg-gradient-to-br from-yumik-primary/10 to-yumik-primary/5 flex items-center justify-center text-yumik-primary group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                <i class="ph-fill ph-book-bookmark text-2xl"></i>
            </div>
            <x-badge color="accent">{{ $subject->code ?? 'MAPEL' }}</x-badge>
        </div>

        <h3 class="text-lg font-extrabold text-slate-800 dark:text-white mb-2 line-clamp-2 group-hover:text-yumik-primary transition-colors leading-snug">
            {{ $subject->name ?? 'Nama Mata Pelajaran' }}
        </h3>

        <p class="text-xs text-slate-400 font-medium mb-5 flex items-center gap-1.5">
            <i class="ph-fill ph-door text-slate-400"></i>
            Kelas {{ $schedule->classroom->name ?? '-' }}
        </p>

        <div class="mt-auto pt-4 border-t border-slate-100 dark:border-white/5 flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-400 shrink-0">
                <i class="ph-fill ph-chalkboard-teacher text-sm"></i>
            </div>
            <div class="overflow-hidden">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 leading-none mb-0.5">Pengajar</p>
                <p class="text-sm font-bold text-slate-700 dark:text-slate-200 truncate">{{ $guru->name ?? 'Guru' }}</p>
            </div>
        </div>
    </x-card>
</a>