document.addEventListener("DOMContentLoaded", function () {
    const questions = document.querySelectorAll(".question-block");
    const navBtns = document.querySelectorAll(".nav-btn");
    const btnPrev = document.getElementById("btn-prev");
    const btnNext = document.getElementById("btn-next");
    const container = document.getElementById("question-container");

    // URL Endpoint untuk Auto-Save diambil dari atribut data HTML
    const storeUrl = container ? container.dataset.storeUrl : null;
    let currentIndex = 0;

    // --- LOGIKA NAVIGASI SOAL ---
    function showQuestion(index) {
        if (questions.length === 0) return;

        // Sembunyikan semua soal dan hapus highlight dari tombol navigasi
        questions.forEach((q) => {
            q.classList.remove("block");
            q.classList.add("hidden");
        });
        navBtns.forEach((b) => {
            b.classList.remove("ring-2", "ring-blue-600", "border-blue-600");
        });

        // Tampilkan soal yang aktif
        questions[index].classList.remove("hidden");
        questions[index].classList.add("block");

        // Beri highlight pada tombol navigasi soal aktif
        navBtns[index].classList.add(
            "ring-2",
            "ring-blue-600",
            "border-blue-600",
        );

        // Atur status tombol Sebelumnya/Selanjutnya
        if (btnPrev) btnPrev.disabled = index === 0;
        if (btnNext) btnNext.disabled = index === questions.length - 1;

        currentIndex = index;
    }

    if (btnNext) {
        btnNext.addEventListener("click", () => {
            if (currentIndex < questions.length - 1)
                showQuestion(currentIndex + 1);
        });
    }

    if (btnPrev) {
        btnPrev.addEventListener("click", () => {
            if (currentIndex > 0) showQuestion(currentIndex - 1);
        });
    }

    navBtns.forEach((btn, index) => {
        btn.addEventListener("click", () => showQuestion(index));
    });

    // Inisialisasi soal pertama saat halaman dimuat
    showQuestion(0);

    // --- LOGIKA AUTO-SAVE JAWABAN (AJAX) ---
    const answerRadios = document.querySelectorAll(".answer-radio");

    answerRadios.forEach((radio) => {
        radio.addEventListener("change", function () {
            const questionId = this.dataset.questionId;
            const optionId = this.value;

            // Tandai tombol navigasi (Grid) menjadi warna biru/terisi
            const navBtn = document.getElementById("nav-btn-" + questionId);
            if (navBtn) {
                navBtn.classList.remove("bg-white", "text-gray-700");
                navBtn.classList.add(
                    "bg-blue-100",
                    "text-blue-800",
                    "border-blue-300",
                );
            }

            // Simpan jawaban ke server jika endpoint tersedia
            if (storeUrl) {
                saveAnswer(questionId, optionId);
            }
        });
    });

    function saveAnswer(questionId, optionId) {
        // Ambil CSRF Token dari meta tag di layout utama (app.blade.php)
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.getAttribute("content") : "";

        fetch(storeUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                Accept: "application/json",
            },
            body: JSON.stringify({
                question_id: questionId,
                option_id: optionId,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (!data.success) {
                    console.error(
                        "Gagal menyimpan jawaban untuk soal: " + questionId,
                    );
                }
            })
            .catch((error) => {
                console.error(
                    "Terjadi kesalahan jaringan saat menyimpan jawaban:",
                    error,
                );
            });
    }
});
