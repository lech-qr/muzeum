$(function () {

    // Testy
    // console.log("Test JS start");
    // $(function () {
    //     console.log("jQuery działa:", typeof $);

    //     if ($('#itemsTable').length) {
    //         console.log("Tabela znaleziona");
    //     } else {
    //         console.log("BRAK tabeli");
    //     }
    // });
    // Testy - KONIEC

    $('#itemsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'api/getItems.php',
            type: 'POST'
        },
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/pl.json'
        },
        pageLength: 25,
        order: [[0, "asc"]],
        columns: [
            { data: 'id' },
            { data: 'dzial' },
            { data: 'przedmiot' },
            {
                data: 'zdjecie_lokalne',
                orderable: false,
                render: function (data) {

                    if (!data) return '';

                    return `
            <img 
                src="images/${data}" 
                style="height:60px; cursor:pointer; border-radius:4px;"
                onclick="openImageModal('images/${data}')"
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
                    return data.length > 400 ? data.substring(0, 400) + '...' : data;
                }
            }
        ]
    });

});
