@props(['schedules' => collect()])

<div id="modal-pengumuman" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/50 backdrop-blur-md transition-opacity duration-300 opacity-0">
    <div class="w-full max-w-lg bg-white dark:bg-[#1A1F29] rounded-[2rem] shadow-2xl border border-white/60 dark:border-white/10 transform scale-95 transition-transform duration-300 relative max-h-[90vh] overflow-y-auto custom-scrollbar">

        {{-- Tombol Tutup --}}
        <button type="button" onclick="closeAnnouncementModal()" class="absolute top-5 right-5 w-9 h-9 flex items-center justify-center rounded-full bg-slate-100 dark:bg-white/5 text-slate-500 hover:text-red-500 active:scale-90 transition-all z-10">
            <i class="ph-bold ph-x"></i>
        </button>

        <form action="{{ route('guru.announcement.store') }}" method="POST" class="p-7 lg:p-8">
            @csrf

            <div class="flex items-center gap-4 mb-7">
                <div class="w-12 h-12 rounded-[1rem] bg-yumik-primary/10 text-yumik-primary flex items-center justify-center shrink-0">
                    <i class="ph-fill ph-megaphone text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-extrabold text-slate-800 dark:text-white">Buat Pengumuman</h3>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Kirim ke siswa di kelas yang Anda ajar</p>
                </div>
            </div>

            <div class="space-y-2 mb-5">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-widest">Pilih Mapel & Kelas <span class="text-red-500">*</span></label>

                @if($schedules->isEmpty())
                    <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 text-sm font-medium text-amber-700 dark:text-amber-400">
                        <i class="ph-bold ph-warning-circle"></i>
                        Anda belum mengajar kelas apa pun di semester ini.
                    </div>
                @else
                    {{-- PASTIKAN X-SELECT MENERIMA SLOT DENGAN BENAR JIKA INI KOMPONEN CUSTOM ANDA --}}
                    <select name="schedule_id" required class="w-full p-3 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-black/20 text-sm text-slate-800 dark:text-white">
                        <option value="">— Pilih Mapel —</option>
                        {{-- Menggunakan null-safe operator ?-> untuk menghindari error jika relasi null --}}
                        @foreach($schedules as $sched)
                            <option value="{{ $sched->id ?? '' }}">
                                {{ $sched->subject?->name ?? 'Mapel Tidak Diketahui' }} — Kelas {{ $sched->classroom?->name ?? 'Tidak Diketahui' }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>

            <div class="space-y-2 mb-5">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-widest">Judul <span class="text-red-500">*</span></label>
                {{-- JIKA X-INPUT BERMASALAH, GANTI DENGAN INPUT HTML BIASA SEMENTARA --}}
                <input type="text" name="title" placeholder="Contoh: Ulangan diundur ke Senin depan" required maxlength="255" class="w-full p-3 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-black/20 text-sm text-slate-800 dark:text-white" />
            </div>

            <div class="space-y-2 mb-5">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-widest">Isi Pengumuman <span class="text-red-500">*</span></label>
                <textarea name="content" rows="5" required placeholder="Tulis detail pengumuman di sini..."
                    class="w-full p-4 rounded-2xl bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 text-sm text-slate-800 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-yumik-primary/50 transition-all resize-none"></textarea>
            </div>

            <div class="space-y-2 mb-7">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-widest">Prioritas <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="priority" value="NORMAL" class="peer sr-only" checked>
                        <div class="flex items-center justify-center gap-2 py-3 rounded-2xl border-2 border-slate-200 dark:border-white/10 peer-checked:border-yumik-primary peer-checked:bg-yumik-primary/5 transition-all">
                            <i class="ph-fill ph-info text-slate-400 peer-checked:text-yumik-primary"></i>
                            <span class="text-sm font-bold text-slate-600 dark:text-slate-300">Normal</span>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="priority" value="IMPORTANT" class="sr-only peer">
                        <div class="flex items-center justify-center gap-2 py-3 rounded-2xl border-2 border-slate-200 dark:border-white/10 peer-checked:border-red-500 peer-checked:bg-red-500/5 transition-all">
                            <i class="ph-fill ph-warning-circle text-slate-400 peer-checked:text-red-500"></i>
                            <span class="text-sm font-bold text-slate-600 dark:text-slate-300">Penting</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="flex gap-3 pt-5 border-t border-slate-100 dark:border-white/10">
                <button type="button" class="flex-1 py-3 bg-slate-100 text-slate-700 rounded-xl font-bold hover:bg-slate-200 transition-colors" onclick="closeAnnouncementModal()">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-3 bg-yumik-primary text-white rounded-xl font-bold hover:bg-green-600 transition-colors flex items-center justify-center gap-2" @if($schedules->isEmpty()) disabled @endif>
                    <i class="ph-bold ph-paper-plane-tilt"></i> Kirim
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openAnnouncementModal() {
        const modal = document.getElementById('modal-pengumuman');
        modal.classList.remove('hidden');
        modal.classList.add('flex'); // Tambahkan flex agar posisinya center
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.querySelector('.transform').classList.remove('scale-95');
        }, 10);
    }

    function closeAnnouncementModal() {
        const modal = document.getElementById('modal-pengumuman');
        modal.classList.add('opacity-0');
        modal.querySelector('.transform').classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex'); // Hapus flex
        }, 300);
    }

    document.addEventListener('click', function(e) {
        const modal = document.getElementById('modal-pengumuman');
        if (modal && e.target === modal) closeAnnouncementModal();
    });
</script>
@endpush