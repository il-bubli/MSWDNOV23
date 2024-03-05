<?php
$menuItems = [
    ['name' => 'Home', 'url' => '/index.php'],
    ['name' => 'About PHP', 'url' => '/aboutphp.php'],
    ['name' => 'About MariaDB', 'url' => '/aboutmariadb.php'],
    ['name' => 'PHP Info', 'url' => '/phpinfo.php'],
    ['name' => 'PHP Summary', 'url' => '/phpsummary.php'],
    ['name' => 'Mastodon Posts', 'url' => '/mastodon.php'],
];
?>

<div class="container">
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo01"
                aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
                <a class="navbar-brand" href="#">Learning PHP</a>
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php
                    $scriptName = $_SERVER['SCRIPT_NAME'];
                    foreach ($menuItems as $menuItem) {
                        $isActive = $menuItem['url'] === $scriptName ? 'active' : '';
                        echo <<<ITEM
                            <li class="nav-item">
                                <a class="nav-link $isActive"
                                    aria-current="page" href="{$menuItem['url']}">{$menuItem['name']}</a>
                            </li>
                        ITEM;
                    }
                    ?>
                </ul>
                <i class="bi bi-moon"></i>
                <button class="darkmode" title="darkmode switch" aria-hidden="true"></button>
            </div>
        </div>
    </nav>
    
</div>

<script type="text/javascript" src="js/darkmode.js"></script>