<?php $katalog = "muzealny"; ?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Katalog <?php echo $katalog; ?> - Muzeum Zamojskiego w Zamościu</title>

    <link rel="icon" href="../images/32x32.jpg" sizes="32x32" />
    <link rel="icon" href="../images/192x192.jpg" sizes="192x192" />
    <link rel="apple-touch-icon" href="../images/180x180.jpg" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.3.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="../assets/style/css/style.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">

    <?php require_once '../assets/php/navigation.php'; ?>

    <h2 class="mb-4">Katalog <span id="katalog"><?php echo $katalog; ?></span> - Muzeum Zamojskiego w Zamościu</h2>

    <table id="itemsTable" class="table table-striped table-bordered w-100">
        <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>Dział</th>            
            <th>Przedmiot</th>
            <th>Fotografia</th>
            <th>Autor</th>
            <th>Nr. inw</th>
            <th>Opis</th>
        </tr>
        </thead>
    </table>
</div>

<!-- IMAGE MODAL -->
<div id="imageModal" class="img-modal">
    <span class="close">&times;</span>
    <img class="modal-content" id="modalImage">
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.3.7/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.7/js/dataTables.bootstrap5.min.js"></script>
<script src="../assets/js/app-muzealny.js"></script>
<script src="../assets/js/scripts.js"></script>

</body>
</html>
