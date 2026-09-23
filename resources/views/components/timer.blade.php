@props(['endTime'])

<div class="sticky top-6 z-50 inline-flex items-center gap-3 px-6 py-3 rounded-full bg-white/80 dark:bg-[#1A1F29]/80 backdrop-blur-xl border border-white dark:border-white/10 shadow-[0_8px_30px_rgba(0,0,0,0.08)]">
    <i class="ph-fill ph-timer text-xl text-yumik-primary animate-pulse"></i>
    <span class="font-mono text-xl font-bold text-slate-800 dark:text-white tracking-widest" id="cbt-timer">00:00:00</span>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // endTime format expected: 'Y-m-d H:i:s'
        const countDownDate = new Date("{{ $endTime }}").getTime();
        const timerDisplay = document.getElementById("cbt-timer");

        const x = setInterval(function() {
            const now = new Date().getTime();
            const distance = countDownDate - now;

            if (distance < 0) {
                clearInterval(x);
                timerDisplay.innerHTML = "WAKTU HABIS";
                timerDisplay.classList.replace('text-slate-800', 'text-red-500');
                
                // Trigger auto-submit jika form cbt-form tersedia
                const form = document.getElementById('cbt-form');
                if(form) form.submit();
                return;
            }

            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            timerDisplay.innerHTML = 
                String(hours).padStart(2, '0') + ":" + 
                String(minutes).padStart(2, '0') + ":" + 
                String(seconds).padStart(2, '0');
                
            // Peringatan visual jika sisa waktu kurang dari 5 menit
            if (distance < 300000) {
                timerDisplay.classList.add('text-red-500');
            }
        }, 1000);
    });
</script>
@endpush