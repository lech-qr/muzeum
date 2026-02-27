<?php $katalog = "muzealny"; ?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Katalog <?php echo $katalog; ?> - Muzeum Zamojskiego w Zamościu</title>
    <?php require_once '../assets/php/head.php'; ?>
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
<footer>
    <p>&copy;&nbsp;<?php echo date("Y"); ?>&nbsp;Copyright by <strong>Muzeum Zamojskie w Zamościu</strong></p>
</footer>

<!-- IMAGE MODAL -->
<div id="imageModal">
    <span id="modalClose">&times;</span>
    <div class="modal-content">
        <img id="modalImage" src="">
        <div id="modalCaption"></div>
    </div>
    <div id="modalPrev">
        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-arrow-left-square" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm11.5 5.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z"></path>
        </svg>        
    </div>
    <div id="modalNext">
        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-arrow-right-square" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm4.5 5.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5z"></path>
        </svg>        
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.3.7/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.7/js/dataTables.bootstrap5.min.js"></script>
<script src="../assets/js/app-muzealny.js"></script>
<script src="../assets/js/scripts.js"></script>

</body>
</html>
