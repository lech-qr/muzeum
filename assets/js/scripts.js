// Pokaz zdjęć
function openImageModal(src) {
    const modal = document.getElementById("imageModal");
    const modalImg = document.getElementById("modalImage");

    modal.style.display = "block";
    modalImg.src = src;
}

// zamykanie
document.querySelector("#imageModal .close").onclick = function () {
    document.getElementById("imageModal").style.display = "none";
};

// zamknięcie kliknięciem w tło
document.getElementById("imageModal").onclick = function (e) {
    if (e.target === this) {
        this.style.display = "none";
    }
};
