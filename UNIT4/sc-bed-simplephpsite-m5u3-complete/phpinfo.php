<!doctype html>
<html lang="en">

<?php include 'includes/head.php' ?>

<body <?= isset($_COOKIE['darkmode']) && $_COOKIE['darkmode'] === 'true' ? 'data-bs-theme="dark"' : '' ?>>
    <?php include 'includes/menu.php' ?>

    <div class="container">
        <?php phpinfo(); ?>
    </div>

    <?php include 'includes/footer.php' ?>
</body>

</html>