<!doctype html>
<html lang="en">

<?php include 'includes/head.php' ?>

<body <?= isset($_COOKIE['darkmode']) && $_COOKIE['darkmode'] == 'true' ? 'data-bs-theme="dark"' : ''?>>
    <?php include 'includes/menu.php' ?>

    <div class="container">
        <h1>PHP Summary</h1>
        <?php
            printf('<p>You are running PHP %s</p>', phpversion());       
        ?>

        <div class="card">
            <div class="card-header">
                Loaded Extensions:
            </div>
            <div class="card-body">
                <?php
                foreach(get_loaded_extensions() as $ext){
                    echo "<span class='badge text-bg-info' style='margin: 2px;'>$ext</span>";
                }
                ?>
            </div>
        </div>

        <div class="card" style='margin-top: 10px;'>
            <div class="card-header">
                Server Environment:
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Key</th>
                            <th>Value</th>
                    </thead>
                    <tbody>
                        <?php
                        foreach($_SERVER as $key => $value){
                            echo "<tr><td>$key</td><td>$value</td></tr>";
                        }
                        ?>
                    </tbody>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php' ?>
</body>

</html>