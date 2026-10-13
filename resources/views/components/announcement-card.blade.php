@props(['announcement', 'showSchedule' => true, 'allowMarkRead' => false, 'isRead' => false])

@php
    $isImportant = $announcement->priority === 'IMPORTANT';
@endphp

<div class="relative overflow-hidden rounded-2xl border {{ $isImportant ? 'border-red-200 dark:border-red-500/30 bg-red-50/50 dark:bg-red-500/5' : 'border-slate-100 dark:border-white/5 bg-slate-50/50 dark:bg-white/5' }} p-4 transition-all hover:shadow-md"
    data-announcement-id="{{ $announcement->id }}">

    @if($isImportant)
        <div class="absolute top-0 right-0 px-2.5 py-0.5 bg-red-500 text-white text-[9px] font-black uppercase tracking-widest rounded-bl-xl">
            Penting
        </div>
    @endif

    <div class="flex items-start gap-3">
        <div class="w-10 h-10 rounded-xl {{ $isImportant ? 'bg-red-100 dark:bg-red-500/20 text-red-500' : 'bg-yumik-primary/10 text-yumik-primary' }} flex items-center justify-center shrink-0">
            <i class="ph-fill ph-megaphone text-lg"></i>
        </div>

        <div class="flex-1 min-w-0">
            @if($showSchedule && $announcement->schedule)
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">
                    {{ $announcement->schedule->subject->name ?? '-' }}
                    @if($announcement->schedule->classroom)
                        · {{ $announcement->schedule->classroom->name }}
                    @endif
                </p>
            @endif

            <h4 class="text-sm font-bold text-slate-800 dark:text-white mb-1">{{ $announcement->title }}</h4>
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed whitespace-pre-line">{{ $announcement->content }}</p>

            <div class="flex items-center justify-between mt-3">
                <p class="text-[10px] font-medium text-slate-400">
                    <i class="ph-bold ph-clock"></i>
                    {{ $announcement->start_at->diffForHumans() }}
                    @if($announcement->user)
                        · oleh {{ $announcement->user->name }}
                    @endif
                </p>

                @if($allowMarkRead && !$isRead)
                    <button type="button"
                        onclick="markAnnouncementRead({{ $announcement->id }})"
                        class="text-[10px] font-bold text-yumik-primary hover:underline flex items-center gap-1">
                        <i class="ph-bold ph-check-circle"></i> Tandai Dibaca
                    </button>
                @elseif($allowMarkRead && $isRead)
                    <span class="text-[10px] font-bold text-emerald-500 flex items-center gap-1">
                        <i class="ph-fill ph-check-circle"></i> Sudah Dibaca
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>

@if($allowMarkRead)
    @once
        @push('scripts')
        <script>
            function markAnnouncementRead(id) {
                fetch(`/announcement/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const card = document.querySelector(`[data-announcement-id="${id}"]`);
                        if (card) {
                            card.style.transition = 'opacity 0.3s';
                            card.style.opacity = '0.4';
                            const btn = card.querySelector('button[onclick*="markAnnouncementRead"]');
                            if (btn) {
                                btn.outerHTML = '<span class="text-[10px] font-bold text-emerald-500 flex items-center gap-1"><i class="ph-fill ph-check-circle"></i> Sudah Dibaca</span>';
                            }
                        }
                    }
                })
                .catch(err => console.error('Gagal tandai baca:', err));
            }
        </script>
        @endpush
    @endonce
@endif