let currentType = 'archeologiczny';

$(function () {

    const table = $('#itemsTable').DataTable({

        processing: true,
        serverSide: true,
        ajax: {
            url: '../api/archeologiczny-list.php',
            type: 'POST'
        },
        language: {
            url: '../assets/js/DataTable_pl.json'
        },
        pageLength: 25,
        order: [[0, "asc"]],
        columns: [
            { data: 'id' },

            { data: 'przedmiot' },

            {
                data: 'szczegolowa_charakterystyka',
                orderable: false,
                render: function (data) {
                    if (!data) return '';
                    return data.length > 300
                        ? data.slice(0, 300).replace(/\s+\S*$/, '') + ' <i>więcej…</i>'
                        : data;
                }
            },

            {
                data: 'zdjecie_lokalne',
                orderable: false,
                class: 'img-column',
                render: function (data, type, row) {

                    if (!data) return '';

                    return `
                        <img 
                            src="../images/archeologiczny/${data}" 
                            style="height:60px; cursor:pointer;"
                            onclick="openGallery(${row.id}, 0, 'archeologiczny')"
                        >
                    `;
                }
            },


            { data: 'chronologia' },
            { data: 'kultura' },
            { data: 'nr_inwentarzowy' }
        ]
    });

    /*
    |--------------------------------------------------------------------------
    | Klik w wiersz → karta
    |--------------------------------------------------------------------------
    */

    $('#itemsTable tbody').on('click', 'tr', function (e) {

        if ($(e.target).is('img') || $(e.target).is('a')) return;

        const data = table.row(this).data();

        if (data && data.id) {

            function slugify(text) {
                return text
                    .toString()
                    .toLowerCase()
                    .normalize("NFD")
                    .replace(/[\u0300-\u036f]/g, "")
                    .replace(/[^a-z0-9]+/g, "-")
                    .replace(/(^-|-$)+/g, "");
            }

            const slug = slugify(data.przedmiot);

            window.open(
                "/katalog-archeologiczny/" + data.id + "-" + slug,
                "kartaProduktu",
                "width=1200,height=1100,menubar=no,toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes"
            );
        }
    });

});