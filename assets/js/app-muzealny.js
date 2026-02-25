$(function () {

    // $('#itemsTable').DataTable({
    const table = $('#itemsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '../api/muzealny-list.php',
            type: 'POST'
        },
        language: {
            url: '../assets/js/DataTable_pl.json'
        },
        pageLength: 25,
        order: [[0, "asc"]],
        columns: [
            { data: 'id' },
            { data: 'dzial', orderable: false },
            { data: 'przedmiot' },
            {
                data: 'zdjecie_lokalne',
                class: 'img-column',
                orderable: false,
                render: function (data) {

                    if (!data) return '';

                    return `
            <img 
                src="../images/muzealny/${data}" 
                onclick="openImageModal('../images/muzealny/${data}')"
            >
        `;
                }
            },
            { data: 'autor_szkola' },
            { data: 'nr_inwentarzowy' },
            {
                data: 'opis',
                orderable: false,
                render: function (data) {
                    if (!data) return '';
                    // return data.length > 400 ? data.substring(0, 400) + '...' : data;
                    return data.length > 400
                        ? data.slice(0, 400).replace(/\s+\S*$/, '') + ' <i>więcej…</i>'
                        : data;
                }
            }
        ],
        // Filtrowanie działów
        initComplete: function () {

            this.api().columns(1).every(function () {

                let column = this;

                let select = $('<select><option value="">Wszystkie działy</option></select>')
                    .appendTo($(column.header()).empty())
                    .on('change', function () {

                        let val = $.fn.dataTable.util.escapeRegex($(this).val());

                        column
                            .search(val ? '^' + val + '$' : '', true, false)
                            .draw();
                    });

                column.data().unique().sort().each(function (d) {
                    select.append('<option value="' + d + '">' + d + '</option>');
                });

            });

        }
    });

    // Obsługa kliknięcia w wiersz tabeli - otwarcie karty katalogowej
    $('#itemsTable tbody').on('click', 'tr', function (e) {

        // jeśli kliknięto obrazek → nie rób nic
        if ($(e.target).is('img')) return;

        var data = table.row(this).data();

        if (data && data.id) {
            function slugify(text) {
                return text
                    .toString()
                    .toLowerCase()
                    .normalize("NFD")
                    .replace(/[\u0300-\u036f]/g, "") // usuwa polskie znaki
                    .replace(/[^a-z0-9]+/g, "-")
                    .replace(/(^-|-$)+/g, "");
            }

            const slug = slugify(data.przedmiot);

            window.open(
                // "../details/karta-katalog-muzealny.php?id=" + data.id,
                "/katalog-muzealny/" + data.id + "-" + slug,
                "kartaProduktu",
                "width=1200,height=1100,menubar=no,toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes"
            );
        }
    });
});
