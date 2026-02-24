$(function () {

    $('#itemsTable').DataTable({
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
            { data: 'dzial' },
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
                render: function (data) {
                    if (!data) return '';
                    // return data.length > 400 ? data.substring(0, 400) + '...' : data;
                    return data.length > 400
                        ? data.slice(0, 400).replace(/\s+\S*$/, '') + ' <i>więcej…</i>'
                        : data;
                }
            }
        ]
    });

});
