document.addEventListener("DOMContentLoaded", () => {
    // Tangkap semua radio button role
    const roleRadios = document.querySelectorAll('input[name="role_id"]');

    // Jika tidak ada form role di halaman ini, hentikan skrip
    if (roleRadios.length === 0) return;

    const panelDemografi = document.getElementById("panel-demografi");
    const panelSpesifik = document.getElementById("panel-spesifik");
    const wrapGuru = document.getElementById("wrapper-guru");
    const wrapSiswa = document.getElementById("wrapper-siswa");
    const judulSpesifik = document.getElementById("judul-spesifik");

    const handleRoleChange = (roleId) => {
        // Sembunyikan semua wrapper spesifik terlebih dahulu
        if (wrapGuru) wrapGuru.classList.add("hidden");
        if (wrapSiswa) wrapSiswa.classList.add("hidden");

        if (roleId == 2) {
            if (panelDemografi) panelDemografi.classList.remove("hidden");
            if (panelSpesifik) panelSpesifik.classList.remove("hidden");
            if (wrapGuru) wrapGuru.classList.remove("hidden");
            if (judulSpesifik) judulSpesifik.innerText = "Data Kepegawaian";
        } else if (roleId == 3) {
            if (panelDemografi) panelDemografi.classList.remove("hidden");
            if (panelSpesifik) panelSpesifik.classList.remove("hidden");
            if (wrapSiswa) wrapSiswa.classList.remove("hidden");
            if (judulSpesifik)
                judulSpesifik.innerText = "Data Akademik & Orang Tua";
        } else {
            if (panelDemografi) panelDemografi.classList.add("hidden");
            if (panelSpesifik) panelSpesifik.classList.add("hidden");
        }
    };

    // Pasang event listener 'change' ke setiap radio button via JavaScript (bukan inline HTML)
    roleRadios.forEach((radio) => {
        radio.addEventListener("change", (e) => {
            handleRoleChange(e.target.value);
        });
    });

    // Jalankan satu kali saat halaman dimuat (untuk menangani old() data saat validasi error)
    const checkedRadio = document.querySelector(
        'input[name="role_id"]:checked',
    );
    if (checkedRadio) handleRoleChange(checkedRadio.value);
});
