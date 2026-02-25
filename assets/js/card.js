$(document).ready(function () {
    // Kopiowanie linku do schowka
    $(document).on('click', '.link-btn', function () {
        let url = window.location.href;
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(url)
                .then(function () {
                    showToast();
                })
                .catch(function () {
                    fallbackCopy(url);
                });
        } else {
            fallbackCopy(url);
        }
    });
    function fallbackCopy(text) {
        let textarea = document.createElement("textarea");
        textarea.value = text;
        textarea.style.position = "fixed";
        textarea.style.opacity = "0";
        document.body.appendChild(textarea);
        textarea.select();

        let copied = document.execCommand("copy");
        document.body.removeChild(textarea);

        if (copied) {
            showToast();
        }
        // ✅ jeśli false — nic nie pokazujemy (bo często i tak działa)
    }
    function showToast() {
        let toast = new bootstrap.Toast(document.getElementById('copyToast'));
        toast.show();
    }
    // Następna / Poprzednia karta - wybór z klawiatury
    document.addEventListener("keydown", function (e) {

        if (e.key === "ArrowLeft") {
            const prev = document.querySelector(".nav-btn.prev");
            if (prev) window.location.href = prev.href;
        }

        if (e.key === "ArrowRight") {
            const next = document.querySelector(".nav-btn.next");
            if (next) window.location.href = next.href;
        }

    });

});