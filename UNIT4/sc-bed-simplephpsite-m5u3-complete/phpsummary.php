<?php
    function displayDirStruct($dir) {
        $dirContents = array_diff(scandir($dir), array('..', '.', '.git', '.DS_Store'));
        echo '<ul>';
        foreach ($dirContents as $file) {
            if (is_dir($dir . '/' . $file)) {
                echo "<li>{$file}:</li>";
                displayDirStruct($dir . '/' . $file);
            } else {
                echo "<li>$file</li>";
            }
        }
        echo "</ul>";
    }
        
?>

<!doctype html>
<html lang="en">

<?php include 'includes/head.php' ?>

<body <?= isset($_COOKIE['darkmode']) && $_COOKIE['darkmode'] === 'true' ? 'data-bs-theme="dark"' : '' ?>>
    <?php include 'includes/menu.php' ?>

    <div class="container">
        <h1>PHP Summary</h1>
        <?php
        printf('<p>You are running PHP %s</p>', phpversion());
        ?>

        <div class="card">
            <div class="card-header">
                Loaded Extensions
            </div>
            <div class="card-body">
                <?php
                foreach (get_loaded_extensions() as $extension) {
                    echo "<span class='badge text-bg-info' style='margin: 2px;'>$extension</span>";
                }
                ?>
            </div>
        </div>

        <div class="card" style="margin-top: 10px;">
            <div class="card-header">
                Server Environment
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Key</th>
                            <th>Value</th>
                        <tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($_SERVER as $key => $value) {
                            echo "<tr><td>$key</td><td>$value</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card" style="margin-top: 10px;">
            <div class="card-header">
                Local Root
            </div>
            <div class="card-body">
                <?php displayDirStruct('.'); ?>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php' ?>
</body>

</html>