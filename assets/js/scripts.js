// Nawigacja
$(document).ready(function () {
    let katalog = $('#katalog').text();
    $('nav a').removeClass('active');
    $('#' + katalog).addClass('active');
    // Dodaj href na podstawie id, ale tylko dla nieaktywnego
    $('nav a').each(function () {
        let thisNav = $(this).attr('id');
        if ($(this).hasClass('active')) {
            $(this).removeAttr('href');
        } else {
            $(this).attr('href', '../katalog-' + thisNav);
        }
    });

});

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
