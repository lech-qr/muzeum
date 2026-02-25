<?php $katalog = "mainNav"; ?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Katalogi - Muzeum Zamojskiego w Zamościu</title>
    <?php require_once '../assets/php/head.php'; ?>
</head>
    <body>

        <div class="<?php echo $katalog; ?>">
            <span id="katalog" class="d-none"><?php echo $katalog; ?></span>
            <?php require_once '../assets/php/navigation.php'; ?>
        </div>

        <!-- IMAGE MODAL -->
        <div id="imageModal" class="img-modal">
            <span class="close">&times;</span>
            <img class="modal-content" id="modalImage">
        </div>

        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
        <script src="assets/js/scripts.js"></script>

    </body>
</html>
