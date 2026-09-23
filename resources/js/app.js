import "@phosphor-icons/web/regular";
import "@phosphor-icons/web/bold";
import Alpine from "alpinejs";

window.Alpine = Alpine;

// Tunggu DOM siap sebelum start Alpine
document.addEventListener("DOMContentLoaded", () => {
    Alpine.start();
});
