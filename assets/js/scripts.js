// Nawigacja
$(document).ready(function () {
    let katalog = $('#katalog').text();
    // console.log("Obecny katalog to katalog " + katalog);
    $('nav a').removeClass('active');
    $('#' + katalog).addClass('active');
    // Dodaj href na podstawie id, ale tylko dla nieaktywnego
    $('nav a').each(function () {
        let thisNav = $(this).attr('id');
        if ($(this).hasClass('active')) {
            $(this).removeAttr('href');
        } else {
            if (katalog === 'mainNav') {
                $(this).attr('href', '/katalog-' + thisNav);
            } else {
                $(this).attr('href', '../katalog-' + thisNav);
            }
        }
    });
});

// // Pokaz zdjęć
// function openImageModal(src) {
//     const modal = document.getElementById("imageModal");
//     const modalImg = document.getElementById("modalImage");

//     modal.style.display = "block";
//     modalImg.src = src;
// }

// // zamykanie
// document.querySelector("#imageModal .close").onclick = function () {
//     document.getElementById("imageModal").style.display = "none";
// };

// // zamknięcie kliknięciem w tło
// document.getElementById("imageModal").onclick = function (e) {
//     if (e.target === this) {
//         this.style.display = "none";
//     }
// };

let currentItemId;
let currentImages = [];
let currentTitle = '';
let currentIndex = 0;
let prevItemId;
let nextItemId;
window.openGallery = function (itemId, imageIndex = 0) {

    console.log("Kliknięto ID:", itemId);

    $.getJSON('../../api/getGallery.php', { id: itemId }, function (data) {

        currentItemId = data.id;
        currentImages = data.images;
        currentTitle = data.title || '';
        currentIndex = imageIndex;
        prevItemId = data.prev;
        nextItemId = data.next;

        showImage();
        $('#imageModal').addClass('active');
    });

};

function showImage() {
    $('#modalImage').attr('src', '/images/muzealny/' + currentImages[currentIndex]);
    $('#modalCaption').text(currentTitle);
}
// Poprzedni modal
$('#modalPrev').on('click', function () {
    if (!prevItemId) return;   // 🔒 zabezpieczenie
    openGallery(prevItemId);
});
// Następny modal
$('#modalNext').on('click', function () {
    if (!nextItemId) return;   // 🔒 zabezpieczenie
    openGallery(nextItemId);
});
// Obsługa klawiaturą
$(document).on('keydown', function (e) {

    if (!$('#imageModal').is(':visible')) return;

    if (e.key === 'ArrowRight') {
        $('#modalNext').click();
    }

    if (e.key === 'ArrowLeft') {
        $('#modalPrev').click();
    }

    if (e.key === 'Escape') {
        $('#imageModal').removeClass('active');
    }

});
// Zamykanie modala
$('#modalClose').on('click', function () {
    $('#imageModal').removeClass('active');
});
// Kliknij poza modaj
$('#imageModal').on('click', function (e) {
    if (!$(e.target).closest('#modalImage, #modalPrev, #modalNext, #modalClose').length) {
        $('#imageModal').removeClass('active');
    }
});

// Kliknij w modal - przejdź do karty
$('#modalImage').on('click', function (e) {

    e.stopPropagation(); // żeby nie zamknęło modala

    function slugify(text) {
        return text
            .toString()
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .replace(/[^a-z0-9]+/g, "-")
            .replace(/(^-|-$)+/g, "");
    }

    const slug = slugify(currentTitle);

    const width = 1200;
    const height = 1100;

    window.open(
        "/katalog-muzealny/" + currentItemId + "-" + slug,
        "kartaProduktu",
        `width=${width},height=${height},
         menubar=no,toolbar=no,location=no,status=no,
         scrollbars=yes,resizable=yes`
    );
});

