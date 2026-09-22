/**
 * ui-animations.js
 * Menangani transisi halus (UX Loading State) pada elemen antarmuka pengguna
 * dan Toast Notification.
 */

document.addEventListener("DOMContentLoaded", () => {
    // ==========================================
    // 1. LOGIKA UX LOADING
    // ==========================================
    const container = document.getElementById("data-container");

    // Kita buat fungsi transisi visual yang aman (mengecek keberadaan container di dalamnya)
    const showLoadingState = () => {
        if (container) {
            container.classList.add(
                "opacity-40",
                "pointer-events-none",
                "animate-pulse",
                "scale-[0.99]",
            );
        }
    };

    const searchForm = document.getElementById("search-form");
    if (searchForm) {
        searchForm.addEventListener("submit", showLoadingState);
    }

    const actionLinks = document.querySelectorAll("a[href]");
    actionLinks.forEach((link) => {
        link.addEventListener("click", function (e) {
            const url = this.getAttribute("href");

            if (
                url &&
                !url.startsWith("#") &&
                !url.startsWith("javascript:") &&
                this.target !== "_blank"
            ) {
                if (
                    this.classList.contains("filter-tab") ||
                    this.closest(".pagination-container") ||
                    url.includes("?page=")
                ) {
                    showLoadingState();
                } else {
                    if (container) {
                        container.classList.add(
                            "opacity-70",
                            "pointer-events-none",
                        );
                    }
                }
            }
        });
    });

    // ==========================================
    // 2. LOGIKA TOAST NOTIFICATION
    // ==========================================
    const toasts = document.querySelectorAll(".yumik-toast");

    toasts.forEach((toast, index) => {
        // Beri sedikit jeda jika ada lebih dari 1 notifikasi (stacking)
        const delay = index * 150;

        // Animasi Masuk (Slide-in)
        setTimeout(() => {
            toast.classList.remove("translate-x-[120%]", "opacity-0");
        }, 100 + delay);

        // Fungsi Animasi Keluar (Slide-out)
        const dismissToast = () => {
            toast.classList.add("translate-x-[120%]", "opacity-0");
            // Hapus elemen dari DOM setelah animasi selesai
            setTimeout(() => {
                toast.remove();
            }, 500);
        };

        // Otomatis menghilang setelah 3.5 detik (3500ms)
        const autoDismiss = setTimeout(dismissToast, 3500 + delay);

        // Bisa ditutup manual dengan mengklik tombol X
        const closeBtn = toast.querySelector(".toast-close");
        if (closeBtn) {
            closeBtn.addEventListener("click", () => {
                clearTimeout(autoDismiss); // Batalkan timer otomatis
                dismissToast();
            });
        }
    });
}); // <-- Diperbaiki di sini, hanya "});"
