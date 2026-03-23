$(function () {

    const table = $('#itemsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '../api/biblioteczny-list.php',
            type: 'POST'
        },
        language: {
            url: '../assets/js/DataTable_pl.json'
        },
        pageLength: 25,
        order: [[0, "asc"]],
        columns: [
            { data: 'id' },
            { data: 'autor' },
            { data: 'tytul' },
            { data: 'rok_wydania_wydawca' },
            { data: 'nr_inwentarzowy' }
        ]
    });

});
