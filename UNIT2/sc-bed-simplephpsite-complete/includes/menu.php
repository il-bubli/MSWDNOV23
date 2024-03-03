<div class="container">
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
                <a class="navbar-brand" href="#">Learning PHP</a>
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?= $_SERVER['SCRIPT_NAME'] === '/index.php' ? 'active' : '' ?>" aria-current="page" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $_SERVER['SCRIPT_NAME'] === '/aboutphp.php' ? 'active' : '' ?>" href="aboutphp.php">About PHP</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $_SERVER['SCRIPT_NAME'] === '/aboutmariadb.php' ? 'active' : '' ?>" href="aboutmariadb.php">About MariaDB</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $_SERVER['SCRIPT_NAME'] === '/phpinfo.php' ? 'active' : '' ?>" href="phpinfo.php">PHP Info</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $_SERVER['SCRIPT_NAME'] === '/phpsummary.php' ? 'active' : '' ?>" href="phpsummary.php">PHP Summary</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>